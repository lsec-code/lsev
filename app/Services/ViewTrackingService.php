<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ViewTrackingService
{
    /**
     * Generate unique fingerprint from IP and User Agent
     */
    public function generateFingerprint(string $ip, string $userAgent): string
    {
        $fingerprint = hash('sha256', $ip . '|' . $userAgent . '|' . config('app.key'));
        return $fingerprint;
    }

    /**
     * Check if this is a unique view (not viewed in last 24 hours)
     */
    public function isUniqueView(int $videoId, string $fingerprint): bool
    {
        $cutoff = now()->subHours(24);
        
        $existingView = DB::table('video_views')
            ->where('video_id', $videoId)
            ->where('fingerprint', $fingerprint)
            ->where('viewed_at', '>=', $cutoff)
            ->first(); // Change to first() to inspect it

        if ($existingView) {
            \Log::info('Duplicate view detected', [
                'video_id' => $videoId,
                'fingerprint' => $fingerprint,
                'previous_view_at' => $existingView->viewed_at,
                'cutoff_time' => $cutoff->toDateTimeString()
            ]);
            return false;
        }

        \Log::info('Unique view check passed', [
            'video_id' => $videoId,
            'fingerprint' => $fingerprint,
            'cutoff_time' => $cutoff->toDateTimeString()
        ]);
            
        return true;
    }

    /**
     * Record a new view
     */
    public function recordView(int $videoId, string $fingerprint, string $ip, string $userAgent, ?int $userId = null): bool
    {
        // Check if unique
        if (!$this->isUniqueView($videoId, $fingerprint)) {
            return false;
        }

        // Check fraud detection
        if ($this->detectFraud($fingerprint)) {
            \Log::warning('Potential view fraud detected', [
                'fingerprint' => $fingerprint,
                'video_id' => $videoId
            ]);
            return false;
        }

        // Create view record
        DB::table('video_views')->insert([
            'video_id' => $videoId,
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'fingerprint' => $fingerprint,
            'watch_duration' => 0,
            'completed' => false,
            'viewed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Increment video views count
        DB::table('videos')
            ->where('id', $videoId)
            ->increment('views');

        // Calculate and add CPM earnings to video owner
        $this->addCpmEarnings($videoId);

        return true;
    }

    /**
     * Calculate CPM earnings and add to video owner's balance
     */
    protected function addCpmEarnings(int $videoId): void
    {
        // Get video owner
        $video = DB::table('videos')->where('id', $videoId)->first();
        if (!$video || !$video->user_id) {
            return;
        }

        // Check if Flat CPM is enabled
        $flatCpmEnabled = DB::table('site_settings')
            ->where('setting_key', 'cpm_flat_enabled')
            ->value('setting_value') === 'true';

        $earnings = 0;
        $cpmInfo = '';

        if ($flatCpmEnabled) {
            $flatRate = DB::table('site_settings')
                ->where('setting_key', 'cpm_rate')
                ->value('setting_value') ?? 0;
            
            // Calculate earning per view (Rate / 1000)
            $earnings = $flatRate / 1000;
            $cpmInfo = "Flat Rate: {$flatRate}";
        } else {
            // Get CPM Min/Max settings
            $cpmMin = DB::table('site_settings')
                ->where('setting_key', 'cpm_min_rate')
                ->value('setting_value') ?? 1;
                
            $cpmMax = DB::table('site_settings')
                ->where('setting_key', 'cpm_max_rate')
                ->value('setting_value') ?? 100;
    
            // Calculate random earnings within range
            $earnings = rand((int)$cpmMin, (int)$cpmMax);
            $cpmInfo = "Dynamic Range: {$cpmMin}-{$cpmMax}";
        }

        // Add earnings to user balance
        DB::table('users')
            ->where('id', $video->user_id)
            ->increment('balance', $earnings);
            
        // Record Daily Stats
        $today = now()->format('Y-m-d');
        
        // Use raw SQL for atomic update or insert to avoid race conditions
        $exists = DB::table('daily_stats')
            ->where('user_id', $video->user_id)
            ->where('date', $today)
            ->exists();
            
        if ($exists) {
            DB::table('daily_stats')
                ->where('user_id', $video->user_id)
                ->where('date', $today)
                ->increment('views', 1, ['earnings' => DB::raw("earnings + $earnings")]);
        } else {
            DB::table('daily_stats')->insert([
                'user_id' => $video->user_id,
                'date' => $today,
                'views' => 1,
                'earnings' => $earnings,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        \Log::info('CPM earnings added', [
            'video_id' => $videoId,
            'user_id' => $video->user_id,
            'earnings' => $earnings,
            'cpm_mode' => $flatCpmEnabled ? 'flat' : 'dynamic',
            'cpm_info' => $cpmInfo
        ]);
    }

    /**
     * Update watch duration for a view
     */
    public function updateWatchDuration(int $videoId, string $fingerprint, int $duration): void
    {
        $view = DB::table('video_views')
            ->where('video_id', $videoId)
            ->where('fingerprint', $fingerprint)
            ->where('viewed_at', '>=', now()->subHours(24))
            ->orderBy('viewed_at', 'desc')
            ->first();

        if ($view) {
            // Get video duration to check completion
            $video = DB::table('videos')->find($videoId);
            $completed = false;
            
            if ($video && $video->duration) {
                // Mark completed if watched >= 80% of video
                $completed = $duration >= ($video->duration * 0.8);
            }

            DB::table('video_views')
                ->where('id', $view->id)
                ->update([
                    'watch_duration' => $duration,
                    'completed' => $completed,
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Detect potential fraud (>10 views in 1 hour)
     */
    public function detectFraud(string $fingerprint): bool
    {
        $recentViews = DB::table('video_views')
            ->where('fingerprint', $fingerprint)
            ->where('viewed_at', '>=', now()->subHour())
            ->count();

        return $recentViews >= 10;
    }

    /**
     * Add artificial/boosted views (Viewer Booster)
     */
    public function addBoostedViews(int $videoId, int $count, int $boostId = null): void
    {
        // 1. Increment video view count
        DB::table('videos')->where('id', $videoId)->increment('views', $count);

        // 2. Calculate Earnings
        // Use the same logic as real views (addCpmEarnings) but looped or multiplied
        // To keep it efficient, we calculate total for the batch
        
        $flatCpmEnabled = DB::table('site_settings')
            ->where('setting_key', 'cpm_flat_enabled')
            ->value('setting_value') === 'true';

        $totalEarnings = 0;

        if ($flatCpmEnabled) {
            $flatRate = DB::table('site_settings')->where('setting_key', 'cpm_rate')->value('setting_value') ?? 0;
            $totalEarnings = ($flatRate / 1000) * $count;
        } else {
            // For dynamic, we simulate average or random range for the batch
            $cpmMin = DB::table('site_settings')->where('setting_key', 'cpm_min_rate')->value('setting_value') ?? 1;
            $cpmMax = DB::table('site_settings')->where('setting_key', 'cpm_max_rate')->value('setting_value') ?? 100;
            
            // To be statistically simpler, we take a random value for EACH view or use Average
            // Iterating $count times is safer for randomness distribution
            for ($i = 0; $i < $count; $i++) {
                $totalEarnings += rand((int)$cpmMin, (int)$cpmMax);
            }
        }

        // 3. Add to User Balance & Daily Stats
        $video = DB::table('videos')->where('id', $videoId)->first();
        
        if ($video && $video->user_id) {
            // 4. Inject Fake Active Viewers (Live Count)
            for ($i = 0; $i < $count; $i++) {
                $fakeSessionId = 'boost_' . uniqid() . '_' . rand(1000, 9999);
                try {
                    DB::table('active_viewers')->insert([
                        'video_id' => $videoId,
                        'user_id' => null, 
                        'session_id' => $fakeSessionId,
                        'last_heartbeat' => now()->addSeconds(rand(10, 50)),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } catch (\Exception $e) {}
            }

            $today = now()->format('Y-m-d');
            $exists = DB::table('daily_stats')
                ->where('user_id', $video->user_id)
                ->where('date', $today)
                ->exists();

            if ($exists) {
                DB::table('daily_stats')
                    ->where('user_id', $video->user_id)
                    ->where('date', $today)
                    ->increment('views', $count, ['earnings' => DB::raw("earnings + $totalEarnings")]);
            } else {
                DB::table('daily_stats')->insert([
                    'user_id' => $video->user_id,
                    'date' => $today,
                    'views' => $count,
                    'earnings' => $totalEarnings,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::table('users')->where('id', $video->user_id)->increment('balance', $totalEarnings);

            \Log::info('Boosted views added', [
                'video_id' => $videoId,
                'count' => $count,
                'total_earnings' => $totalEarnings
            ]);

            // 5. Track stats in ViewerBoost record
            if ($boostId) {
                DB::table('viewer_boosts')
                    ->where('id', $boostId)
                    ->increment('views_added', $count, [
                        'earnings_added' => DB::raw("earnings_added + $totalEarnings")
                    ]);
            }
        }
    }
}

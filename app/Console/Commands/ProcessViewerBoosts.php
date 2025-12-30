<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessViewerBoosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boost:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("----------------------------------------------------------------");
        $this->info("   AUTO BOOSTER RUNNING - " . now()->format('Y-m-d H:i:s'));
        $this->info("----------------------------------------------------------------");

        $activeBoosts = \App\Models\ViewerBoost::with(['video', 'user'])
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->get();

        if ($activeBoosts->isEmpty()) {
            $this->comment("   [ZZZ] Tidak ada booster aktif. Menunggu...");
            return;
        }

        $viewService = app(\App\Services\ViewTrackingService::class);
        $count = 0;

        foreach ($activeBoosts as $boost) {
            $viewsToAdd = $boost->views_per_minute;
            
            // Randomize if max speed is set
            if ($boost->max_views_per_minute && $boost->max_views_per_minute > $boost->views_per_minute) {
                $viewsToAdd = rand($boost->views_per_minute, $boost->max_views_per_minute);
            }

            $viewService->addBoostedViews($boost->video_id, $viewsToAdd, $boost->id);
            
            $user = $boost->user ? $boost->user->username : 'Unknown';
            $video = $boost->video ? \Illuminate\Support\Str::limit($boost->video->title, 20) : 'Video Deleted';

            $this->line("   [OK] User: <comment>{$user}</comment> | Video: <info>{$video}</info> | +{$viewsToAdd} Views");
            $count++;
        }
        
        // Mark expired
        \App\Models\ViewerBoost::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'completed']);
            
        $this->info("----------------------------------------------------------------");
        $this->info("   Total Processed: $count Video");
        $this->newLine();
    }
}

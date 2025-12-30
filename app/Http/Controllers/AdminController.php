<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Added File facade
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Video;
use App\Models\Withdrawal;
use App\Models\AdminActivityLog;
use App\Models\SecurityAlert;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => \App\Models\User::where('is_admin', 0)->count(),
            'online_users' => \App\Models\User::where('is_admin', 0)->where('last_activity_at', '>=', now()->subMinutes(5))->count(),
            'total_videos' => Video::whereHas('user', function($q) { $q->where('is_admin', 0); })->count(),
            'total_views' => Video::whereHas('user', function($q) { $q->where('is_admin', 0); })->sum('views'),
            'total_earnings' => \App\Models\User::where('is_admin', 0)->sum('balance') + 
                                Withdrawal::whereHas('user', function($q) { $q->where('is_admin', 0); })
                                          ->whereIn('status', ['pending', 'approved'])->sum('amount'),
            'banned_users' => \App\Models\User::where('is_suspended', true)->count(),
            'banned_ips' => \App\Models\IpBan::count(),
            'pending_withdrawals' => Withdrawal::whereHas('user', function($q) { $q->where('is_admin', 0); })->where('status', 'pending')->count(),
            'approved_withdrawals' => Withdrawal::whereHas('user', function($q) { $q->where('is_admin', 0); })->where('status', 'approved')->count(),
            'rejected_withdrawals' => Withdrawal::whereHas('user', function($q) { $q->where('is_admin', 0); })->where('status', 'rejected')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function bans() 
    {
        // Fetch ALL IP bans (including expired) for admin review
        $banned_ips = \App\Models\IpBan::whereNotNull('banned_at')
            ->latest('banned_at')
            ->paginate(20);

        $suspended_users = User::where('is_suspended', true)
            ->latest('suspended_at')
            ->paginate(20);

        return view('admin.bans.index', compact('banned_ips', 'suspended_users'));
    }

    public function allVideos()
    {
        $videos = Video::with(['user', 'folder'])
            ->latest('created_at')
            ->paginate(50);
        
        return view('admin.videos.index', compact('videos'));
    }

    public function earnings()
    {
        // Get users with balance > 0, sorted by balance desc
        $users = User::where('is_admin', 0)
            ->where('balance', '>', 0)
            ->withCount('videos')
            ->withSum('videos', 'views')
            ->orderBy('balance', 'desc')
            ->paginate(50);
        
        $stats = [
            'total_balance' => User::where('is_admin', 0)->sum('balance'),
            'total_withdrawn' => Withdrawal::whereIn('status', ['pending', 'approved'])->sum('amount'),
            'total_earnings' => User::where('is_admin', 0)->sum('balance') + 
                               Withdrawal::whereIn('status', ['pending', 'approved'])->sum('amount'),
            'users_with_balance' => User::where('is_admin', 0)->where('balance', '>', 0)->count(),
        ];
        
        return view('admin.earnings.index', compact('users', 'stats'));
    }

    public function bannedUsers()
    {
        $users = User::where('is_suspended', true)
            ->withCount('videos')
            ->latest('suspended_at')
            ->paginate(20);
        
        return view('admin.users.banned', compact('users'));
    }

    public function withdrawalsByStatus($status)
    {
        $withdrawals = Withdrawal::with('user')
            ->where('status', $status)
            ->latest('created_at')
            ->paginate(50);
        
        $stats = [
            'total_amount' => Withdrawal::where('status', $status)->sum('amount'),
            'count' => Withdrawal::where('status', $status)->count(),
        ];
        
        return view('admin.withdrawals.index', compact('withdrawals', 'stats', 'status'));
    }

    public function theme()
    {
        $settings = SiteSetting::all()->pluck('setting_value', 'setting_key');
        $themePresets = SiteSetting::getThemePresets();
        
        return view('admin.theme.index', compact('settings', 'themePresets'));
    }

    public function updateTheme(Request $request)
    {
        $themeFields = ['active_theme', 'primary_color', 'secondary_color', 'custom_css'];
        
        foreach ($themeFields as $field) {
            if ($request->has($field)) {
                SiteSetting::updateOrCreate(
                    ['setting_key' => $field],
                    ['setting_value' => $request->$field]
                );
            }
        }

        // Handle bulk CSS variables from presets
        if ($request->has('css_vars') && is_array($request->css_vars)) {
            foreach ($request->css_vars as $key => $value) {
                SiteSetting::updateOrCreate(
                    ['setting_key' => 'theme_var_' . $key],
                    ['setting_value' => $value]
                );
            }
        }
        
        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'theme_update',
            'description' => 'Memperbarui tema situs ke: ' . ($request->active_theme ?? 'custom'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Tema berhasil diperbarui!');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'type' => 'required|in:logo,favicon'
        ]);
        
        $type = $request->type;
        $path = $request->file('logo')->store('theme', 'public');
        
        SiteSetting::updateOrCreate(
            ['setting_key' => $type . '_url'],
            ['setting_value' => $path]
        );
        
        return back()->with('success', ucfirst($type) . ' berhasil diupload!');
    }

    public function systemHealth()
    {
        $health = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'memory' => [
                'used' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit'),
            ],
            'disk' => [
                'total' => disk_total_space(base_path()),
                'free' => disk_free_space(base_path()),
                'used' => disk_total_space(base_path()) - disk_free_space(base_path()),
            ],
        ];
        
        // Get recent error logs
        $logFile = storage_path('logs/laravel.log');
        $errorLogs = [];
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $errorLogs = array_slice(array_reverse($lines), 0, 50);
        }
        
        return view('admin.system.health', compact('health', 'errorLogs'));
    }

    public function backup()
    {
        try {
            $backupPath = storage_path('app/backups');
            
            // Create backup directory if not exists
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Get all backup files
            $backups = [];
            if (is_dir($backupPath)) {
                $files = scandir($backupPath);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                        $backups[] = [
                            'name' => $file,
                            'size' => filesize($backupPath . '/' . $file),
                            'date' => filemtime($backupPath . '/' . $file),
                        ];
                    }
                }
            }
            
            // Sort by date descending
            usort($backups, function($a, $b) {
                return $b['date'] - $a['date'];
            });
    
            // Get current backup command setting
            $mysqldump_command = SiteSetting::where('setting_key', 'mysqldump_command')->value('setting_value') ?? 'mysqldump';
            
            // dd('DEBUG: Logic finished, rendering view next...');
    
            return view('admin.backup.index', compact('backups', 'mysqldump_command'));
        } catch (\Exception $e) {
            dd('ERROR CAUGHT: ' . $e->getMessage() . ' on line ' . $e->getLine());
        }
    }

    public function updateBackupSettings(Request $request)
    {
        $request->validate([
            'mysqldump_command' => 'required|string',
        ]);

        SiteSetting::updateOrCreate(
            ['setting_key' => 'mysqldump_command'],
            ['setting_value' => $request->mysqldump_command]
        );

        return back()->with('success', 'Pengaturan backup berhasil diperbarui.');
    }

    public function createBackup()
    {
        try {
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Create backup directory if not exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Get mysqldump command from settings
            $mysqldump = SiteSetting::where('setting_key', 'mysqldump_command')->value('setting_value') ?? 'mysqldump';
            
            // Auto-detect Laragon path if user left placeholder
            if (str_contains($mysqldump, 'mysql-X.X')) {
                $laragonMysqlPath = 'C:\\laragon\\bin\\mysql';
                if (is_dir($laragonMysqlPath)) {
                    $versions = array_filter(scandir($laragonMysqlPath), function($item) use ($laragonMysqlPath) {
                        return is_dir($laragonMysqlPath . DIRECTORY_SEPARATOR . $item) && $item !== '.' && $item !== '..';
                    });
                    if (!empty($versions)) {
                        $latestVersion = end($versions);
                        $mysqldump = str_replace('mysql-X.X', $latestVersion, $mysqldump);
                        // Save the corrected version back to settings
                        SiteSetting::updateOrCreate(['setting_key' => 'mysqldump_command'], ['setting_value' => $mysqldump]);
                    }
                }
            }

            // Build the command
            // Note: We don't redirect 2>&1 to the file anymore to keep the SQL clean
            $executable = (str_contains($mysqldump, ' ') && !str_starts_with($mysqldump, '"')) 
                ? "\"$mysqldump\"" 
                : $mysqldump;

            $command = sprintf(
                '%s -h %s -u %s %s %s > %s 2> %s',
                $executable,
                $dbHost,
                $dbUser,
                $dbPass ? '-p' . escapeshellarg($dbPass) : '',
                $dbName,
                escapeshellarg($backupPath),
                escapeshellarg(storage_path('app/backups/backup_error.log'))
            );
            
            exec($command, $output, $returnVar);
            
            // Check if file exists and has content
            if ($returnVar === 0 && file_exists($backupPath) && filesize($backupPath) > 500) {
                AdminActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'backup_created',
                    'description' => 'Database backup created: ' . $filename,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
                
                // Cleanup error log if success
                if (file_exists(storage_path('app/backups/backup_error.log'))) {
                    unlink(storage_path('app/backups/backup_error.log'));
                }

                return back()->with('success', 'Backup berhasil dibuat: ' . $filename);
            } else {
                $errorMsg = "Gagal membuat backup.";
                if (file_exists(storage_path('app/backups/backup_error.log'))) {
                    $errorMsg .= " Error: " . file_get_contents(storage_path('app/backups/backup_error.log'));
                }
                
                // If it failed, delete the empty/partial file
                if (file_exists($backupPath)) {
                    unlink($backupPath);
                }

                return back()->with('error', $errorMsg . " (Return code: $returnVar)");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadBackup($file)
    {
        $path = storage_path('app/backups/' . $file);
        
        if (!file_exists($path)) {
            return back()->with('error', 'Backup file tidak ditemukan.');
        }
        
        return response()->download($path);
    }

    public function deleteBackup($file)
    {
        $path = storage_path('app/backups/' . $file);
        
        if (file_exists($path)) {
            unlink($path);
            
            AdminActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_deleted',
                'description' => 'Backup deleted: ' . $file,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return back()->with('success', 'Backup berhasil dihapus.');
        }
        
        return back()->with('error', 'Backup file tidak ditemukan.');
    }

    public function restoreFromBackup($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filepath)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        try {
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            // Derive mysql path from mysqldump setting
            $mysqldump = SiteSetting::where('setting_key', 'mysqldump_command')->value('setting_value') ?? 'mysqldump';
            // Replace 'mysqldump' with 'mysql' to get the restore command
            // Handle both .exe (Windows) and no extension (Linux)
            $mysql = str_replace('mysqldump', 'mysql', $mysqldump);
            
            $command = sprintf(
                '%s -h %s -u %s %s %s < %s 2>&1',
                $mysql,
                $dbHost,
                $dbUser,
                $dbPass ? '-p' . escapeshellarg($dbPass) : '',
                $dbName,
                escapeshellarg($filepath)
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                AdminActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'database_restored',
                    'description' => 'Database restored from backup: ' . $filename,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
                
                return redirect()->route('admin.dashboard')->with('success', 'Database berhasil di-restore dari backup: ' . $filename);
            } else {
                return back()->with('error', 'Restore gagal. Error: ' . implode(' ', $output));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat restore: ' . $e->getMessage());
        }
    }

    public function uploadAndRestore(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt,text|max:102400',
            'confirm_restore' => 'required|accepted',
        ]);

        try {
            $file = $request->file('sql_file');
            $tempPath = storage_path('app/temp_restore_' . time() . '.sql');
            $file->move(storage_path('app'), basename($tempPath));
            
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            
            // Derive mysql path from mysqldump setting
            $mysqldump = SiteSetting::where('setting_key', 'mysqldump_command')->value('setting_value') ?? 'mysqldump';
            $mysql = str_replace('mysqldump', 'mysql', $mysqldump);
            
            $command = sprintf(
                '%s -h %s -u %s %s %s < %s 2>&1',
                $mysql,
                $dbHost,
                $dbUser,
                $dbPass ? '-p' . escapeshellarg($dbPass) : '',
                $dbName,
                escapeshellarg($tempPath)
            );
            
            exec($command, $output, $returnVar);
            
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            if ($returnVar === 0) {
                AdminActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'database_restored_upload',
                    'description' => 'Database restored from uploaded file: ' . $file->getClientOriginalName(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
                
                return redirect()->route('admin.dashboard')->with('success', 'Database berhasil di-restore dari file upload!');
            } else {
                return back()->with('error', 'Restore gagal. Error: ' . implode(' ', $output));
            }
        } catch (\Exception $e) {
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
            return back()->with('error', 'Error saat restore: ' . $e->getMessage());
        }
    }

    public function badges()
    {
        $badges = \App\Models\Badge::withCount('users')->get();
        
        return view('admin.badges.index', compact('badges'));
    }

    public function seedBadges()
    {
        // Create default badges
        $defaultBadges = [
            ['name' => 'Newbie', 'description' => 'Upload video pertama', 'icon' => 'fa-star', 'color' => 'gray', 'requirement_type' => 'videos', 'requirement_value' => 1],
            ['name' => 'Content Creator', 'description' => 'Upload 10 video', 'icon' => 'fa-video', 'color' => 'blue', 'requirement_type' => 'videos', 'requirement_value' => 10],
            ['name' => 'Pro Creator', 'description' => 'Upload 50 video', 'icon' => 'fa-fire', 'color' => 'orange', 'requirement_type' => 'videos', 'requirement_value' => 50],
            ['name' => 'Viral Star', 'description' => '10,000 total views', 'icon' => 'fa-eye', 'color' => 'purple', 'requirement_type' => 'views', 'requirement_value' => 10000],
            ['name' => 'Millionaire', 'description' => '1,000,000 total views', 'icon' => 'fa-crown', 'color' => 'yellow', 'requirement_type' => 'views', 'requirement_value' => 1000000],
            ['name' => 'Money Maker', 'description' => 'Earn Rp 100,000', 'icon' => 'fa-dollar-sign', 'color' => 'green', 'requirement_type' => 'earnings', 'requirement_value' => 100000],
        ];
        
        foreach ($defaultBadges as $badge) {
            \App\Models\Badge::updateOrCreate(
                ['name' => $badge['name']],
                $badge
            );
        }
        
        // Auto-assign badges to users
        $this->autoAssignBadges();
        
        return back()->with('success', 'Default badges created and auto-assigned!');
    }

    private function autoAssignBadges()
    {
        $badges = \App\Models\Badge::all();
        $users = User::where('is_admin', 0)
            ->withCount('videos')
            ->withSum('videos', 'views')
            ->get();
        
        foreach ($users as $user) {
            foreach ($badges as $badge) {
                $qualified = false;
                
                switch ($badge->requirement_type) {
                    case 'videos':
                        $qualified = $user->videos_count >= $badge->requirement_value;
                        break;
                    case 'views':
                        $qualified = ($user->videos_sum_views ?? 0) >= $badge->requirement_value;
                        break;
                    case 'earnings':
                        $qualified = $user->balance >= $badge->requirement_value;
                        break;
                }
                
                if ($qualified) {
                    \App\Models\UserBadge::updateOrCreate([
                        'user_id' => $user->id,
                        'badge_id' => $badge->id,
                    ]);
                }
            }
        }
    }

    public function leaderboard()
    {
        $topEarners = User::where('is_admin', 0)
            ->where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->limit(10)
            ->get();
        
        $topUploaders = User::where('is_admin', 0)
            ->withCount('videos')
            ->orderBy('videos_count', 'desc')
            ->limit(10)
            ->get();
        
        $topViewed = User::where('is_admin', 0)
            ->withSum('videos', 'views')
            ->orderBy('videos_sum_views', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.leaderboard.index', compact('topEarners', 'topUploaders', 'topViewed'));
    }

    public function unbanIp($id)
    {
        $ban = \App\Models\IpBan::findOrFail($id);
        $ban->delete(); // Or set expires_at to now()

        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'unban_ip',
            'description' => "Unbanned IP: {$ban->ip_address}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'IP berhasil di-unban & cooldown di-reset.');
    }

    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'is_suspended' => false,
            'suspension_reason' => null,
            'suspended_at' => null
        ]);

        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'unban_user',
            'description' => "Unbanned User: {$user->username}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'User berhasil di-unban.');
    }

    public function settings()
    {
        $settings = SiteSetting::all()->pluck('setting_value', 'setting_key');
        
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except('_token', 'css_vars') as $key => $value) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }

        // Handle bulk CSS variables from presets (copied from updateTheme)
        if ($request->has('css_vars') && is_array($request->css_vars)) {
            foreach ($request->css_vars as $key => $value) {
                SiteSetting::updateOrCreate(
                    ['setting_key' => 'theme_var_' . $key],
                    ['setting_value' => $value]
                );
            }
        }

        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_update',
            'description' => "Memperbarui pengaturan situs.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function toggleSetting(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required'
        ]);

        SiteSetting::updateOrCreate(
            ['setting_key' => $request->key],
            ['setting_value' => $request->value]
        );

        return response()->json(['success' => true]);
    }

    public function withdrawals()
    {
        $withdrawals = Withdrawal::with('user')->latest()->paginate(20);
        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function updateWithdrawal($id, $action)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($action === 'approve') {
            $withdrawal->update(['status' => 'approved']);
            
            // Notify User
            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'withdrawal',
                'title' => 'Penarikan Disetujui ✅',
                'message' => 'Permintaan penarikan Anda sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah disetujui.',
                'url' => route('withdraw.index'),
            ]);

            AdminActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'withdrawal_approve',
                'description' => "Menyetujui penarikan ID #{$withdrawal->id} sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " untuk user {$withdrawal->user->username}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return back()->with('success', 'Penarikan disetujui!');
        } elseif ($action === 'reject') {
            // Refund balance
            $withdrawal->user->increment('balance', $withdrawal->amount);
            $withdrawal->update(['status' => 'rejected']);

            // Notify User
            Notification::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'withdrawal',
                'title' => 'Penarikan Ditolak ❌',
                'message' => 'Permintaan penarikan Anda sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' ditolak. Saldo telah dikembalikan.',
                'url' => route('withdraw.index'),
            ]);

            AdminActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'withdrawal_reject',
                'description' => "Menolak penarikan ID #{$withdrawal->id} sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " untuk user {$withdrawal->user->username}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return back()->with('success', 'Penarikan ditolak dan saldo dikembalikan!');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    public function statistics()
    {
        return view('admin.statistics');
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('q');
        $users = User::where('name', 'LIKE', "%$query%")
                    ->orWhere('username', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%")
                    ->limit(5)
                    ->get()
                    ->map(function ($user) use ($request) {
                        $user->avatar_url = $user->getAvatarUrl();
                        
                        if ($request->has('minimal')) {
                            return [
                                'id' => $user->id,
                                'username' => $user->username,
                                'email' => $user->email,
                                'avatar_url' => $user->avatar_url
                            ];
                        }
                        
                        // Calculated Stats (Heavy)
                        $user->total_videos = $user->videos()->count();
                        $user->total_views = $user->videos()->sum('views');
                        
                        // Active Viewers (Live)
                        $user->active_viewers = \DB::table('active_viewers')
                            ->join('videos', 'active_viewers.video_id', '=', 'videos.id')
                            ->where('videos.user_id', $user->id)
                            ->where('active_viewers.last_heartbeat', '>=', now()->subSeconds(30))
                            ->distinct('active_viewers.session_id')
                            ->count('active_viewers.session_id');

                        // Financial Stats
                        $today = now()->format('Y-m-d');
                        $weekStart = now()->startOfWeek()->format('Y-m-d');
                        $monthStart = now()->startOfMonth()->format('Y-m-d');
                        
                        $dailyStats = \App\Models\DailyStat::where('user_id', $user->id);
                        
                        $user->daily_earnings = (clone $dailyStats)->where('date', $today)->sum('earnings');
                        $user->week_earnings = (clone $dailyStats)->where('date', '>=', $weekStart)->sum('earnings');
                        $user->month_earnings = (clone $dailyStats)->where('date', '>=', $monthStart)->sum('earnings');
                        
                        // Lifetime: Max of (Sum Daily) or (Balance + Withdrawals)
                        $sumDaily = (clone $dailyStats)->sum('earnings');
                        $sumderived = $user->balance + $user->withdrawals()->where('status', 'approved')->sum('amount');
                        $user->lifetime_earnings = max($sumDaily, $sumderived);

                        return $user;
                    });

        return response()->json($users);
    }

    public function updateUserStats(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount_to_add' => 'required|numeric', // Can be negative
        ]);

        $user = User::findOrFail($request->user_id);
        
        $amount = $request->amount_to_add;

        if ($amount == 0) {
            return back()->with('error', 'Jumlah tidak boleh nol.');
        }

        // Update Balance
        $oldBalance = $user->balance;
        $user->balance += $amount;
        $user->save();

        // Sync with DailyStat (Current Date)
        $today = now()->format('Y-m-d');
        $stat = \App\Models\DailyStat::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['views' => 0, 'earnings' => 0]
        );
        
        $stat->increment('earnings', $amount);

        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_balance_update',
            'description' => "Updated user {$user->username}: Added IDR " . number_format($amount) . " to balance.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Saldo berhasil ditambahkan dan disinkronisasi!');
    }

    public function logs()
    {
        $logs = AdminActivityLog::with('user')->latest()->paginate(20);
        return view('admin.logs', compact('logs'));
    }

    public function securityAlerts()
    {
        $alerts = SecurityAlert::with('user')->latest()->paginate(20);
        return view('admin.security_alerts', compact('alerts'));
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        
        return redirect($notification->url ?? route('admin.dashboard'));
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    // User Management
    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:1'], // Relaxed validation as requested
            'security_code' => ['nullable', 'string', 'min:1'], // Relaxed validation
            'role' => ['required', 'in:user,admin'],
        ]);

        $user = \App\Models\User::create([
            'username' => $request->username,
            'name' => $request->username, // Default name to username
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'security_code' => $request->security_code ? \Illuminate\Support\Facades\Hash::make($request->security_code) : null,
            'is_admin' => $request->role === 'admin',
            'email_verified_at' => now(), // Auto verify
        ]);

        return redirect()->route('admin.dashboard')->with('success', "User {$user->username} berhasil dibuat!");
    }

    // Data Cleanup
    public function dataCleanup()
    {
        return view('admin.data.cleanup');
    }

    public function toggleChat(Request $request)
    {
        $status = $request->enabled ? 'true' : 'false';
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'global_chat_enabled'],
            ['setting_value' => $status]
        );

        return back()->with('success', 'Global chat status updated.');
    }

    public function toggleRemoteUpload(Request $request)
    {
        $status = $request->enabled ? 'true' : 'false';
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'remote_upload_enabled'],
            ['setting_value' => $status]
        );

        return back()->with('success', 'Fitur Remote Upload berhasil diperbarui.');
    }

    public function toggleCustomDomain(Request $request)
    {
        $status = $request->enabled ? 'true' : 'false';
        \App\Models\SiteSetting::updateOrCreate(
            ['setting_key' => 'custom_domain_enabled'],
            ['setting_value' => $status]
        );

        return back()->with('success', 'Fitur Domain Kustom berhasil diperbarui.');
    }

    public function clearChatHistory()
    {
        \App\Models\ChatMessage::truncate();
        
        \App\Models\AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'clear_chat_history',
            'description' => 'Cleared all global chat history',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Riwayat chat global berhasil dihapus.');
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_identifier' => ['required', 'string'],
            'confirm_text' => ['required', 'in:HAPUS'],
        ]);

        // Find by ID, Username, or Email
        $user = \App\Models\User::where('id', $request->user_identifier)
            ->orWhere('username', $request->user_identifier)
            ->orWhere('email', $request->user_identifier)
            ->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        if ($user->is_admin) {
             return back()->with('error', 'Tidak dapat menghapus akun Admin utama.');
        }

        // Manual Cleanup (if cascade isn't enough or for extra safety)
        $user->videos()->delete(); // Soft delete videos
        $user->withdrawals()->delete();
        $user->comments()->delete();
        $user->loginActivities()->delete();
        $user->delete();

        \App\Models\AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_user',
            'description' => "Deleted user: {$user->username} ({$user->email})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Akun {$user->username} berhasil dihapus permanen.");
    }

    public function resetDatabaseTotal(Request $request)
    {
        $request->validate([
            'admin_password' => ['required', 'string'],
            // 'confirm_text' => ['required', 'in:RESET DATABASE'], // Removed by user request
            'understand_risk' => ['required', 'accepted'],
        ]);

        // Verify admin password
        $admin = auth()->user();
        if (!$admin || !$admin->is_admin) {
            return back()->with('error', 'Unauthorized: Hanya admin yang dapat melakukan reset total.');
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->admin_password, $admin->password)) {
            return back()->with('error', 'Password admin salah! Reset dibatalkan.');
        }

        try {
            // Disable Foreign Key Checks to allow truncation
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Disable Foreign Key Checks to allow truncation
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Transaction removed because TRUNCATE automatically commits in MySQL

            // 1. DELETE ALL NON-ADMIN USERS
            $deletedCount = User::where('is_admin', 0)->delete();

            // 2. DELETE ALL DATA (even admin's data)
            \App\Models\Video::truncate();
            \App\Models\DailyStat::truncate();
            \DB::table('active_viewers')->truncate();
            \App\Models\Withdrawal::truncate();
            \App\Models\Folder::truncate();
            \App\Models\Comment::truncate();
            \DB::table('comment_likes')->truncate();
            \App\Models\Notification::truncate();
            \App\Models\ChatMessage::truncate();
            // \DB::table('messages')->truncate(); // Removed: table does not exist
            \DB::table('viewer_boosts')->truncate();
            \DB::table('user_badges')->truncate();
            \DB::table('login_activities')->truncate();

            // 3. RESET ADMIN STATS TO ZERO
            User::where('is_admin', 1)->update([
                'balance' => 0,
            ]);

            // 4. DELETE PHYSICAL FILES (Videos, Images, etc)
            $pathsToClean = [
                public_path('uploads/videos'),
                public_path('uploads/images'),
                public_path('uploads/thumbnails'),
                public_path('uploads/avatars'), // Optional: if you want to clear user avatars
            ];

            foreach ($pathsToClean as $path) {
                if (File::isDirectory($path)) {
                    // Get all directories and files inside
                    $files = File::allFiles($path);
                    $directories = File::directories($path);

                    foreach ($files as $file) {
                        // Skip .gitignore or keep-alive files if any
                        if ($file->getFilename() != '.gitignore') {
                            File::delete($file);
                        }
                    }

                    foreach ($directories as $directory) {
                        File::deleteDirectory($directory);
                    }
                }
            }

            // Re-enable Foreign Key Checks
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            \App\Models\AdminActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'reset_database_total',
                'description' => "RESET DATABASE TOTAL: Menghapus {$deletedCount} user non-admin, me-reset statistik, dan membersihkan file fisik (video/gambar).",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('admin.dashboard')->with('success', "✅ DATABASE & FILE FISIK DIRESET! Dihapus {$deletedCount} user & file terkait. Sistem bersih total.");

        } catch (\Exception $e) {
            // \DB::rollBack(); // Removed
            \Log::error('Reset Database Total Failed: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal mereset database: ' . $e->getMessage());
        }
    }

    // ADS Management
    public function adsManagement()
    {
        $ad_video = SiteSetting::where('setting_key', 'ad_script_video_watch')->value('setting_value');
        $ad_auth = SiteSetting::where('setting_key', 'ad_script_auth')->value('setting_value');
        $ad_user_videos = SiteSetting::where('setting_key', 'ad_script_user_videos')->value('setting_value');
        $ad_404 = SiteSetting::where('setting_key', 'ad_script_404')->value('setting_value');
        $ad_suspended = SiteSetting::where('setting_key', 'ad_script_suspended')->value('setting_value');
        $ad_security = SiteSetting::where('setting_key', 'ad_script_security')->value('setting_value');
        
        return view('admin.ads.index', compact('ad_video', 'ad_auth', 'ad_user_videos', 'ad_404', 'ad_suspended', 'ad_security'));
    }

    public function updateAds(Request $request)
    {
        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_video_watch'],
            ['setting_value' => $request->ad_script_video_watch]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_auth'],
            ['setting_value' => $request->ad_script_auth]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_user_videos'],
            ['setting_value' => $request->ad_script_user_videos]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_404'],
            ['setting_value' => $request->ad_script_404]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_suspended'],
            ['setting_value' => $request->ad_script_suspended]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_security'],
            ['setting_value' => $request->ad_script_security]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_auth'],
            ['setting_value' => $request->ad_script_auth]
        );

        SiteSetting::updateOrCreate(
            ['setting_key' => 'ad_script_user_videos'],
            ['setting_value' => $request->ad_script_user_videos]
        );

        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'ads_update',
            'description' => "Memperbarui Script Iklan",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.ads')->with('success', 'Pengaturan Iklan berhasil diperbarui!');
    }    

    // Captcha Settings
    public function captchaSettings()
    {
        $active_driver = \App\Models\SiteSetting::where('setting_key', 'captcha_driver')->value('setting_value') ?? 'none';
        
        $recaptcha_site = \App\Models\SiteSetting::where('setting_key', 'recaptcha_site_key')->value('setting_value');
        $recaptcha_secret = \App\Models\SiteSetting::where('setting_key', 'recaptcha_secret_key')->value('setting_value');
        
        $turnstile_site = \App\Models\SiteSetting::where('setting_key', 'turnstile_site_key')->value('setting_value');
        $turnstile_secret = \App\Models\SiteSetting::where('setting_key', 'turnstile_secret_key')->value('setting_value');

        return view('admin.captcha.index', compact(
            'active_driver', 
            'recaptcha_site', 'recaptcha_secret',
            'turnstile_site', 'turnstile_secret'
        ));
    }

    public function updateCaptcha(Request $request)
    {
        $request->validate([
            'captcha_driver' => ['required', 'in:none,google,cloudflare'],
            'recaptcha_site_key' => ['nullable', 'string'],
            'recaptcha_secret_key' => ['nullable', 'string'],
            'turnstile_site_key' => ['nullable', 'string'],
            'turnstile_secret_key' => ['nullable', 'string'],
        ]);

        \App\Models\SiteSetting::updateOrCreate(['setting_key' => 'captcha_driver'], ['setting_value' => $request->captcha_driver]);
        
        \App\Models\SiteSetting::updateOrCreate(['setting_key' => 'recaptcha_site_key'], ['setting_value' => $request->recaptcha_site_key]);
        \App\Models\SiteSetting::updateOrCreate(['setting_key' => 'recaptcha_secret_key'], ['setting_value' => $request->recaptcha_secret_key]);
        
        \App\Models\SiteSetting::updateOrCreate(['setting_key' => 'turnstile_site_key'], ['setting_value' => $request->turnstile_site_key]);
        \App\Models\SiteSetting::updateOrCreate(['setting_key' => 'turnstile_secret_key'], ['setting_value' => $request->turnstile_secret_key]);

        \App\Models\AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_captcha_settings',
            'description' => "Updated Captcha Driver to {$request->captcha_driver}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.captcha')->with('success', 'Pengaturan Captcha berhasil disimpan!');
    }

    // CPM & Financial Settings
    public function cpmSettings()
    {
        // Get settings from database
        $cpm_min = SiteSetting::where('setting_key', 'cpm_min_rate')->value('setting_value') ?? 1;
        $cpm_max = SiteSetting::where('setting_key', 'cpm_max_rate')->value('setting_value') ?? 100;
        $cpm_flat_enabled = SiteSetting::where('setting_key', 'cpm_flat_enabled')->value('setting_value') === 'true';
        $cpm_rate = SiteSetting::where('setting_key', 'cpm_rate')->value('setting_value') ?? 0;
        $min_withdrawal = SiteSetting::where('setting_key', 'min_withdrawal')->value('setting_value') ?? 250000;
        
        // Auto-mark expired boosts
        \App\Models\ViewerBoost::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'completed']);

        // Auto-cleanup: Keep only the latest 100 records (10 pages)
        // This ensures the history doesn't grow infinitely
        if (\App\Models\ViewerBoost::count() > 100) {
            $keepIds = \App\Models\ViewerBoost::latest()->take(100)->pluck('id');
            \App\Models\ViewerBoost::whereNotIn('id', $keepIds)->delete();
        }
            
        $boosts = \App\Models\ViewerBoost::with(['user', 'video'])->latest()->paginate(10);
        
        return view('admin.cpm.index', compact('cpm_min', 'cpm_max', 'cpm_flat_enabled', 'cpm_rate', 'min_withdrawal', 'boosts'));
    }

    public function updateCpm(Request $request)
    {
        $request->validate([
            'cpm_min_rate' => 'sometimes|nullable|numeric|min:1',
            'cpm_max_rate' => 'sometimes|nullable|numeric|gt:cpm_min_rate',
            'cpm_rate' => 'sometimes|nullable|numeric|min:0',
            'min_withdrawal' => 'required|numeric|min:0',
        ]);

        if ($request->has('cpm_min_rate')) {
            SiteSetting::updateOrCreate(['setting_key' => 'cpm_min_rate'], ['setting_value' => $request->cpm_min_rate]);
        }
        
        if ($request->has('cpm_max_rate')) {
            SiteSetting::updateOrCreate(['setting_key' => 'cpm_max_rate'], ['setting_value' => $request->cpm_max_rate]);
        }
        
        // Handle Flat CPM
        $flatEnabled = $request->cpm_flat_enabled === 'true' ? 'true' : 'false';
        SiteSetting::updateOrCreate(['setting_key' => 'cpm_flat_enabled'], ['setting_value' => $flatEnabled]);
        
        if ($request->has('cpm_rate')) {
            SiteSetting::updateOrCreate(['setting_key' => 'cpm_rate'], ['setting_value' => $request->cpm_rate]);
        }

        // Handle Min Withdrawal
        SiteSetting::updateOrCreate(['setting_key' => 'min_withdrawal'], ['setting_value' => $request->min_withdrawal]);

        AdminActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_cpm_settings',
            'description' => "Updated Financial Settings",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Pengaturan Keuangan & CPM berhasil diperbarui!');
    }
    
    // Announcement Management (Simple Editor with Color Control)
    public function announcements()
    {
        $announcement = \App\Models\Announcement::first();
        
        // Create default if doesn't exist
        if (!$announcement) {
            $announcement = \App\Models\Announcement::create([
                'content' => 'Website ini masih dalam tahap beta testing. Jika ada kendala silahkan hubungi admin.',
                'color' => '#00ff00',
                'active' => true
            ]);
        }
        
        return view('admin.announcements.index', compact('announcement'));
    }
    
    public function updateAnnouncement(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'color' => 'required|string'
        ]);
        
        $announcement = \App\Models\Announcement::first();
        
        if ($announcement) {
            $announcement->update([
                'content' => $request->content,
                'color' => $request->color
            ]);
        } else {
            \App\Models\Announcement::create([
                'content' => $request->content,
                'color' => $request->color,
                'active' => true
            ]);
        }
        
        // Clear cache so changes appear immediately
        \Cache::forget('active_announcement');
        
        return redirect()->route('admin.announcements')->with('success', 'Pengumuman berhasil diupdate!');
    }
    
    public function deleteAnnouncement($id)
    {
        \App\Models\Announcement::findOrFail($id)->delete();
        return redirect()->route('admin.announcements')->with('success', 'Pengumuman berhasil dihapus!');
    }
    /**
     * List all users
     */
    public function userList(Request $request)
    {
        $query = User::where('is_admin', 0)
            ->withCount('videos')
            ->withSum('videos', 'views');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('created_at')->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * List online users (active in last 5 mins)
     */
    public function onlineUsers(Request $request)
    {
        $query = User::where('is_admin', 0)
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->withCount('videos')
            ->withSum('videos', 'views');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('last_activity_at', 'desc')->paginate(20);
        
        $isOnlineView = true;
        return view('admin.users.index', compact('users', 'isOnlineView'));
    }

    /**
     * Inspect user files (folders + videos)
     */
    public function userFiles($id, $folderId = null)
    {
        $user = User::findOrFail($id);
        
        if ($folderId) {
            // Inside a folder: Show videos in this folder, no subfolders
            $folders = collect([]); 
            $videos = Video::where('user_id', $id)->where('folder_id', $folderId)->get();
            $currentFolder = \App\Models\Folder::find($folderId);
        } else {
            // Root: Show all folders and videos without folder
            $folders = \App\Models\Folder::where('user_id', $id)->get();
            $videos = Video::where('user_id', $id)->whereNull('folder_id')->get();
            $currentFolder = null;
        }
        
        // Breadcrumb logic (Flat)
        $breadcrumbs = [];
        if ($currentFolder) {
            $breadcrumbs[] = $currentFolder;
        }

        return view('admin.users.files', compact('user', 'folders', 'videos', 'currentFolder', 'breadcrumbs'));
    }
    /**
     * Show Security Verification Form
     */
    public function verifySecurity()
    {
        return view('admin.auth.security');
    }

    /**
     * Process Security Verification
     */
    public function checkSecurity(Request $request)
    {
        $request->validate([
            'security_code' => 'required|string'
        ]);

        // Fetch code from settings or default
        $systemCode = \App\Models\SiteSetting::where('setting_key', 'admin_security_code')->value('setting_value');
        
        // Fallback or Env (Ideally should be set in DB)
        if (!$systemCode) {
            $systemCode = env('ADMIN_SECURITY_CODE', 'admin123'); 
        }

        if ($request->security_code === $systemCode) {
            session(['admin_verified' => true]);
            
            // Log successful verification
            \App\Models\AdminActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'security_verified',
                'description' => 'Admin security code verified for session.',
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('admin.dashboard');
        }

        // Log failed attempt
        \App\Models\SecurityAlert::create([
            'user_id' => auth()->id(),
            'alert_type' => 'FAILED_ADMIN_LOGIN',
            'severity' => 'critical',
            'pattern_detected' => 'Failed Admin Security Code attempt.',
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip()
        ]);

        return back()->withErrors(['security_code' => 'Kode Keamanan Salah! Akses ditolak.']);
    }

    // =========================================================================
    // Viewer Booster (Fake Views)
    // =========================================================================

    public function boosts()
    {
        $boosts = \App\Models\ViewerBoost::with(['user', 'video'])->latest()->paginate(10);
        return view('admin.boosts.index', compact('boosts'));
    }

    public function createBoost()
    {
        return view('admin.boosts.create');
    }

    public function storeBoost(Request $request)
    {
        // Support both single string (legacy) and array (new multi-select)
        $inputs = $request->validate([
            'emails' => 'nullable|array',
            'emails.*' => 'string',
            'email' => 'nullable|string', 
            'views_per_minute' => 'required|integer|min:1|max:1000', // Acts as MIN Speed
            'views_per_minute_max' => 'nullable|integer|min:1|max:1000|gte:views_per_minute', // OPTIONAL MAX Speed
            'duration_minutes' => 'required|integer|min:1|max:1440',
        ]);

        $targets = [];
        if ($request->has('emails') && is_array($request->emails)) {
            $targets = $request->emails;
        } elseif ($request->has('email')) {
            $targets = [$request->email];
        }

        if (empty($targets)) {
            return back()->with('error', 'Harap pilih minimal satu user target.');
        }

        $successCount = 0;
        $videoCount = 0;
        $processedUsers = [];

        foreach ($targets as $targetInput) {
            // Find User
            $user = \App\Models\User::where('email', $targetInput)
                        ->orWhere('username', $targetInput)
                        ->first();

            if (!$user) continue;

            // Get 5 Random Videos
            $videos = \App\Models\Video::where('user_id', $user->id)
                        ->inRandomOrder()
                        ->take(5)
                        ->get();

            if ($videos->isNotEmpty()) {
                foreach ($videos as $video) {
                    \App\Models\ViewerBoost::create([
                        'user_id' => $user->id,
                        'video_id' => $video->id,
                        'views_per_minute' => (int) $request->views_per_minute,
                        'max_views_per_minute' => $request->views_per_minute_max ? (int) $request->views_per_minute_max : null,
                        'duration_minutes' => (int) $request->duration_minutes,
                        'started_at' => now(),
                        'expires_at' => now()->addMinutes((int) $request->duration_minutes),
                        'status' => 'active'
                    ]);
                    $videoCount++;
                }
                $successCount++;
                $processedUsers[] = $user->username;
            }
        }

        if ($successCount === 0) {
            return back()->with('error', 'Gagal memproses booster. Pastikan user valid dan memiliki video.');
        }

        $userList = implode(', ', array_slice($processedUsers, 0, 3));
        if (count($processedUsers) > 3) $userList .= '...';

        return redirect()->route('admin.cpm')->with('success', "Sukses! $videoCount video dari $successCount user ($userList) sedang diboost.");
    }

    public function endBoost($id)
    {
        $boost = \App\Models\ViewerBoost::findOrFail($id);
        $boost->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Booster berhasil dihentikan.');
    }

    // Browser-Based Worker Endpoint
    public function processBoosts()
    {
        $activeBoosts = \App\Models\ViewerBoost::where('status', 'active')
            ->where('expires_at', '>', now())
            ->get();

        $count = 0;
        $viewService = app(\App\Services\ViewTrackingService::class);

        foreach ($activeBoosts as $boost) {
            $viewsToAdd = $boost->views_per_minute;
            
            // Randomize if max speed is set
            if ($boost->max_views_per_minute && $boost->max_views_per_minute > $boost->views_per_minute) {
                $viewsToAdd = rand($boost->views_per_minute, $boost->max_views_per_minute);
            }

            $viewService->addBoostedViews($boost->video_id, $viewsToAdd);
            $count++;
        }
        
        // Mark expired
        \App\Models\ViewerBoost::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'completed']);

        return response()->json(['status' => 'success', 'processed' => $count]);
    }

    public function verifyAdminPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string',
            ]);

            if (!auth()->check()) {
                return response()->json(['valid' => false, 'message' => 'Sesi admin berakhir. Silakan login ulang.']);
            }

            if (Hash::check($request->password, auth()->user()->password)) {
                return response()->json(['valid' => true]);
            }

            return response()->json(['valid' => false, 'message' => 'Password salah!']);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false, 
                'message' => 'SYSTEM ERROR: ' . $e->getMessage() . ' line ' . $e->getLine()
            ], 200); // Return 200 so JS can parse the error message easily
        }
    }
}

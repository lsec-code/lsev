<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CleanupInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users who never logged in within 24 hours of registration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting cleanup of inactive users...');
        
        // Find users who:
        // 1. Never logged in (has_logged_in = false)
        // 2. Registered more than 7 days ago
        // 3. Have no login activity records
        // 4. Have 0 balance
        // 5. Have no videos
        // 6. Are not admins
        $inactiveUsers = User::where('has_logged_in', false)
            ->where('is_admin', false)
            ->where('balance', 0)
            ->where('created_at', '<', now()->subDays(7))
            ->whereDoesntHave('loginActivities')
            ->whereDoesntHave('videos')
            ->get();
        
        $count = $inactiveUsers->count();
        
        if ($count === 0) {
            $this->info('✅ No inactive users found.');
            return 0;
        }
        
        $this->info("📊 Found {$count} inactive user(s) to delete:");
        
        foreach ($inactiveUsers as $user) {
            $this->line("   - {$user->name} ({$user->email}) - Registered: {$user->created_at->diffForHumans()}");
        }
        
        // Delete inactive users using the same criteria
        $deleted = User::where('has_logged_in', false)
            ->where('is_admin', false)
            ->where('balance', 0)
            ->where('created_at', '<', now()->subDays(7))
            ->whereDoesntHave('loginActivities')
            ->whereDoesntHave('videos')
            ->delete();
        
        $this->info("✅ Successfully deleted {$deleted} inactive user(s).");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        return 0;
    }
}

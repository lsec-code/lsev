<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\LoginActivity;

class CleanupIdleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup-idle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup sessions that have been idle for more than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thirtyMinutesAgo = now()->subMinutes(30)->timestamp;
        
        // Get idle sessions
        $idleSessions = DB::table('sessions')
            ->where('last_activity', '<', $thirtyMinutesAgo)
            ->get();
        
        foreach ($idleSessions as $session) {
            // Create logout activity record
            if ($session->user_id) {
                $activity = LoginActivity::where('user_id', $session->user_id)
                    ->where('session_id', $session->id)
                    ->first();
                    
                if ($activity && !$activity->logout_at) {
                    $activity->update(['logout_at' => now()]);
                }
            }
        }
        
        // Delete idle sessions
        $deletedCount = DB::table('sessions')
            ->where('last_activity', '<', $thirtyMinutesAgo)
            ->delete();
        
        $this->info("Cleaned up {$deletedCount} idle sessions.");
        
        return 0;
    }
}

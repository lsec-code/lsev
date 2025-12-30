<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove users who have not verified their email within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();
            
        $this->info("Successfully removed {$count} unverified users.");
    }
}

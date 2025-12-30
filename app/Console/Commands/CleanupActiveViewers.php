<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupActiveViewers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-active-viewers';

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
        $deleted = \DB::table('active_viewers')
            ->where('last_heartbeat', '<', now()->subMinute())
            ->delete();

        $this->info("Cleaned up {$deleted} stale viewer records.");
        
        return Command::SUCCESS;
    }
}

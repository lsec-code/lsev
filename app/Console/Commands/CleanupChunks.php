<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:chunks';
    protected $description = 'Clean up incomplete upload chunks older than 24 hours';

    public function handle()
    {
        $chunkPath = storage_path('app/chunks');
        
        if (!is_dir($chunkPath)) {
            $this->info('No chunks directory found.');
            return;
        }

        $directories = \Illuminate\Support\Facades\File::directories($chunkPath);
        $count = 0;
        $now = now();

        foreach ($directories as $dir) {
            $lastModified = \Illuminate\Support\Facades\File::lastModified($dir);
            $dirDate = \Carbon\Carbon::createFromTimestamp($lastModified);

            // Delete if older than 24 hours
            if ($dirDate->diffInHours($now) > 24) {
                 \Illuminate\Support\Facades\File::deleteDirectory($dir);
                 $count++;
            }
        }

        $this->info("Cleaned up $count stale chunk directories.");
    }
}

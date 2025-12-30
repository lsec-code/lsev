<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;

class UpdateVideoFileSizes extends Command
{
    protected $signature = 'videos:update-sizes';
    protected $description = 'Update file sizes for existing videos';

    public function handle()
    {
        $videos = Video::whereNull('file_size')->get();
        $updated = 0;
        $failed = 0;

        foreach ($videos as $video) {
            $path = public_path('uploads/videos/' . $video->filename);
            
            if (file_exists($path)) {
                $video->file_size = filesize($path);
                $video->save();
                $this->info("Updated: {$video->title} - " . number_format($video->file_size / 1048576, 2) . " MB");
                $updated++;
            } else {
                $this->warn("File not found: {$video->filename}");
                $failed++;
            }
        }

        $this->info("\nCompleted! Updated: {$updated}, Failed: {$failed}");
        return 0;
    }
}

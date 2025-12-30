<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Schedule idle session cleanup every minute
Schedule::command('sessions:cleanup-idle')->everyMinute();

// Schedule inactive user cleanup every hour
Schedule::command('users:cleanup-inactive')->hourly();

// Schedule active viewers cleanup every minute
Schedule::command('app:cleanup-active-viewers')->everyMinute();

// Viewer Booster - Process Fake Views
// Schedule::command('boost:process')->everyMinute();

// Clean up stale upload chunks daily
Schedule::command('cleanup:chunks')->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

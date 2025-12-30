<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\AdminActivityLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Login Event
        Event::listen(Login::class, function ($event) {
            if ($event->user) {
                AdminActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'login',
                    'description' => 'User logged in',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        // Logout Event
        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                AdminActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'logout',
                    'description' => 'User logged out',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        // Register Event
        Event::listen(Registered::class, function ($event) {
            if ($event->user) {
                AdminActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'register',
                    'description' => 'User registered',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
        
        // Password Reset Event
        Event::listen(PasswordReset::class, function ($event) {
            if ($event->user) {
                AdminActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'password_reset',
                    'description' => 'User reset password',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
    }
}

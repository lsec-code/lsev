<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\DetectMaliciousActivity::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackLoginActivity::class,
            \App\Http\Middleware\EnforceSessionLimit::class,
            \App\Http\Middleware\CheckSuspended::class,
            \App\Http\Middleware\UpdateLastActivity::class,
            \App\Http\Middleware\HandleCustomDomain::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerifiedCustom::class,
            'admin.secure' => \App\Http\Middleware\ProtectAdminAccess::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

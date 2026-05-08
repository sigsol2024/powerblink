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
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '*'));
        $middleware->web(append: [
            \App\Http\Middleware\TrackPublicTraffic::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'admin.audit' => \App\Http\Middleware\TrackAdminAuditTrail::class,
            'login.otp.pending' => \App\Http\Middleware\EnsureLoginOtpPending::class,
            'pending.registration' => \App\Http\Middleware\EnsurePendingRegistration::class,
            'vendor.idle' => \App\Http\Middleware\VendorIdleTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

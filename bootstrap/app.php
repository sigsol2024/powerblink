<?php

use App\Http\Middleware\EnsureLoginOtpPending;
use App\Http\Middleware\EnsurePendingRegistration;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\TrackAdminAuditTrail;
use App\Http\Middleware\TrackPublicTraffic;
use App\Http\Middleware\VendorIdleTimeout;
use Illuminate\Console\Scheduling\Schedule;
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
        $middleware->validateCsrfTokens(except: [
            'payment/paystack/webhook',
        ]);
        $middleware->web(append: [
            TrackPublicTraffic::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'admin.audit' => TrackAdminAuditTrail::class,
            'login.otp.pending' => EnsureLoginOtpPending::class,
            'pending.registration' => EnsurePendingRegistration::class,
            'vendor.idle' => VendorIdleTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        //
    })
    ->create();

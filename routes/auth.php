<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginOtpChallengeController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterOtpController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UnifiedAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'throttle:30,1'])->group(function () {
    Route::get('/login/otp-challenge', [LoginOtpChallengeController::class, 'create'])
        ->middleware('login.otp.pending')
        ->name('login.otp.show');
    Route::post('/login/otp-challenge', [LoginOtpChallengeController::class, 'store'])
        ->middleware(['login.otp.pending', 'throttle:12,1'])
        ->name('login.otp.store');
    Route::post('/login/otp-challenge/resend', [LoginOtpChallengeController::class, 'resend'])
        ->middleware(['login.otp.pending', 'throttle:6,1'])
        ->name('login.otp.resend');
    Route::post('/login/otp-challenge/cancel', [LoginOtpChallengeController::class, 'cancel'])
        ->middleware('login.otp.pending')
        ->name('login.otp.cancel');
});

Route::middleware('guest')->group(function () {
    Route::get('signup', [UnifiedAuthController::class, 'show'])->name('register');
    Route::get('login', [UnifiedAuthController::class, 'show'])->name('login');

    Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->middleware('throttle:20,1')
        ->name('auth.google.redirect');
    Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->middleware('throttle:20,1')
        ->name('auth.google.callback');

    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('register/verify', [RegisterOtpController::class, 'create'])
        ->middleware('pending.registration')
        ->name('register.verify.show');
    Route::post('register/verify', [RegisterOtpController::class, 'store'])
        ->middleware(['pending.registration', 'throttle:12,1'])
        ->name('register.verify.store');
    Route::post('register/verify/resend', [RegisterOtpController::class, 'resend'])
        ->middleware(['pending.registration', 'throttle:6,1'])
        ->name('register.verify.resend');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

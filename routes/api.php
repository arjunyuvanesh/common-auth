<?php

use Illuminate\Support\Facades\Route;
use Arjunyuvanesh\CommonAuth\Http\Controllers\LoginController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\RegisterController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\ForgotPasswordController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\ResetPasswordController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\VerificationController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\ChangePasswordController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\ProfileController;

// Dynamically fetch the route prefix from the host application's configuration
$prefix = config('common-auth.route_prefix', 'common-auth');

Route::prefix('api/' . $prefix)->middleware('api')->group(function () {
    
    // Guest API Routes (Mobile Apps & React)
    Route::middleware(['guest', 'throttle:common-auth'])->group(function () {
        Route::post('/login', [LoginController::class, 'login'])->name('api.common-auth.login');
        Route::post('/register', [RegisterController::class, 'register'])->name('api.common-auth.register');
        
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('api.common-auth.password.email');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('api.common-auth.password.update');
    });

    // Authenticated API Routes (Secured by Laravel Sanctum for Tokens)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [LoginController::class, 'me'])->name('api.common-auth.me');
        Route::post('/logout', [LoginController::class, 'logout'])->name('api.common-auth.logout');
        
        // Account Management
        Route::put('/profile', [ProfileController::class, 'update'])->name('api.common-auth.profile.update');
        Route::put('/password', [ChangePasswordController::class, 'update'])->name('api.common-auth.password.change');
        Route::delete('/account', [ProfileController::class, 'destroy'])->name('api.common-auth.account.delete');
        
        // Dual Email Verification Routes (OTP and Resend)
        Route::post('/email/verify-otp', [VerificationController::class, 'verifyOtp'])->name('api.common-auth.verification.verify-otp');
        Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware(['throttle:common-auth'])->name('api.common-auth.verification.send');
    });
    
    // Magic Link Verification Route (must be signed by Laravel)
    // Even for APIs, magic links are clicked in an email browser, but we provide it here for consistency
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyLink'])
        ->middleware(['signed', 'throttle:common-auth'])
        ->name('api.common-auth.verification.verify');
    
});

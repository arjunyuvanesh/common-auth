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

Route::prefix($prefix)->middleware('web')->group(function () {
    
    // Guest Routes (Only accessible if NOT logged in)
    // SECURITY: We add 'throttle:common-auth' to dynamically prevent brute-force attacks based on the config file
    Route::middleware(['guest', 'throttle:common-auth'])->group(function () {
        Route::post('/login', [LoginController::class, 'login'])->name('common-auth.login');
        Route::post('/register', [RegisterController::class, 'register'])->name('common-auth.register');
        
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('common-auth.password.email');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('common-auth.password.update');
    });

    // Authenticated Routes (Only accessible if logged in)
    Route::middleware('auth')->group(function () {
        Route::get('/me', [LoginController::class, 'me'])->name('common-auth.me');
        Route::post('/logout', [LoginController::class, 'logout'])->name('common-auth.logout');
        
        // Account Management
        Route::put('/profile', [ProfileController::class, 'update'])->name('common-auth.profile.update');
        Route::put('/password', [ChangePasswordController::class, 'update'])->name('common-auth.password.change');
        Route::delete('/account', [ProfileController::class, 'destroy'])->name('common-auth.account.delete');
        
        // Dual Email Verification Routes (OTP and Resend)
        Route::post('/email/verify-otp', [VerificationController::class, 'verifyOtp'])->name('common-auth.verification.verify-otp');
        Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware(['throttle:common-auth'])->name('common-auth.verification.send');
    });
    
    // Magic Link Verification Route (must be signed by Laravel)
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyLink'])
        ->middleware(['signed', 'throttle:common-auth'])
        ->name('common-auth.verification.verify');
    
});

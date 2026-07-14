<?php

use Illuminate\Support\Facades\Route;
use Arjunyuvanesh\CommonAuth\Http\Controllers\LoginController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\RegisterController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\ForgotPasswordController;
use Arjunyuvanesh\CommonAuth\Http\Controllers\ResetPasswordController;

// Dynamically fetch the route prefix from the host application's configuration
$prefix = config('common-auth.route_prefix', 'common-auth');

Route::prefix($prefix)->middleware('web')->group(function () {
    
    // Guest Routes (Only accessible if NOT logged in)
    Route::middleware('guest')->group(function () {
        Route::post('/login', [LoginController::class, 'login'])->name('common-auth.login');
        Route::post('/register', [RegisterController::class, 'register'])->name('common-auth.register');
        
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('common-auth.password.email');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('common-auth.password.update');
    });

    // Authenticated Routes (Only accessible if logged in)
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('common-auth.logout');
    });
    
});

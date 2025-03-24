<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('email-verification/{key}', [AuthController::class, 'email_verification']);
Route::get('sso-callback', [AuthController::class, 'sso_auth']);

Route::middleware('guest')->group(function () {
    Route::get('auth/login', [AuthController::class, 'index'])->name('login');
    Route::post('auth/login', [AuthController::class, 'validate']);
    // Route::get('forgot-password', [AuthController::class, 'forgot'])->name('auth.forgot');
    // Route::post('forgot-password', [AuthController::class, 'forgot_save']);
    // Route::get('reset-password/{key}', [AuthController::class, 'reset_password'])->name('password.reset');
    // Route::post('reset-password', [AuthController::class, 'reset_password_save']);
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

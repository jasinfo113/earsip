<?php

use App\Http\Controllers\Account\AccountController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('/profile', function () {
            return view('account.view');
        })->name('account.profile');
        Route::post('/update', [AccountController::class, 'update'])->name('account.update');
        Route::post('/password', [AccountController::class, 'password'])->name('account.password');
        Route::post('/deactivate', [AccountController::class, 'deactivate'])->name('account.deactivate');
    });
});

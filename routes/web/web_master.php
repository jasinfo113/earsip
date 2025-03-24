<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Master\CategoriesController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('master')->group(function () {
        Route::get('/categories', function () {
            return view('master.categoris.view');
        })->name('master.categories');
        Route::post('/categori/data', [CategoriesController::class, 'categori_data'])->name('categori.data');
        // Route::post('/update', [AccountController::class, 'update'])->name('account.update');
        // Route::post('/password', [AccountController::class, 'password'])->name('account.password');
        // Route::post('/deactivate', [AccountController::class, 'deactivate'])->name('account.deactivate');
    });
});

<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('valid_access')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/pegawai/data', [DashboardController::class, 'pegawai_data'])->name('pegawai.data');
        Route::get('/pegawai/export', [DashboardController::class, 'pegawai_export'])->name('pegawai.export');
        Route::post('/notif/data', [DashboardController::class, 'notif_data'])->name('notif.data');
        Route::post('/notif/send', [DashboardController::class, 'notif_send'])->name('notif.send');
        Route::post('/fcm/data', [DashboardController::class, 'notif_data'])->name('notif.data');
        Route::post('/fcm/send', [DashboardController::class, 'notif_send'])->name('notif.send');
        Route::get('/notif/data', [DashboardController::class, 'notif_data'])->name('notif.data');
        Route::get('/fcm/data', [DashboardController::class, 'notif_data'])->name('notif.data');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Main\PegawaiController;
use App\Http\Controllers\Main\TicketController;


Route::middleware('valid_access')->group(function () {
    Route::prefix('main')->group(function () {
        Route::prefix('/pegawai')->group(function () {
            Route::get('/', function () {
                return view('main.pegawai.view');
            })->name('pegawai');
            Route::post('/data', [PegawaiController::class, 'data'])->name('pegawai.data');
            Route::post('/detail', [PegawaiController::class, 'detail'])->name('pegawai.detail');
            Route::post('/activity', [PegawaiController::class, 'activity'])->name('pegawai.activity');
        });

        Route::prefix('/ticket')->group(function () {
            Route::get('/', function () {
                return view('main.ticket.view');
            })->name('ticket');
            Route::get('/data', [TicketController::class, 'data'])->name('ticket.data');
            Route::post('/data', [TicketController::class, 'data'])->name('ticket.data');
            Route::post('/form/{id?}', [TicketController::class, 'form'])->name('ticket.form');
            Route::post('/save', [TicketController::class, 'save'])->name('ticket.save');
            Route::post('/del', [TicketController::class, 'del'])->name('ticket.del');
            Route::post('/update', [TicketController::class, 'update'])->name('ticket.update');
            Route::post('/close', [TicketController::class, 'close'])->name('ticket.close');
            Route::post('/detail', [TicketController::class, 'detail'])->name('ticket.detail');
            Route::get('/detail', [TicketController::class, 'detail'])->name('ticket.detail');
            Route::prefix('/file')->group(function () {
                Route::post('/data', [TicketController::class, 'file_data'])->name('ticket.file.data');
                Route::post('/del', [TicketController::class, 'file_del'])->name('ticket.file.del');
            });
            Route::post('/response', [TicketController::class, 'response'])->name('ticket.response');
            Route::post('/response/data', [TicketController::class, 'response_data'])->name('ticket.response.data');
            Route::post('/response/save', [TicketController::class, 'response_save'])->name('ticket.response.save');
        });
    });
});

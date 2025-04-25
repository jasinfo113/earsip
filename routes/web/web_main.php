<?php

use App\Http\Controllers\Main\ArchivesController;
use Illuminate\Support\Facades\Route;


Route::middleware('valid_access')->group(function () {
    Route::prefix('main')->group(function () {
        Route::prefix('/archives')->group(function () {
            Route::get('/', function () {
                return view('main.archives.view');
            })->name('main.archives');
            Route::post('/data', [ArchivesController::class, 'archives_data'])->name('main.archives.data');
            Route::post('/form/{id?}', [ArchivesController::class, 'form'])->name('main.archives.form');
            Route::post('/save', [ArchivesController::class, 'save'])->name('main.archives.save');
            Route::post('/del', [ArchivesController::class, 'del'])->name('main.archives.del');
            Route::post('/detail', [ArchivesController::class, 'detail'])->name('main.archives.detail');
            Route::post('/pembubuhan', [ArchivesController::class, 'pembubuhan'])->name('main.archives.pembubuhan');
        });
    });
});

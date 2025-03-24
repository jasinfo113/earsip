<?php

use App\Http\Controllers\Apps\GeneralController;
use App\Http\Controllers\Apps\IntroController;
use App\Http\Controllers\Apps\InfoController;
use App\Http\Controllers\Apps\RulesController;
use App\Http\Controllers\Apps\ActivityController;
use Illuminate\Support\Facades\Route;


Route::middleware('valid_access')->group(function () {
    Route::prefix('apps')->group(function () {
        Route::prefix('/general')->group(function () {
            Route::get('/', function () {
                return view('apps.general.view');
            })->name('apps.general');
            Route::post('/data', [GeneralController::class, 'data'])->name('apps.general.data');
            Route::post('/form', [GeneralController::class, 'form'])->name('apps.general.form');
            Route::post('/save', [GeneralController::class, 'save'])->name('apps.general.save');
        });

        Route::prefix('/intro')->group(function () {
            Route::get('/', function () {
                return view('apps.intro.view');
            })->name('apps.intro');
            Route::post('/data', [IntroController::class, 'data'])->name('apps.intro.data');
            Route::post('/form/{id?}', [IntroController::class, 'form'])->name('apps.intro.form');
            Route::post('/save', [IntroController::class, 'save'])->name('apps.intro.save');
            Route::post('/del', [IntroController::class, 'del'])->name('apps.intro.del');
            Route::post('/status', [IntroController::class, 'status'])->name('apps.intro.status');
        });

        Route::prefix('/info')->group(function () {
            Route::prefix('/headline')->group(function () {
                Route::get('/', function () {
                    return view('apps.info.headline.view');
                })->name('apps.headline');
                Route::post('/data', [InfoController::class, 'headline_data'])->name('apps.headline.data');
                Route::post('/form/{id?}', [InfoController::class, 'headline_form'])->name('apps.headline.form');
                Route::post('/save', [InfoController::class, 'headline_save'])->name('apps.headline.save');
                Route::post('/del', [InfoController::class, 'headline_del'])->name('apps.headline.del');
                Route::post('/status', [InfoController::class, 'headline_status'])->name('apps.headline.status');
            });
            Route::prefix('/popup')->group(function () {
                Route::get('/', function () {
                    return view('apps.info.popup.view');
                })->name('apps.popup');
                Route::post('/data', [InfoController::class, 'popup_data'])->name('apps.popup.data');
                Route::post('/form/{id?}', [InfoController::class, 'popup_form'])->name('apps.popup.form');
                Route::post('/save', [InfoController::class, 'popup_save'])->name('apps.popup.save');
                Route::post('/del', [InfoController::class, 'popup_del'])->name('apps.popup.del');
                Route::post('/status', [InfoController::class, 'popup_status'])->name('apps.popup.status');
            });
        });

        Route::prefix('/rules')->group(function () {
            Route::get('/', function () {
                return view('apps.rules.view');
            })->name('apps.rules');
            Route::get('/data', [RulesController::class, 'data'])->name('apps.rules.data');
            Route::post('/data', [RulesController::class, 'data'])->name('apps.rules.data');
            Route::post('/save', [RulesController::class, 'save'])->name('apps.rules.save');
            Route::post('/status', [RulesController::class, 'status'])->name('apps.rules.status');
            Route::prefix('/permission')->group(function () {
                Route::post('/data', [RulesController::class, 'permission_data'])->name('apps.permission.data');
                Route::post('/form', [RulesController::class, 'permission_form'])->name('apps.permission.form');
                Route::post('/save', [RulesController::class, 'permission_save'])->name('apps.permission.save');
                Route::post('/del', [RulesController::class, 'permission_del'])->name('apps.permission.del');
            });
            Route::prefix('/role')->group(function () {
                Route::post('/data', [RulesController::class, 'role_data'])->name('apps.role.data');
                Route::post('/form/{id?}', [RulesController::class, 'role_form'])->name('apps.role.form');
                Route::post('/save', [RulesController::class, 'role_save'])->name('apps.role.save');
                Route::post('/status', [RulesController::class, 'role_status'])->name('apps.role.status');
            });
            Route::prefix('/reference')->group(function () {
                Route::post('/data', [RulesController::class, 'reference_data'])->name('apps.reference.data');
                Route::post('/form/{id?}', [RulesController::class, 'reference_form'])->name('apps.reference.form');
                Route::post('/save', [RulesController::class, 'reference_save'])->name('apps.reference.save');
                Route::post('/status', [RulesController::class, 'reference_status'])->name('apps.reference.status');
            });
        });

        Route::prefix('/activity')->group(function () {
            Route::get('/', function () {
                return view('apps.activity.view');
            })->name('apps.activity');
        });
        Route::prefix('/sosialisasi')->group(function () {
            Route::prefix('/materi')->group(function () {
                Route::post('/data', [ActivityController::class, 'sosialisasi_materi_data'])->name('apps.sosialisasi.materi.data');
                Route::post('/form', [ActivityController::class, 'sosialisasi_materi_form'])->name('apps.sosialisasi.materi.form');
                Route::post('/save', [ActivityController::class, 'sosialisasi_materi_save'])->name('apps.sosialisasi.materi.save');
                Route::post('/del', [ActivityController::class, 'sosialisasi_materi_del'])->name('apps.sosialisasi.materi.del');
            });
        });
        Route::prefix('/pembinaan')->group(function () {
            Route::prefix('/materi')->group(function () {
                Route::post('/data', [ActivityController::class, 'pembinaan_materi_data'])->name('apps.pembinaan.materi.data');
                Route::post('/form', [ActivityController::class, 'pembinaan_materi_form'])->name('apps.pembinaan.materi.form');
                Route::post('/save', [ActivityController::class, 'pembinaan_materi_save'])->name('apps.pembinaan.materi.save');
                Route::post('/del', [ActivityController::class, 'pembinaan_materi_del'])->name('apps.pembinaan.materi.del');
            });
        });
    });
});

<?php

use App\Http\Controllers\Cms\GeneralController;
use App\Http\Controllers\Cms\InfoController;
use App\Http\Controllers\Cms\NewsController;
use Illuminate\Support\Facades\Route;


Route::middleware('valid_access')->group(function () {
    Route::prefix('cms')->group(function () {
        Route::prefix('/general')->group(function () {
            Route::get('/', function () {
                return view('cms.general.view');
            })->name('cms.general');
            Route::post('/data', [GeneralController::class, 'data'])->name('cms.general.data');
            Route::post('/form', [GeneralController::class, 'form'])->name('cms.general.form');
            Route::post('/save', [GeneralController::class, 'save'])->name('cms.general.save');
        });

        Route::prefix('/info')->group(function () {
            Route::prefix('/glosarium')->group(function () {
                Route::get('/', function () {
                    return view('cms.info.glosarium.view');
                })->name('cms.info.glosarium');
                Route::post('/data', [InfoController::class, 'glosarium_data'])->name('cms.info.glosarium.data');
                Route::post('/form/{id?}', [InfoController::class, 'glosarium_form'])->name('cms.info.glosarium.form');
                Route::post('/save', [InfoController::class, 'glosarium_save'])->name('cms.info.glosarium.save');
                Route::post('/del', [InfoController::class, 'glosarium_del'])->name('cms.info.glosarium.del');
                Route::post('/status', [InfoController::class, 'glosarium_status'])->name('cms.info.glosarium.status');
            });

            Route::prefix('/law')->group(function () {
                Route::get('/', function () {
                    return view('cms.info.law.view');
                })->name('cms.info.law');
                Route::post('/data', [InfoController::class, 'law_data'])->name('cms.info.law.data');
                Route::post('/form/{id?}', [InfoController::class, 'law_form'])->name('cms.info.law.form');
                Route::post('/save', [InfoController::class, 'law_save'])->name('cms.info.law.save');
                Route::post('/del', [InfoController::class, 'law_del'])->name('cms.info.law.del');
                Route::post('/status', [InfoController::class, 'law_status'])->name('cms.info.law.status');
                Route::post('/detail', [InfoController::class, 'law_detail'])->name('cms.info.law.detail');
                Route::post('/view', [InfoController::class, 'law_view'])->name('cms.info.law.view');
                Route::post('/download', [InfoController::class, 'law_download'])->name('cms.info.law.download');
                Route::post('/pegawai', [InfoController::class, 'law_pegawai_data'])->name('cms.info.law.pegawai.data');
                Route::get('/pegawai/export', [InfoController::class, 'law_pegawai_export'])->name('cms.info.law.pegawai.export');
            });

            Route::prefix('/faq')->group(function () {
                Route::get('/', function () {
                    return view('cms.info.faq.view');
                })->name('cms.info.faq');
                Route::post('/data', [InfoController::class, 'faq_data'])->name('cms.info.faq.data');
                Route::post('/form/{id?}', [InfoController::class, 'faq_form'])->name('cms.info.faq.form');
                Route::post('/save', [InfoController::class, 'faq_save'])->name('cms.info.faq.save');
                Route::post('/del', [InfoController::class, 'faq_del'])->name('cms.info.faq.del');
                Route::post('/status', [InfoController::class, 'faq_status'])->name('cms.info.faq.status');
            });

            Route::get('/other', function () {
                return view('cms.info.other.view');
            })->name('cms.info.other');

            Route::prefix('/privacy')->group(function () {
                Route::post('/data', [InfoController::class, 'privacy_data'])->name('cms.info.privacy.data');
                Route::post('/form/{id?}', [InfoController::class, 'privacy_form'])->name('cms.info.privacy.form');
                Route::post('/save', [InfoController::class, 'privacy_save'])->name('cms.info.privacy.save');
            });

            Route::prefix('/terms')->group(function () {
                Route::post('/data', [InfoController::class, 'terms_data'])->name('cms.info.terms.data');
                Route::post('/form/{id?}', [InfoController::class, 'terms_form'])->name('cms.info.terms.form');
                Route::post('/save', [InfoController::class, 'terms_save'])->name('cms.info.terms.save');
            });

        });

        Route::prefix('/news')->group(function () {
            Route::get('/', function () {
                return view('cms.news.view');
            })->name('cms.news');
            Route::post('/data', [NewsController::class, 'data'])->name('cms.news.data');
            Route::post('/form/{id?}', [NewsController::class, 'form'])->name('cms.news.form');
            Route::post('/save', [NewsController::class, 'save'])->name('cms.news.save');
            Route::post('/del', [NewsController::class, 'del'])->name('cms.news.del');
            Route::post('/status', [NewsController::class, 'status'])->name('cms.news.status');
            Route::post('/detail', [NewsController::class, 'detail'])->name('cms.news.detail');
            Route::post('/view', [NewsController::class, 'view_data'])->name('cms.news.view.data');
            Route::post('/share', [NewsController::class, 'share_data'])->name('cms.news.share.data');
            Route::post('/pegawai', [NewsController::class, 'pegawai_data'])->name('cms.news.pegawai.data');
            Route::get('/pegawai/export', [NewsController::class, 'pegawai_export'])->name('cms.news.pegawai.export');
        });

    });
});

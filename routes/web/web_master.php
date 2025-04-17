<?php

use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\ClientController;
use App\Http\Controllers\Master\CategoriesController;
use App\Http\Controllers\Master\LocationController;
use App\Http\Controllers\Master\TagsController;
use Illuminate\Support\Facades\Route;


Route::middleware('valid_access')->group(function () {
    Route::prefix('master')->group(function () {
        Route::prefix('/categories')->group(function () {
            Route::get('/', function () {
                return view('master.categories.view');
            })->name('master.categories');
            Route::post('/data', [CategoriesController::class, 'categori_data'])->name('master.categories.data');
            Route::post('/form/{id?}', [CategoriesController::class, 'form'])->name('master.categories.form');
            Route::post('/save', [CategoriesController::class, 'save'])->name('master.categories.save');
            Route::post('/del', [CategoriesController::class, 'del'])->name('master.categories.del');
            Route::post('/detail', [CategoriesController::class, 'detail'])->name('master.categories.detail');
        });
        Route::prefix('/tags')->group(function () {
            Route::get('/', function () {
                return view('master.tags.view');
            })->name('master.tags');
            Route::post('/data', [TagsController::class, 'tag_data'])->name('master.tags.data');
            Route::post('/form/{id?}', [TagsController::class, 'form'])->name('master.tags.form');
            Route::post('/save', [TagsController::class, 'save'])->name('master.tags.save');
            Route::post('/del', [TagsController::class, 'del'])->name('master.tags.del');
            Route::post('/detail', [TagsController::class, 'detail'])->name('master.tags.detail');
        });
        Route::prefix('/location')->group(function () {
            Route::get('/', function () {
                return view('master.location.view');
            })->name('master.location');
            Route::post('/data', [LocationController::class, 'location_data'])->name('master.location.data');
            Route::post('/form/{id?}', [LocationController::class, 'form'])->name('master.location.form');
            Route::post('/save', [LocationController::class, 'save'])->name('master.location.save');
            Route::post('/del', [LocationController::class, 'del'])->name('master.tags.del');
            Route::post('/detail', [LocationController::class, 'detail'])->name('master.location.detail');
        });
    });
});




// use App\Http\Controllers\Account\AccountController;
// use App\Http\Controllers\Master\CategoriesController;
// use Illuminate\Support\Facades\Route;


// Route::middleware('auth')->group(function () {
//     Route::prefix('master')->group(function () {
//         Route::get('/categories', function () {
//             return view('master.categoris.view');
//         })->name('master.categories');
//         Route::post('/categori/data', [CategoriesController::class, 'categori_data'])->name('categori.data');
//         // Route::post('/update', [AccountController::class, 'update'])->name('account.update');
//         // Route::post('/password', [AccountController::class, 'password'])->name('account.password');
//         // Route::post('/deactivate', [AccountController::class, 'deactivate'])->name('account.deactivate');
//     });
// });

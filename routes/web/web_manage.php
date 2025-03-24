<?php

use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\ClientController;
use Illuminate\Support\Facades\Route;


Route::middleware('valid_access')->group(function () {
    Route::prefix('manage')->group(function () {
        Route::prefix('/user')->group(function () {
            Route::get('/', function () {
                return view('manage.user.view');
            })->name('manage.user');
            Route::post('/data', [UserController::class, 'data'])->name('manage.user.data');
            Route::post('/form/{id?}', [UserController::class, 'form'])->name('manage.user.form');
            Route::post('/save', [UserController::class, 'save'])->name('manage.user.save');
            Route::post('/del', [UserController::class, 'del'])->name('manage.user.del');
            Route::post('/detail', [UserController::class, 'detail'])->name('manage.user.detail');

            Route::prefix('/role')->group(function () {
                Route::get('/', function () {
                    return view('manage.role.view');
                })->name('manage.role');
                Route::post('/data', [UserController::class, 'role_data'])->name('manage.role.data');
                Route::post('/form/{id?}', [UserController::class, 'role_form'])->name('manage.role.form');
                Route::post('/save', [UserController::class, 'role_save'])->name('manage.role.save');
                Route::post('/status', [UserController::class, 'role_status'])->name('manage.role.status');
            });

            Route::prefix('/privilege')->group(function () {
                Route::get('/', function () {
                    return view('manage.privilege.view');
                })->name('manage.privilege');
                Route::post('/data', [UserController::class, 'privilege_data'])->name('manage.privilege.data');
                Route::post('/form', [UserController::class, 'privilege_form'])->name('manage.privilege.form');
                Route::post('/save', [UserController::class, 'privilege_save'])->name('manage.privilege.save');
                Route::post('/del', [UserController::class, 'privilege_del'])->name('manage.privilege.del');
                Route::post('/status', [UserController::class, 'privilege_status'])->name('manage.privilege.status');
            });
        });

        Route::prefix('/client')->group(function () {
            Route::get('/', function () {
                return view('manage.client.view');
            })->name('manage.client');
            Route::post('/data', [ClientController::class, 'data'])->name('manage.client.data');
            Route::post('/form/{id?}', [ClientController::class, 'form'])->name('manage.client.form');
            Route::post('/save', [ClientController::class, 'save'])->name('manage.client.save');
            Route::post('/del', [ClientController::class, 'del'])->name('manage.client.del');
            Route::post('/status', [ClientController::class, 'status'])->name('manage.client.status');
            Route::post('/detail', [ClientController::class, 'detail'])->name('manage.client.detail');
        });
    });
});

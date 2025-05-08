<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\GeneralController;
use Illuminate\Http\Request;

Route::middleware('auth')->group(function () {
    Route::prefix('general')->group(function () {
        // Route::get('/manual_process', [GeneralController::class, '_manualProcess']);
        Route::get('/selection', [GeneralController::class, 'selection']);
        Route::post('/selection', [GeneralController::class, 'selection']);
        Route::get('/download', [GeneralController::class, 'download']);
        Route::post('/download', [GeneralController::class, 'download']);
        Route::get('/test', function (Request $request) {
            // return app(\App\Classes\FcmController::class)->cron();
            // return app(\App\Classes\EmailController::class)->birthday();
            $_email = [
                'subject' => 'Selamat Datang Jhon',
                'title' => 'Selamat Datang di SIAGA API Jakarta',
                'content' => 'email.auth.registration',
                'url' => 'https://pemadam.jakarta.go.id/',
            ];
            return view('email.layout', $_email);
            $request = app(\App\Classes\EmailController::class)->send('ardian.jasinfo@gmail.com', $_email);
            echo json_encode($request);
        })->name('email');
        Route::get('/email-layout', function (Request $request) {
            $_email = [
                'subject' => 'Selamat Datang Jhon',
                'title' => 'Selamat Datang di SIAGA API Jakarta',
                'content' => 'email.auth.registration',
                'url' => 'https://pemadam.jakarta.go.id/',
            ];
            return view('email.layout', $_email);
            $_email = [
                'subject' => 'Selamat Datang di SI DAMKAR',
                'title' => 'Selamat Datang di SI DAMKAR',
                'content' => 'email.auth.registration',
                'url' => 'https://pemadam.jakarta.go.id/',
            ];
            return view('email.layout', $_email);
        })->name('email');
    });
});

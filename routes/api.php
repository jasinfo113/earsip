<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('get-token', [TokenController::class, 'Get_token']);
Route::middleware('auth:sanctum')->post('/getcodearsip', [TokenController::class, 'getcodearsip']);

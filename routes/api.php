<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



#Route::post('/getcodearsip', [TokenController::class, 'getcodearsip']);

Route::post('get-token', [TokenController::class, 'Get_token']);
//Route::post('/getcodearsip', [TokenController::class, 'getcodearsip']);
//pakai auth
Route::middleware('auth:sanctum')->post('/getcodearsip', [TokenController::class, 'getcodearsip']);
Route::middleware('auth:sanctum')->get('/form-options', [TokenController::class, 'getFormOptions']);
#Route::get('/form-options', [TokenController::class, 'getFormOptions']);

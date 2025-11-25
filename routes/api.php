<?php

// use API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api', 'jabatan:Staff'])->get('/tes', function (Request $request) {
    return 'haloo';
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);


// Route::prefix('admin')->middleware('auth:api')->group(function(){
//     Route::apiResource('users', UserController::class);
// });

Route::prefix('admin')->group(function(){
    Route::apiResource('users', UserController::class);
});
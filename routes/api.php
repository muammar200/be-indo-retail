<?php

// use API\AuthController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BarangMasukController;
use App\Http\Controllers\API\LaporanBarangKeluarController;
use App\Http\Controllers\API\LaporanBarangMasukController;
use App\Http\Controllers\API\PermintaanBarangController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\StokController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'jabatan:Staff'])->get('/tes', function (Request $request) {
    return 'haloo';
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);
Route::get('/me', [AuthController::class, 'me'])->middleware(['auth:api']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendOtp']);

// Route::prefix('admin')->middleware('auth:api')->group(function(){
//     Route::apiResource('users', UserController::class);
// });

Route::prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('barangMasuk', BarangMasukController::class);
    Route::apiResource('laporanBarangMasuk', LaporanBarangMasukController::class);
    Route::post('cetakLaporanBarangMasuk', [LaporanBarangMasukController::class, 'cetakLaporanBarangMasuk']);
    Route::apiResource('barangKeluar', BarangKeluarController::class);
    Route::apiResource('stok', StokController::class);
    Route::get('cetakStok', [StokController::class, 'cetakStok']);
    Route::post('permintaanStok', [StokController::class, 'permintaanStok']);
    Route::apiResource('laporanBarangKeluar', LaporanBarangKeluarController::class);
    Route::apiResource('permintaanBarang', PermintaanBarangController::class);
    Route::get('cetakPermintaanBarang', [PermintaanBarangController::class, 'cetakPermintaanBarang']);
});

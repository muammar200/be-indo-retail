<?php

// use API\AuthController;
use App\Http\Controllers\API\AbsensiController;
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

Route::post('/absensi/clock-in', [AbsensiController::class, 'clockIn'])->middleware(['auth:api']);
Route::post('/absensi/clock-out', [AbsensiController::class, 'clockOut'])->middleware(['auth:api']);
Route::post('/absensi/izin-sakit', [AbsensiController::class, 'izinSakit'])->middleware(['auth:api']);
Route::get('/absensi/history', [AbsensiController::class, 'riwayatAbsenByBulan'])->middleware(['auth:api']);

Route::get('/data-absensi/on-day', [App\Http\Controllers\API\DataAbsensiController::class, 'absensiOnDay']);
Route::get('/data-absensi/on-day/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'absensiById']);
Route::get('/data-absensi/bukti-izin-sakit/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'buktiIzinSakit']);
Route::patch('/data-absensi/update-status/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'updateStatusAbsensi']);
Route::get('/data-absensi/rekap-absensi-by-bulan', [App\Http\Controllers\API\DataAbsensiController::class, 'rekapAbsensiByBulan']);

Route::get('/dashboard/chart', [App\Http\Controllers\API\DashboardController::class, 'chart']);
Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'dashboard']);

// Route::prefix('admin')->middleware('auth:api')->group(function(){
//     Route::apiResource('users', UserController::class);
// });

Route::prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('barangMasuk', BarangMasukController::class);
    Route::apiResource('laporanBarangMasuk', LaporanBarangMasukController::class);
    Route::post('cetakLaporanBarangMasuk', [LaporanBarangMasukController::class, 'cetakLaporanBarangMasuk']);
    Route::apiResource('barangKeluar', BarangKeluarController::class);
        Route::post('cetakLaporanBarangKeluar', [LaporanBarangKeluarController::class, 'cetakLaporanBarangKeluar']);
    Route::apiResource('stok', StokController::class);
    Route::get('cetakStok', [StokController::class, 'cetakStok']);
    Route::post('permintaanStok', [StokController::class, 'permintaanStok']);
    Route::apiResource('laporanBarangKeluar', LaporanBarangKeluarController::class);
    Route::apiResource('permintaanBarang', PermintaanBarangController::class);
    Route::get('cetakPermintaanBarang', [PermintaanBarangController::class, 'cetakPermintaanBarang']);
});



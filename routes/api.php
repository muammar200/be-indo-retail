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
use App\Http\Controllers\FcmController;
use App\Http\Controllers\StokController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:api', 'jabatan:Staff'])->get('/tes', function (Request $request) {
    return 'haloo';
});

Route::post('/fcm-token', [FcmController::class, 'store'])->middleware(['auth:api']);

Route::get('/dates-in-month', [App\Http\Controllers\API\DataAbsensiController::class, 'getTanggalDiBUlan']);
Route::get('/month', [App\Http\Controllers\API\DataAbsensiController::class, 'getMonth']);


// AUTH
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);
Route::get('/me', [AuthController::class, 'me'])->middleware(['auth:api']);

// FORGOT PASSWORD
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendOtp'])->middleware(['otp.throttle']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/forgot-password/reset-password', [AuthController::class, 'resetPassword']);

// ABSENSI SAYA
Route::post('/absensi/clock-in', [AbsensiController::class, 'clockIn'])->middleware(['auth:api']);
Route::post('/absensi/clock-out', [AbsensiController::class, 'clockOut'])->middleware(['auth:api']);
Route::post('/absensi/izin-sakit', [AbsensiController::class, 'izinSakit'])->middleware(['auth:api']);
Route::get('/absensi/history', [AbsensiController::class, 'riwayatAbsenByBulan'])->middleware(['auth:api']);

// DATA ABSENSI
Route::middleware(['auth:api'])->group(function(){
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff'])->group(function () {
        Route::get('/data-absensi/on-day', [App\Http\Controllers\API\DataAbsensiController::class, 'absensiOnDay']); // Pimpinan, Staff
        Route::get('/data-absensi/on-day/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'absensiById']); // Pimpinan, Staff
    // });
    // Route::middleware(['auth:api', 'jabatan:Pimpinan'])->group(function () {
        Route::get('/data-absensi/bukti-izin-sakit/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'buktiIzinSakit']); // //Pimpinan
        Route::patch('/data-absensi/update-status/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'updateStatusAbsensi']); // Pimpinan
    // });
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff'])->group(function () {
        Route::get('/data-absensi/rekap-absensi-by-bulan', [App\Http\Controllers\API\DataAbsensiController::class, 'rekapAbsensiByBulan']); // Pimpinan, Staff
    // });
    // Route::middleware(['auth:api', 'jabatan:Pimpinan'])->group(function () {
        Route::patch('/data-absensi/approve-izin-sakit/{id}', [App\Http\Controllers\API\DataAbsensiController::class, 'approveIzinSakit']); // Pimpinan
    // });
});

// DASHBOARD
// Route::middleware(['auth:api'])->group(function () {
Route::get('/dashboard/chart', [App\Http\Controllers\API\DashboardController::class, 'chart']);
Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'dashboard']);
// });

// Route::prefix('admin')->middleware('auth:api')->group(function(){
//     Route::apiResource('users', UserController::class);
// });

Route::middleware(['auth:api'])->prefix('admin')->group(function () {

    // USER
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff'])->group(function () {
        Route::apiResource('users', UserController::class);
    // });

    // BARANGMASUK
    // Route::apiResource('barangMasuk', BarangMasukController::class);
    // Route::middleware(['auth:api'])->group(function () {
        Route::get('/barangMasuk', [BarangMasukController::class, 'index']);
        Route::get('/barangMasuk/{id}', [BarangMasukController::class, 'show']);
    // });
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff'])->group(function () {
        Route::post('/barangMasuk', [BarangMasukController::class, 'store']);
        Route::patch('/barangMasuk/{id}', [BarangMasukController::class, 'update']);
        Route::delete('/barangMasuk/{id}', [BarangMasukController::class, 'destroy']);
    // });

    // LAPORAN BARANG MASUK
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff'])->group(function () {
        Route::apiResource('laporanBarangMasuk', LaporanBarangMasukController::class);
        Route::post('cetakLaporanBarangMasuk', [LaporanBarangMasukController::class, 'cetakLaporanBarangMasuk']);
    // });

    // BARANG KELUAR
    // Route::apiResource('barangKeluar', BarangKeluarController::class);
    // Route::middleware(['auth:api'])->group(function () {
        Route::get('/barangKeluar', [BarangKeluarController::class, 'index']); // ALL ROLE
        Route::get('/barangKeluar/{id}', [BarangKeluarController::class, 'show']); // ALL ROLE
    // });
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff,Karyawan-Pelapor'])->group(function () {
        Route::post('/barangKeluar', [BarangKeluarController::class, 'store']);
        Route::patch('/barangKeluar/{id}', [BarangKeluarController::class, 'update']);
        Route::delete('/barangKeluar/{id}', [BarangKeluarController::class, 'destroy']);
    // });

    // LAPORAN BARANG KELUAR
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff,Karyawan-Pelapor'])->group(function () {
        Route::post('cetakLaporanBarangKeluar', [LaporanBarangKeluarController::class, 'cetakLaporanBarangKeluar']);
        Route::apiResource('laporanBarangKeluar', LaporanBarangKeluarController::class);
    // });

    // DATA STOK
    // Route::middleware(['auth:api', 'jabatan:Pimpinan,Staff'])->group(function () {
    Route::apiResource('stok', StokController::class);
    Route::get('/allStok', [StokController::class, 'allStok']);
    Route::get('cetakStok', [StokController::class, 'cetakStok']);
    Route::post('permintaanStok', [StokController::class, 'permintaanStok']);
    // });
    Route::apiResource('permintaanBarang', PermintaanBarangController::class);
    Route::get('cetakPermintaanBarang', [PermintaanBarangController::class, 'cetakPermintaanBarang']);
});

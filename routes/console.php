<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Hanya contoh cara penggunaan console
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Mendapatkan waktu saat ini berdasarkan zona waktu Asia/Makassar
$now = Carbon::now('Asia/Makassar'); 

// Mengatur format waktu menjadi jam dan menit sekarang. Untuk waktu notifikasinya diatur di server
$formattedTime = $now->format('H:i'); 

// Menjadwalkan perintah 'notify:daily' untuk dijalankan setiap hari pada waktu yang telah diformat
Schedule::command('notify:daily')
    ->dailyAt($formattedTime) // Waktu eksekusi berdasarkan nilai $formattedTime
    ->timezone('Asia/Makassar') // Menetapkan zona waktu untuk jadwal ini
    ->withoutOverlapping() // Mencegah eksekusi jadwal yang tumpang tindih
    ->onOneServer(); // Menjalankan perintah hanya pada satu server (untuk mencegah eksekusi ganda di server berbeda)


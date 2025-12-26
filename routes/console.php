<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$now = Carbon::now('Asia/Makassar'); // Mendapatkan waktu sekarang di zona waktu Asia/Makassar
$formattedTime = $now->format('H:i'); // Memformat waktu menjadi format jam:menit (24 jam)

Schedule::command('notify:daily')
    ->dailyAt($formattedTime) // Menggunakan waktu sekarang
    ->timezone('Asia/Makassar')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule::command('notify:daily')
//     ->dailyAt('23:26')
//     ->timezone('Asia/Makassar')
//     ->withoutOverlapping()
//     ->onOneServer();

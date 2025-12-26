<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/notify-daily', function () {
    Artisan::call('notify:daily');

    return Artisan::output();
});

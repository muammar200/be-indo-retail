<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;  // Menggunakan HTTP client Laravel untuk membuat permintaan HTTP

class FonnteService
{
    // Fungsi statis untuk mengirim pesan menggunakan Fonnte API
    public static function send($target, $message)
    {
        // Mendapatkan token otentikasi dari file .env
        $token = env('FONNTE_TOKEN');

        // Mengirimkan permintaan POST ke API Fonnte dengan target dan pesan yang diberikan
        return Http::withHeaders([  // Menambahkan header otentikasi
            'Authorization' => $token,
        ])->asForm()  // Menyertakan data dalam format form
            ->post('https://api.fonnte.com/send', [  // URL endpoint API Fonnte untuk mengirim pesan
                'target' => $target,  // Target nomor atau penerima pesan
                'message' => $message,  // Isi pesan yang akan dikirim
            ])->json();  // Mengambil respons dalam format JSON
    }
}

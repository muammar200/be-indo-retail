<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FcmService;

class SendDailyNotification extends Command
{
    /**
     * Nama dan signature dari perintah console.
     * 'notify:daily' adalah perintah yang dijalankan di command line.
     */
    protected $signature = 'notify:daily';

    /**
     * Deskripsi perintah console.
     * Menjelaskan tujuan perintah ini, yaitu mengirim push notification harian.
     */
    protected $description = 'Send daily push notification to all users';

    /**
     * Menjalankan perintah.
     * Menggunakan FcmService untuk mengirim notifikasi push kepada semua pengguna.
     */
    public function handle(FcmService $fcm)
    {
        // Judul dan isi notifikasi yang akan dikirimkan
        $title = 'Pengingat Harian';
        $body  = 'Jangan lupa absen hari ini 🚀';

        // Mengirim notifikasi ke semua pengguna
        $fcm->sendToAll($title, $body);

        // Menampilkan informasi bahwa notifikasi telah berhasil dikirim
        $this->info('Daily notification sent successfully.');
    }
}

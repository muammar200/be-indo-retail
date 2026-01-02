<?php

namespace App\Services;

use App\Models\FcmToken;  // Model untuk mengambil FCM tokens yang telah disimpan
use Google\Auth\Credentials\ServiceAccountCredentials;  // Menggunakan kredensial akun layanan untuk autentikasi ke Firebase
use GuzzleHttp\Client;  // Client HTTP untuk mengirimkan permintaan ke Firebase Cloud Messaging (FCM) API
use GuzzleHttp\Exception\RequestException;  // Menangani pengecualian yang terjadi saat permintaan HTTP gagal
use Illuminate\Support\Facades\Log;  // Menyediakan fasilitas untuk pencatatan log

class FcmService
{
    protected string $projectId;  // ID proyek FCM
    protected array $serviceAccount;  // Kredensial akun layanan Firebase

    public function __construct()
    {
        // Mengambil ID proyek FCM dari file konfigurasi
        $this->projectId = config('services.fcm.project_id');

        // Mendekode file kredensial akun layanan Firebase
        $this->serviceAccount = json_decode(
            file_get_contents(storage_path('app/firebase-service-account.json')),  // Mengambil file kredensial
            true  // Mengonversi JSON ke array asosiatif
        );
    }

    // Mengambil token akses untuk autentikasi ke FCM API menggunakan akun layanan
    protected function getAccessToken(): string
    {
        // Membuat objek ServiceAccountCredentials dengan scope untuk Firebase Messaging
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $this->serviceAccount
        );

        // Mendapatkan token akses
        $token = $credentials->fetchAuthToken();

        // Jika token tidak ada, lempar pengecualian
        if (!isset($token['access_token'])) {
            throw new \Exception('Failed to get FCM access token');
        }

        return $token['access_token'];
    }

    // Mengirimkan pemberitahuan ke semua token perangkat yang ada
    public function sendToAll(string $title, string $body): void
    {
        // Mengambil semua token FCM yang ada di database
        $tokens = FcmToken::pluck('token')->toArray();
        
        // Jika tidak ada token, keluar dari fungsi
        if (empty($tokens)) return;

        // Mendapatkan token akses untuk autentikasi ke FCM API
        $accessToken = $this->getAccessToken();
        
        // Membuat client HTTP untuk permintaan
        $client = new Client([
            'timeout' => 5,  // Menetapkan waktu tunggu 5 detik untuk permintaan HTTP
        ]);
        
        // Mengirim pemberitahuan ke setiap token perangkat
        foreach ($tokens as $token) {
            try {
                // Mengirimkan permintaan POST ke FCM API untuk mengirimkan pemberitahuan
                $client->post(
                    "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                    [
                        'headers' => [
                            'Authorization' => "Bearer {$accessToken}",  // Menyertakan token akses di header
                            'Content-Type' => 'application/json',  // Menyertakan tipe konten JSON
                        ],
                        'json' => [
                            'message' => [
                                'token' => trim($token),  // Token perangkat yang akan dikirimi pemberitahuan
                                'notification' => [
                                    'title' => $title,  // Judul pemberitahuan
                                    'body' => $body,    // Isi pemberitahuan
                                ],
                            ],
                        ],
                    ]
                );
            } catch (RequestException $e) {
                // Jika permintaan gagal, log kesalahan dengan informasi token dan responsnya
                Log::error('FCM Send Failed', [
                    'token' => $token,  // Menyertakan token yang gagal dikirim
                    'response' => optional($e->getResponse())->getBody()->getContents(),  // Menyertakan respons dari FCM jika ada
                ]);
            }
        }
    }
}

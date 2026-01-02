<?php

namespace App\Http\Resources;  // Menunjukkan bahwa kelas ini berada di dalam namespace Resources

use Carbon\Carbon;  // Mengimpor Carbon untuk memanipulasi dan memformat tanggal
use Illuminate\Http\Request;  // Mengimpor Request untuk menangani data request HTTP
use Illuminate\Http\Resources\Json\JsonResource;  // Mengimpor JsonResource untuk mengubah resource menjadi array JSON

class RiwayatAbsenResource extends JsonResource  // Mendeklarasikan kelas resource untuk model RiwayatAbsen
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>  // Mendeklarasikan tipe data yang dikembalikan berupa array
     */
    public function toArray(Request $request): array  // Fungsi utama yang mengubah data resource menjadi array
    {
        static $incrementId = 1;  // Variabel statis untuk memberikan ID yang bertambah otomatis

        // Mengonversi tanggal dengan format yang lebih manusiawi (contoh: "Senin, 01 Januari 2023")
        $tanggalDenganHari = Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l, d F Y');

        return [
            'id' => $incrementId++,  // Memberikan ID yang bertambah tiap kali (dimulai dari 1)
            'tanggal' => $tanggalDenganHari,  // Mengubah tanggal menjadi format yang lebih mudah dibaca dengan hari dalam bahasa Indonesia
            'waktu_checkin' => $this->waktu_checkin ? $this->waktu_checkin : '-',  // Jika waktu check-in ada, tampilkan; jika tidak, tampilkan "-"
            'waktu_checkout' => $this->waktu_checkout ? $this->waktu_checkout : '-',  // Jika waktu checkout ada, tampilkan; jika tidak, tampilkan "-"
            'status' => $this->status === 'Menunggu Konfirmasi' ? '-' : $this->status,  // Jika status adalah "Menunggu Konfirmasi", tampilkan "-"; jika tidak, tampilkan status aslinya
            'izin_sakit' => $this->image_proof ? $this->status : '-',  // Jika ada bukti (image_proof), tampilkan status; jika tidak, tampilkan "-"
        ];
    }
}

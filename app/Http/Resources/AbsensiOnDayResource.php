<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsensiOnDayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID absensi pada hari tertentu
            'user' => $this->user->name, // Nama pengguna yang melakukan absensi
            'waktu_checkin' => $this->waktu_checkin, // Waktu pengguna melakukan check-in
            'waktu_checkout' => $this->waktu_checkout, // Waktu pengguna melakukan check-out
            'status' => $this->status, // Status absensi (misalnya: "Hadir", "Izin", "Sakit")
            'izin_sakit' => $this->image_proof ? $this->status : '-', // Menyertakan bukti izin atau sakit jika ada
        ];
    }
}

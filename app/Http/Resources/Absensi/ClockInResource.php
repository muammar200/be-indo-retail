<?php

namespace App\Http\Resources\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClockInResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->user->name, // Mengambil nama pengguna yang melakukan check-in
            'tanggal' => Carbon::parse($this->tanggal)->format('d-m-Y'), // Format tanggal check-in
            'status' => $this->status, // Status check-in (misalnya: "Hadir", "Terlambat")
            'waktu_checkin' => $this->waktu_checkin, // Waktu tepat check-in (misalnya: jam 08:00)
        ];
    }
}

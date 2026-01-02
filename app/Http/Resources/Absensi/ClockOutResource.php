<?php

namespace App\Http\Resources\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClockOutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->user->name, // Mengambil nama pengguna yang melakukan check-out
            'tanggal' => Carbon::parse($this->tanggal)->format('d-m-Y'), // Format tanggal check-out
            'status' => $this->status, // Status check-out (misalnya: "Pulang Tepat Waktu", "Terlambat")
            'waktu_checkout' => $this->waktu_checkout, // Waktu tepat check-out (misalnya: jam 17:00)
        ];
    }
}

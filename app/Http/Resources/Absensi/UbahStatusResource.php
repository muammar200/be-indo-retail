<?php

namespace App\Http\Resources\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UbahStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID perubahan status absensi
            'user' => $this->user->name, // Nama pengguna yang status absensinya diubah
            'tanggal' => Carbon::parse($this->tanggal)->format('d-m-Y'), // Tanggal perubahan status absensi
            'status' => $this->status, // Status absensi (misalnya: "Hadir", "Izin", "Sakit")
        ];
    }
}

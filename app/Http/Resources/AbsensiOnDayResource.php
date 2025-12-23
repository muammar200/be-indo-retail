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
            'id' => $this->id,
            'user' => $this->user->name,
            'waktu_checkin' => $this->waktu_checkin,
            'waktu_checkout' => $this->waktu_checkout,
            // 'status' => $this->status === 'Menunggu Konfirmasi' ? '-' : $this->status,
            'status' => $this->status,
            'izin_sakit' => $this->image_proof ? $this->status : '-',
        ];
    }
}

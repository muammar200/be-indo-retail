<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatAbsenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tanggalDenganHari = Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l, d F Y');

        return [
            'tanggal' => $tanggalDenganHari,
            'waktu_checkin' => $this->waktu_checkin,
            'waktu_checkout' => $this->waktu_checkout,
            'status' => $this->status,
        ];
    }
}

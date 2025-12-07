<?php

namespace App\Http\Resources\Absensi;

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
            'user' => $this->user->name,
            'tanggal' => $this->tanggal,
            'status' => $this->status,
            'waktu_checkout' => $this->waktu_checkout,
        ];
    }
}

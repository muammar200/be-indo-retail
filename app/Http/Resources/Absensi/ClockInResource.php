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
            'user' => $this->user->name,
            // 'tanggal' => $this->tanggal,
            'tanggal' => Carbon::parse($this->tanggal)->format('d-m-Y'),
            'status' => $this->status,
            'waktu_checkin' => $this->waktu_checkin,
        ];
    }
}

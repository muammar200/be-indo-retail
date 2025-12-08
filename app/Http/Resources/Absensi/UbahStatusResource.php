<?php

namespace App\Http\Resources\Absensi;

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
            'id' => $this->id,
            'user' => $this->user->name,
            'tanggal' => $this->tanggal,
            'status' => $this->status,
        ];
    }
}

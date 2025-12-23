<?php

namespace App\Http\Resources\Absensi;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsensiRekapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        static $incrementId = 1;

        return [
            'id' => $incrementId++,
            'nama' => $this->nama,
            'jabatan' => $this->jabatan,
            'hadir' => $this->hadir,
            'izin' => $this->izin,
            'sakit' => $this->sakit,
        ];
    }
}

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
            'id' => $incrementId++, // Menggunakan counter statis untuk ID unik
            'nama' => $this->nama, // Nama karyawan
            'jabatan' => $this->jabatan, // Jabatan karyawan
            'hadir' => $this->hadir, // Status kehadiran (hadir)
            'izin' => $this->izin, // Status izin
            'sakit' => $this->sakit, // Status sakit
        ];
    }
}

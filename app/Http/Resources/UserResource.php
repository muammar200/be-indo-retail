<?php

namespace App\Http\Resources;  // Menunjukkan bahwa kelas ini berada di dalam namespace Resources

use Illuminate\Http\Request;  // Mengimpor Request untuk menangani data request HTTP
use Illuminate\Http\Resources\Json\JsonResource;  // Mengimpor JsonResource untuk mengubah resource menjadi array JSON

class UserResource extends JsonResource  // Mendeklarasikan kelas resource untuk model User
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>  // Mendeklarasikan tipe data yang dikembalikan berupa array
     */
    public function toArray(Request $request): array  // Fungsi utama yang mengubah data resource User menjadi array
    {
        return [
            'id' => $this->id,  // Menampilkan ID user
            'name' => $this->name,  // Menampilkan nama user
            'no_hp' => $this->no_hp,  // Menampilkan nomor handphone user
            'jabatan' => $this->jabatan,  // Menampilkan jabatan user
        ];
    }
}

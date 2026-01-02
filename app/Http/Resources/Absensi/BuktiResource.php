<?php

namespace App\Http\Resources\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuktiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // Mengambil ID bukti absensi
            'user' => $this->user->name, // Mengambil nama pengguna yang terkait dengan bukti
            'tanggal' => Carbon::parse($this->tanggal)->format('d-m-Y'), // Format tanggal bukti absensi
            'kategori' => $this->kategori, // Mengambil kategori (misalnya: "Izin" atau "Sakit")
            'keterangan' => $this->keterangan, // Mengambil keterangan tambahan jika ada
            'image' => url('storage/images/absensi/'.$this->image_proof), // URL untuk file bukti absensi (gambar)
        ];
    }
}

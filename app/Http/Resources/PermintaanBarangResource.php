<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermintaanBarangResource extends JsonResource
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
            'nama_barang' => $this->nama_barang,
            'tanggal_permintaan' => $this->tanggal_permintaan,
            'jumlah_permintaan' => $this->jumlah_permintaan,
            'modal' => $this->modal,
            'nomor_npwp' => $this->nomor_npwp,
        ];
    }
}

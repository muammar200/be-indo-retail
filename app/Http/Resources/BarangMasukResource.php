<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangMasukResource extends JsonResource
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
            'kode_barang' => $this->kode_barang,
            'nama' => $this->jumlah,
            'harga' => $this->harga,
            'jumlah' => $this->jumlah,
            'sub_kategori' => $this->sub_kategori,
            'tanggal_masuk' => $this->tanggal_masuk,
        ];
    }
}

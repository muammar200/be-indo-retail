<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StokResource extends JsonResource
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
            'nama' => $this->nama,
            'harga' => $this->harga,
            'stok_awal' => $this->stok_awal,
            'stok_total' => $this->stok_total,
            'tanggal_masuk' => $this->tanggal_masuk,
            'tanggal_update' => $this->tanggal_update,
            'sub_kategori' => $this->sub_kategori,
        ];
    }
}

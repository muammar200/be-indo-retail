<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'id' => $this->id, // ID Barang Masuk
            'kode_barang' => $this->kode_barang, // Kode barang yang masuk
            'nama' => $this->nama, // Nama barang yang masuk
            'harga' => $this->harga, // Harga barang yang masuk
            'jumlah' => $this->jumlah, // Jumlah barang yang masuk
            'sub_kategori' => $this->sub_kategori, // Subkategori barang yang masuk
            'tanggal_masuk' => Carbon::parse($this->tanggal_masuk)->format('d-m-Y'), // Tanggal masuk barang, diformat ke dd-mm-yyyy
        ];
    }
}

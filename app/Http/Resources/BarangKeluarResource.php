<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangKeluarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID Barang Keluar
            'barang_id' => $this->stok->id, // ID dari stok yang terkait dengan barang keluar
            'kode_barang' => $this->kode_barang, // Kode barang yang keluar
            'nama' => $this->nama, // Nama barang yang keluar
            'harga' => $this->harga, // Harga barang yang keluar
            'jumlah' => $this->jumlah, // Jumlah barang yang keluar
            'sub_kategori' => $this->sub_kategori, // Subkategori dari barang
            'tanggal_keluar' => Carbon::parse($this->tanggal_keluar)->format('d-m-Y'), // Tanggal keluar barang, diformat ke dd-mm-yyyy
            'toko_tujuan' => $this->toko_tujuan, // Nama toko tujuan barang keluar
        ];
    }
}

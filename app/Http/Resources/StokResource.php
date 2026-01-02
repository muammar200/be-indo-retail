<?php

namespace App\Http\Resources;  // Menunjukkan bahwa kelas ini berada di dalam namespace Resources

use Carbon\Carbon;  // Mengimpor library Carbon untuk manipulasi tanggal dan waktu
use Illuminate\Http\Request;  // Mengimpor Request untuk menangani data request HTTP
use Illuminate\Http\Resources\Json\JsonResource;  // Mengimpor JsonResource untuk mengubah resource menjadi array JSON

class StokResource extends JsonResource  // Mendeklarasikan kelas resource untuk model Stok
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>  // Mendeklarasikan tipe data yang dikembalikan berupa array
     */
    public function toArray(Request $request): array  // Fungsi utama yang mengubah data resource menjadi array
    {
        return [
            'id' => $this->id,  // Menampilkan ID stok
            'kode_barang' => $this->kode_barang,  // Menampilkan kode barang
            'nama' => $this->nama,  // Menampilkan nama barang
            'harga' => $this->harga,  // Menampilkan harga barang
            'stok_awal' => $this->stok_awal,  // Menampilkan jumlah stok awal
            'stok_total' => $this->stok_total,  // Menampilkan jumlah stok total yang ada
            'tanggal_masuk' => Carbon::parse($this->tanggal_masuk)->format('d-m-Y'),  // Mengonversi tanggal masuk barang ke format "dd-mm-yyyy"
            'tanggal_update' => Carbon::parse($this->tanggal_update)->format('d-m-Y'),  // Mengonversi tanggal update barang ke format "dd-mm-yyyy"
            'sub_kategori' => $this->sub_kategori,  // Menampilkan subkategori barang
        ];
    }
}

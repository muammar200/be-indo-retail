<?php

namespace App\Http\Resources;  // Mendeklarasikan namespace untuk resource ini

use Carbon\Carbon;  // Mengimpor kelas Carbon untuk manipulasi dan format tanggal
use Illuminate\Http\Request;  // Mengimpor kelas Request untuk menangani request HTTP
use Illuminate\Http\Resources\Json\JsonResource;  // Mengimpor JsonResource untuk mengubah data menjadi format JSON

class PermintaanBarangResource extends JsonResource  // Mendeklarasikan kelas resource untuk model PermintaanBarang
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>  // Menyatakan bahwa fungsi ini akan mengembalikan array dengan string sebagai key dan data mixed sebagai value
     */
    public function toArray(Request $request): array  // Method utama yang mengubah data resource menjadi array
    {
        return [
            'id' => $this->id,  // Mengambil ID dari model PermintaanBarang
            'nama_barang' => $this->nama_barang,  // Mengambil nama barang dari model
            'tanggal_permintaan' => Carbon::parse($this->tanggal_permintaan)->format('d-m-Y'),  // Mengonversi tanggal permintaan ke format 'd-m-Y'
            'jumlah_permintaan' => $this->jumlah_permintaan,  // Mengambil jumlah permintaan dari model
            'modal' => $this->modal,  // Mengambil modal dari model
            'nomor_npwp' => $this->nomor_npwp,  // Mengambil nomor NPWP dari model
        ];
    }
}

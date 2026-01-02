<?php

namespace App\Models;  // Menunjukkan bahwa kelas ini berada dalam namespace Models

use Illuminate\Database\Eloquent\Model;  // Mengimpor Model untuk Eloquent ORM

class BarangKeluar extends Model  // Mendeklarasikan kelas model BarangKeluar yang akan berinteraksi dengan tabel 'barang_keluar' di database
{
    protected $table = 'barang_keluar';  // Menentukan nama tabel yang digunakan dalam database

    protected $fillable = [  // Mendefinisikan field yang dapat diisi melalui mass assignment
        'kode_barang',  // Kode barang yang keluar
        'nama',  // Nama barang yang keluar
        'harga',  // Harga barang
        'jumlah',  // Jumlah barang yang keluar
        'sub_kategori',  // Sub kategori barang
        'tanggal_keluar',  // Tanggal barang keluar
        'toko_tujuan',  // Toko tujuan barang keluar
    ];

    public function stok()  // Mendefinisikan relasi antara BarangKeluar dan Stok
    {
        return $this->belongsTo(Stok::class, 'kode_barang', 'kode_barang');  // Relasi belongsTo dengan model Stok menggunakan 'kode_barang' sebagai kunci hubungan
    }
}

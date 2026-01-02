<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';  // Menentukan nama tabel yang digunakan dalam database

    protected $fillable = [  // Mendefinisikan field yang dapat diisi melalui mass assignment
        'kode_barang',  // Kode barang yang masuk ke stok
        'nama',  // Nama barang yang masuk
        'harga',  // Harga barang
        'jumlah',  // Jumlah barang yang masuk
        'sub_kategori',  // Sub kategori barang
        'tanggal_masuk',  // Tanggal barang masuk ke dalam stok
    ];

    public function stok()  // Mendefinisikan relasi antara BarangMasuk dan Stok
    {
        return $this->belongsTo(Stok::class, 'kode_barang', 'kode_barang');  // Relasi belongsTo dengan model Stok, menggunakan 'kode_barang' sebagai kunci hubungan
    }

    // UNTUK SEEDER
    // public static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($barangMasuk) {
    //         // Ambil atau buat stok baru berdasarkan kode_barang
    //         $stok = \App\Models\Stok::firstOrNew(['kode_barang' => $barangMasuk->kode_barang]);

    //         // Update stok_total dengan menambah jumlah barang yang masuk
    //         $stok->stok_awal = $barangMasuk->jumlah;
    //         $stok->stok_total = $barangMasuk->jumlah;
    //         $stok->save();
    //     });
    // }
}

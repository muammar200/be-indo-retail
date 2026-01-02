<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $table = 'stok';  // Menentukan nama tabel yang digunakan untuk model ini, yaitu 'stok'

    protected $fillable = [  // Mendefinisikan kolom-kolom yang dapat diisi melalui mass assignment
        'kode_barang',     // Kode unik untuk setiap barang
        'nama',            // Nama barang
        'harga',           // Harga barang
        'stok_awal',       // Stok awal barang yang masuk
        'stok_total',      // Total stok barang yang tersedia
        'tanggal_masuk',   // Tanggal barang masuk ke dalam sistem
        'tanggal_update',  // Tanggal terakhir kali stok diperbarui
        'sub_kategori',    // Kategori atau subkategori barang
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'kode_barang', 'kode_barang');
        // Relasi one-to-many, menunjukkan bahwa setiap barang dapat memiliki banyak entri di tabel 'barang_masuk'
        // Berdasarkan kolom 'kode_barang' di kedua tabel.
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'kode_barang', 'kode_barang');
        // Relasi one-to-many, menunjukkan bahwa setiap barang dapat memiliki banyak entri di tabel 'barang_keluar'
        // Berdasarkan kolom 'kode_barang' di kedua tabel.
    }

    // UNTUK SEEDER
    // public static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($barangMasuk) {
    //         // Ambil atau buat stok baru berdasarkan kode_barang
    //         $stok = BarangMasuk::firstOrNew(['kode_barang' => $barangMasuk->kode_barang]);
    //          $stok->kode_barang = $barangMasuk->kode_barang;
    //         $stok->nama = $barangMasuk->nama;
    //          $stok->harga = $barangMasuk->harga;
    //           $stok->sub_kategori = $barangMasuk->sub_kategori;
    //            $stok->tanggal_masuk = $barangMasuk->tanggal_masuk;
    //         // Update stok_total dengan menambah jumlah barang yang masuk
    //         $stok->jumlah = $barangMasuk->stok_awal;
    //         // $stok->stok_total = $barangMasuk->jumlah;
    //         $stok->save();
    //     });
    // }
}

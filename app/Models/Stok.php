<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $table = 'stok';

    protected $fillable = [
        'kode_barang',
        'nama',
        'harga',
        'stok_awal',
        'stok_total',
        'tanggal_masuk',
        'tanggal_update',
        'sub_kategori',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'kode_barang', 'kode_barang');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'kode_barang', 'kode_barang');
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

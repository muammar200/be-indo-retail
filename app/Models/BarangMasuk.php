<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

    protected $fillable = [
        'kode_barang',
        'nama',
        'harga',
        'jumlah',
        'sub_kategori',
        'tanggal_masuk',
    ];

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'kode_barang', 'kode_barang');
    }


    //UNTUK SEEDER
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

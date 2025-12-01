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
}

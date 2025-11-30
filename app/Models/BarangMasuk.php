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
}

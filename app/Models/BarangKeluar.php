<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'kode_barang',
        'nama',
        'harga',
        'jumlah',
        'sub_kategori',
        'tanggal_keluar',
        'toko_tujuan',
    ];

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'kode_barang', 'kode_barang');
    }
}

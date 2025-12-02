<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';

    protected $fillable = [
        'nama_barang',
        'tanggal_permintaan',
        'jumlah_permintaan',
        'modal',
        'nomor_npwp',
    ];
}

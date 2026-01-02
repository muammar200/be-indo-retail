<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';  // Menentukan nama tabel yang digunakan untuk model ini, yaitu 'permintaan_barang'

    protected $fillable = [  // Mendefinisikan kolom-kolom yang dapat diisi melalui mass assignment
        'nama_barang',     // Nama barang yang diminta
        'tanggal_permintaan',  // Tanggal permintaan barang
        'jumlah_permintaan',   // Jumlah barang yang diminta
        'modal',            // Modal yang diperlukan untuk permintaan barang
        'nomor_npwp',       // Nomor NPWP yang terkait dengan permintaan barang
    ];
}


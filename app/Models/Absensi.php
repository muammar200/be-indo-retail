<?php

namespace App\Models;  // Menunjukkan bahwa kelas ini berada dalam namespace Models

use Illuminate\Database\Eloquent\Model;  // Mengimpor Model untuk Eloquent ORM
use Illuminate\Database\Eloquent\Relations\BelongsTo;  // Mengimpor BelongsTo untuk mendefinisikan relasi antar model

class Absensi extends Model  // Mendeklarasikan kelas model Absensi yang akan berinteraksi dengan tabel 'absensi' di database
{
    protected $table = 'absensi';  // Menentukan nama tabel yang digunakan dalam database

    protected $fillable = [  // Mendefinisikan field yang dapat diisi melalui mass assignment
        'user_id',  // ID pengguna yang melakukan absensi
        'tanggal',  // Tanggal absensi
        'status',  // Status kehadiran (Hadir, Izin, Sakit, dll.)
        'keterangan',  // Keterangan terkait absensi
        'waktu_checkin',  // Waktu check-in (jam masuk)
        'waktu_checkout',  // Waktu check-out (jam keluar)
        'image_proof',  // Bukti gambar absensi (misalnya foto)
        'kategori'  // Kategori absensi (misalnya Izin atau Sakit)
    ];

    protected $with = ['user'];  // Menentukan relasi yang akan dimuat secara otomatis ketika model Absensi dipanggil

    public function user(): BelongsTo  // Mendefinisikan relasi one-to-many antara Absensi dan User
    {
        return $this->belongsTo(User::class);  // Relasi ke model User (setiap Absensi dimiliki oleh satu User)
    }
}

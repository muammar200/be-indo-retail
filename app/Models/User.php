<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;  // Menggunakan kontrak JWTSubject untuk integrasi dengan JWT
use Illuminate\Notifications\Notifiable;  // Menyediakan fungsionalitas notifikasi untuk model
use Illuminate\Database\Eloquent\Factories\HasFactory;  // Menyediakan factory untuk pembuatan data model
use Illuminate\Foundation\Auth\User as Authenticatable;  // Menyediakan fungsionalitas autentikasi

class User extends Authenticatable implements JWTSubject  // Model User yang mengimplementasikan kontrak JWTSubject untuk otentikasi berbasis JWT
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;  // Menggunakan trait HasFactory untuk pembuatan data dan Notifiable untuk pengiriman notifikasi

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [  // Kolom yang dapat diisi secara massal (mass-assignment)
        'name',              // Nama pengguna
        'no_hp',             // Nomor telepon pengguna
        'jabatan',           // Jabatan pengguna
        'password',          // Password pengguna
        'otp',               // Kode OTP untuk autentikasi dua faktor
        'otp_expires_at',    // Waktu kadaluarsa OTP
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [  // Kolom yang tidak akan disertakan saat serialisasi (misalnya saat pengiriman response API)
        'password',          // Sembunyikan password
        'remember_token',    // Sembunyikan token untuk otentikasi yang berkelanjutan
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array  // Memastikan bahwa atribut tertentu dikendalikan dengan casting data yang sesuai
    {
        return [
            'password' => 'hashed',  // Pastikan password disimpan dalam bentuk hash
        ];
    }

    public function getJWTIdentifier()  // Metode yang mengembalikan ID pengguna untuk JWT
    {
        return $this->getKey();  // Mengembalikan nilai key utama (id) pengguna
    }

    public function getJWTCustomClaims()  // Mengembalikan klaim khusus JWT, saat ini kosong
    {
        return [];  // Tidak ada klaim kustom yang ditambahkan dalam JWT
    }
}

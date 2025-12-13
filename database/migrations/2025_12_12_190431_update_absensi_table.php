<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            DB::statement("ALTER TABLE absensi CHANGE status status ENUM('Tidak Hadir', 'Hadir', 'Terlambat', 'Hadir(Tidak Absen Pulang)', 'Izin', 'Sakit', 'Menunggu Konfirmasi') DEFAULT 'Tidak Hadir'");
        });

        DB::table('absensi')->where('status', 'Absen')->update(['status' => 'Tidak Hadir']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            DB::statement("ALTER TABLE absensi CHANGE status status ENUM('Absen', 'Hadir', 'Terlambat', 'Hadir(Tidak Absen Pulang)', 'Izin', 'Sakit', 'Menunggu Konfirmasi') DEFAULT 'Absen'");
        });

        DB::table('absensi')->where('status', 'Tidak Hadir')->update(['status' => 'Absen']);
    }
};

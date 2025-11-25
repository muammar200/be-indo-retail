<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', [
                'Absen',
                'Hadir',
                'Terlambat',
                'Hadir(Tidak Absen Pulang)',
                'Izin',
                'Sakit',
                'Menunggu Konfirmasi'
            ])->default('Absen');

            $table->time('waktu_checkin')->nullable();
            $table->time('waktu_checkout')->nullable();

            $table->string('image_proof')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            //
        });
    }
};

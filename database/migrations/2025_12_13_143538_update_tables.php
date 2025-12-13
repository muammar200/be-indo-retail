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
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->string('harga')->change();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->string('harga')->change();
        });

        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->string('modal')->change();
        });

        Schema::table('stok', function (Blueprint $table) {
            $table->string('harga')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->float('harga')->change();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->float('harga')->change();
        });

        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->float('modal')->change();
        });

        Schema::table('stok', function (Blueprint $table) {
            $table->float('harga')->change();
        });
    }
};

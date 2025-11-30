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
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama');
            $table->float('harga');
            $table->integer('jumlah');
            $table->string('sub_kategori');
            $table->date('tanggal_masuk');
            $table->timestamps();

            $table->foreign('kode_barang')->references('kode_barang')->on('stok')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};

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
        Schema::create('stok', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();  
            $table->string('nama');
            $table->float('harga');
            $table->integer('stok_awal')->default(0);  
            $table->integer('stok_total')->default(0); 
            $table->date('tanggal_masuk');
            $table->date('tanggal_update')->nullable(); 
            $table->string('sub_kategori');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};

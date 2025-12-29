<?php

namespace Database\Seeders;

use App\Models\BarangKeluar;
use App\Models\Stok;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BarangKeluarSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Ambil stok yang ada di tabel stok dengan id 1 sampai 100
        $stok = Stok::all();

        foreach ($stok as $item) {
            // Tentukan jumlah barang yang keluar, pastikan stok tidak kurang dari 100
            $jumlahKeluar = $faker->numberBetween(1, $item->stok_total);

            // Simpan data barang keluar
            BarangKeluar::create([
                'kode_barang' => $item->kode_barang,
                'nama' => $item->nama,
                'harga' => $item->harga,
                'sub_kategori' => $item->sub_kategori,
                'jumlah' => $jumlahKeluar,
                'tanggal_keluar' => Carbon::now()->format('Y-m-d'), // Menggunakan tanggal saat ini
                'toko_tujuan' => $faker->company,
            ]);

            // Kurangi stok barang yang keluar, pastikan stok tidak kurang dari 100
            // $newStokTotal = max(100, $item->stok_total - $jumlahKeluar);
            $newStokTotal = $item->stok_total - $jumlahKeluar;
            $item->update([
                'stok_total' => $newStokTotal,
            ]);
            // $item->save();
        }
    }
}

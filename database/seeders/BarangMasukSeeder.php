<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\Stok;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            Stok::create([
                'kode_barang' => $faker->word . $i,
                'nama' => $faker->word,
                'harga' => '10000',
                'stok_awal' => 100,
                'stok_total' => 100,
                // 'jumlah' => $faker->numberBetween(1, 50),
                'sub_kategori' => $faker->word,
                'tanggal_masuk' => $faker->date('Y-m-d'),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermintaanBarang;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PermintaanBarangSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Membuat 100 data permintaan barang
        for ($i = 0; $i < 100; $i++) {
            PermintaanBarang::create([
                'nama_barang' => $faker->word, // Nama barang acak
                'tanggal_permintaan' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'), // Tanggal permintaan acak dalam rentang 30 hari terakhir
                'jumlah_permintaan' => $faker->numberBetween(1, 50), // Jumlah permintaan acak antara 1 dan 50
                'modal' => $faker->randomFloat(2, 1000, 10000), // Modal acak dalam rentang 1000 sampai 10000
                'nomor_npwp' => $faker->bothify('##.###.###.#-###.###'), // Format acak untuk nomor NPWP
            ]);
        }
    }
}

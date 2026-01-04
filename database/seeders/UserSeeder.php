<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Alif',
            'no_hp' => '6289652558065',
            'jabatan' => 'Pimpinan',
            'password' => 'password'
        ]);
        // $faker = Faker::create();

        // for ($i = 105; $i < 150; $i++) {
        //     // Membuat data user menggunakan faker
        //     User::create([
        //         'name' => $faker->name,
        //         'no_hp' => $faker->phoneNumber,
        //         'jabatan' => $faker->randomElement(['Pimpinan', 'Staff', 'Karyawan Pelapor', 'Karyawan Biasa']),
        //         'password' => 'password',
        //     ]);
        // }
    }
}

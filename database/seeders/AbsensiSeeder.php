<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        // $statuses = ['Hadir', 'Tidak Hadir', 'Terlambat', 'Menunggu Konfirmasi'];
        // $userCount = 100;
        // $currentMonth = Carbon::now()->month;
        // $currentYear = Carbon::now()->year;  // Get the current year
        // $daysInMonth = Carbon::now()->daysInMonth;  // Get the number of days in the current month

        // // Loop through each user
        // for ($i = 1; $i <= $userCount; $i++) {
        //     // Loop through each day of the current month
        //     for ($day = 1; $day <= $daysInMonth; $day++) {
        //         // Create the date for the given day
        //         $tanggal = Carbon::create($currentYear, $currentMonth, $day);

        //         // Check if the day is a Sunday (Carbon::SUNDAY = 0)
        //         if ($tanggal->dayOfWeek == Carbon::SUNDAY) {
        //             continue; // Skip Sundays
        //         }

        //         // Random status
        //         $status = $statuses[array_rand($statuses)];

        //         // Random check-in and check-out times
        //         $waktu_checkin = null;
        //         $waktu_checkout = null;

        //         // If the status is 'Hadir' or 'Terlambat', generate check-in and check-out times
        //         if ($status === 'Hadir' || $status === 'Terlambat') {
        //             $waktu_checkin = Carbon::create($tanggal)->hour(rand(7, 9))->minute(rand(0, 59))->second(rand(0, 59));
        //             $waktu_checkout = Carbon::create($tanggal)->hour(rand(16, 18))->minute(rand(0, 59))->second(rand(0, 59));
        //         } else {
        //             $waktu_checkin = null;
        //             $waktu_checkout = null;
        //         }

        //         // If status is 'Tidak Hadir', no check-in/check-out is needed
        //         // $keterangan = $status === 'Tidak Hadir' ? 'Tidak masuk' : null;

        //         // Insert the record for the given user and day
        //         DB::table('absensi')->insert([
        //             'user_id' => $i,
        //             'tanggal' => $tanggal->format('Y-m-d'), // Format to YYYY-MM-DD
        //             'status' => $status,
        //             // 'keterangan' => $keterangan,
        //             'waktu_checkin' => $waktu_checkin,
        //             'waktu_checkout' => $waktu_checkout,
        //             'created_at' => Carbon::now(),
        //             'updated_at' => Carbon::now(),
        //         ]);
        //     }
        // }
        

        $status = ['Menunggu Konfirmasi'];
        $userCount = 133;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;  // Get the current year
        $daysInMonth = Carbon::now()->daysInMonth;  // Get the number of days in the current month

        // Loop through each user
        for ($i = 105; $i <= $userCount; $i++) {
            // Loop through each day of the current month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                // Create the date for the given day
                $tanggal = Carbon::create($currentYear, $currentMonth, $day);

                // Check if the day is a Sunday (Carbon::SUNDAY = 0)
                if ($tanggal->dayOfWeek == Carbon::SUNDAY) {
                    continue; // Skip Sundays
                }

                // Random status
                // $status = $statuses[array_rand($statuses)];

                // Random check-in and check-out times
                $waktu_checkin = null;
                $waktu_checkout = null;

                // Insert the record for the given user and day
                DB::table('absensi')->insert([
                    'user_id' => $i,
                    'tanggal' => $tanggal->format('Y-m-d'), // Format to YYYY-MM-DD
                    'status' => $status,
                    // 'keterangan' => $keterangan,
                    'waktu_checkin' => $waktu_checkin,
                    'waktu_checkout' => $waktu_checkout,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}

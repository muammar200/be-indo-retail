<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users 
            MODIFY jabatan ENUM(
                'Pimpinan',
                'Staff',
                'Karyawan Pelapor',
                'Karyawan Biasa'
            ) NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE users 
            MODIFY jabatan ENUM(
                'Pimpinan',
                'Staff',
                'Karyawan - Pelapor',
                'Karyawan - Biasa'
            ) NOT NULL
        ");
    }
};

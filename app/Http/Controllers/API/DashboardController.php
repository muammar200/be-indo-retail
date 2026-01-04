<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\PermintaanBarang;
use App\Models\Stok;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan ringkasan data dashboard.
     * Mengambil total pengguna, absensi hari ini, stok barang, dan permintaan barang.
     */
    public function dashboard()
    {
        // Menghitung total user yang terdaftar
        $totalUser = User::count();

        // Menghitung total absensi hari ini dengan status 'Hadir'
        $totalAbsensiHariIni = Absensi::where('tanggal', date('Y-m-d'))->count();

        // Menghitung total stok barang yang ada
        $totalBarang = Stok::sum('stok_total');

        // Menghitung total permintaan barang
        $totalPermintaanBarang = PermintaanBarang::sum('jumlah_permintaan');

        // Menyiapkan data untuk dikirim sebagai response
        $data = [
            'totalAbsensiHariIni' => $totalAbsensiHariIni,
            'totalUser' => $totalUser,
            'totalBarang' => $totalBarang,
            'totalPermintaanBarang' => $totalPermintaanBarang,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Dashboard Summary',
            'data' => $data,
        ], 200);
    }

    /**
     * Menampilkan grafik data barang masuk dan keluar per bulan dalam tahun ini.
     * Data ini digunakan untuk analisis tren barang masuk dan keluar.
     */
    public function chart()
    {
        // Menentukan tahun saat ini
        $year = date('Y');  

        // Menyusun nama-nama bulan untuk grafik
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Mengambil data barang masuk per bulan untuk tahun ini
        $dataBarangMasuk = BarangMasuk::selectRaw('MONTH(tanggal_masuk) as bulan, SUM(jumlah) as total_barang_masuk')
            ->whereYear('tanggal_masuk', '=', $year) 
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');  // Mengelompokkan hasil berdasarkan bulan

        // Menyusun hasil barang masuk per bulan
        $resultBarangMasuk = [];
        foreach ($bulan as $bulanIndex => $bulanName) {
            $total = isset($dataBarangMasuk[$bulanIndex]) ? (int) $dataBarangMasuk[$bulanIndex]->total_barang_masuk : 0;  
            $resultBarangMasuk[] = $total; // Menambahkan data untuk grafik
        }

        // Mengambil data barang keluar per bulan untuk tahun ini
        $dataBarangKeluar = BarangKeluar::selectRaw('MONTH(tanggal_keluar) as bulan, SUM(jumlah) as total_barang_keluar')
            ->whereYear('tanggal_keluar', '=', $year)  
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan'); // Mengelompokkan hasil berdasarkan bulan

        // Menyusun hasil barang keluar per bulan
        $resultBarangKeluar = [];
        foreach ($bulan as $bulanIndex => $bulanName) {
            $totalBarangKeluar = isset($dataBarangKeluar[$bulanIndex]) ? (int) $dataBarangKeluar[$bulanIndex]->total_barang_keluar : 0;
            $resultBarangKeluar[] = $totalBarangKeluar; // Menambahkan data untuk grafik
        }

        // Mengembalikan data dalam format JSON untuk ditampilkan pada grafik
        return response()->json([
            [
                'name' => 'barangMasuk',   // Data barang masuk
                'data' => $resultBarangMasuk,
            ],
            [
                'name' => 'barangKeluar',  // Data barang keluar
                'data' => $resultBarangKeluar,
            ],
        ], 200);
    }
}

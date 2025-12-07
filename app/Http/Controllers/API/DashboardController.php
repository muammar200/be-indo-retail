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
    public function dashboard()
    {
        $totalUser = User::count();
        $totalAbsensiHariIni = Absensi::where('tanggal', date('Y-m-d'))->where('status', 'Hadir')->count();
        $totalBarang = Stok::sum('stok_total');
        $totalPermintaanBarang = PermintaanBarang::sum('jumlah_permintaan');
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
    public function chart()
    {
        $year = date('Y');  

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

        // BARANG MASUK
        $dataBarangMasuk = BarangMasuk::selectRaw('MONTH(tanggal_masuk) as bulan, SUM(jumlah) as total_barang_masuk')
            ->whereYear('tanggal_masuk', '=', $year) 
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');


        $resultBarangMasuk = [];


        foreach ($bulan as $bulanIndex => $bulanName) {
            $total = isset($dataBarangMasuk[$bulanIndex]) ? (int) $dataBarangMasuk[$bulanIndex]->total_barang_masuk : 0;  
            $resultBarangMasuk[] = $total;
        }

        // BARANG KELUAR
        $dataBarangKeluar = BarangKeluar::selectRaw('MONTH(tanggal_keluar) as bulan, SUM(jumlah) as total_barang_keluar')
            ->whereYear('tanggal_keluar', '=', $year)  
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan'); 

        $resultBarangKeluar = [];

        foreach ($bulan as $bulanIndex => $bulanName) {
            $totalBarangKeluar = isset($dataBarangKeluar[$bulanIndex]) ? (int) $dataBarangKeluar[$bulanIndex]->total_barang_keluar : 0;
            $resultBarangKeluar[] = $totalBarangKeluar;
        }

        return response()->json([
                [
                    'name' => 'barangMasuk',
                    'data' => $resultBarangMasuk,
                ],
                [
                    'name' => 'barangKeluar',
                    'data' => $resultBarangKeluar,
                ],
            ], 200);
    }
}

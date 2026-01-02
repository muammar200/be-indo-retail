<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BarangKeluarResource;
use App\Http\Resources\MetaPaginateResource;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class LaporanBarangKeluarController extends Controller
{
    /**
     * Menampilkan daftar laporan barang keluar dengan fitur pencarian dan pagination.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Ambil parameter pagination dan pencarian dari request
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Filter barang keluar berdasarkan pencarian di beberapa field
        $barang_keluar = BarangKeluar::latest()
            ->where('nama', 'LIKE', "%$search%")
            ->orWhere('kode_barang', 'LIKE', "%$search%")
            ->orWhere('harga', 'LIKE', "%$search%")
            ->orWhere('jumlah', 'LIKE', "%$search%")
            ->orWhere('sub_kategori', 'LIKE', "%$search%")
            ->orWhere('tanggal_keluar', 'LIKE', "%$search%")
            ->orWhere('toko_tujuan', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        // Format response data
        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Keluar Success',
            'meta' => new MetaPaginateResource($barang_keluar),
            'data' => BarangKeluarResource::collection($barang_keluar),
        ];

        // Kembalikan response JSON
        return response()->json($data, 200);
    }

    /**
     * Menampilkan detail laporan barang keluar berdasarkan ID.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Cari laporan barang keluar berdasarkan ID
        $laporanBarangKeluar = BarangKeluar::find($id);

        // Jika laporan tidak ditemukan, kembalikan response 404
        if (! $laporanBarangKeluar) {
            return response()->json([
                'status' => false,
                'message' => 'Laporan Barang Keluar tidak ditemukan',
            ], 404);
        }

        // Format response data
        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Keluar Success',
            'data' => new BarangKeluarResource($laporanBarangKeluar),
        ];

        // Kembalikan response JSON
        return response()->json($data, 200);
    }

    /**
     * Membuat laporan barang keluar baru (Saat ini belum diimplementasikan).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Fitur ini belum diimplementasikan
        return response()->json([
            'status' => false,
            'message' => 'Fitur ini belum tersedia',
        ], 400);
    }

    /**
     * Mengupdate laporan barang keluar berdasarkan ID (Saat ini belum diimplementasikan).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Fitur ini belum diimplementasikan
        return response()->json([
            'status' => false,
            'message' => 'Fitur ini belum tersedia',
        ], 400);
    }

    /**
     * Menghapus laporan barang keluar berdasarkan ID (Saat ini belum diimplementasikan).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        // Fitur ini belum diimplementasikan
        return response()->json([
            'status' => false,
            'message' => 'Fitur ini belum tersedia',
        ], 400);
    }

    /**
     * Mencetak laporan barang keluar berdasarkan ID yang dipilih.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cetakLaporanBarangKeluar(Request $request)
    {
        try {
            // Ambil array ID laporan barang keluar dari request
            $ids = json_decode($request->ids, true);

            // Ambil laporan barang keluar berdasarkan ID yang dipilih
            $barang_keluar = BarangKeluar::whereIn('id', $ids)->latest()->get();

            // Format response data
            $data = [
                'status' => true,
                'message' => 'Cetak Laporan Barang Keluar Success',
                'data' => BarangKeluarResource::collection($barang_keluar),
            ];

            // Kembalikan response JSON
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            // Jika terjadi error, kembalikan response error
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

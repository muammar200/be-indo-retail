<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BarangMasukResource;
use App\Http\Resources\MetaPaginateResource;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class LaporanBarangMasukController extends Controller
{
    /**
     * Menampilkan daftar laporan barang masuk dengan fitur pencarian dan pagination.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Ambil parameter pagination dan pencarian dari request
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Filter barang masuk berdasarkan pencarian di beberapa field
        $barang_masuk = BarangMasuk::latest()
            ->where('nama', 'LIKE', "%$search%")
            ->orWhere('kode_barang', 'LIKE', "%$search%")
            ->orWhere('harga', 'LIKE', "%$search%")
            ->orWhere('jumlah', 'LIKE', "%$search%")
            ->orWhere('sub_kategori', 'LIKE', "%$search%")
            ->orWhere('tanggal_masuk', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        // Format response data
        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Masuk Success',
            'meta' => new MetaPaginateResource($barang_masuk),
            'data' => BarangMasukResource::collection($barang_masuk),
        ];

        // Kembalikan response JSON
        return response()->json($data, 200);
    }

    /**
     * Menampilkan detail laporan barang masuk berdasarkan ID.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Cari laporan barang masuk berdasarkan ID
        $laporanBarangMasuk = BarangMasuk::find($id);
        
        // Jika laporan tidak ditemukan, kembalikan response 404
        if (!$laporanBarangMasuk) {
            return response()->json([
                'status' => false,
                'message' => 'Laporan Barang Masuk tidak ditemukan',
            ], 404);
        }

        // Format response data
        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Masuk Success',
            'data' => new BarangMasukResource($laporanBarangMasuk),
        ];

        // Kembalikan response JSON
        return response()->json($data, 200);
    }

    /**
     * Membuat laporan barang masuk baru (Saat ini belum diimplementasikan).
     * 
     * @param Request $request
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
     * Mengupdate laporan barang masuk berdasarkan ID (Saat ini belum diimplementasikan).
     * 
     * @param Request $request
     * @param string $id
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
     * Menghapus laporan barang masuk berdasarkan ID (Saat ini belum diimplementasikan).
     * 
     * @param string $id
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
     * Mencetak laporan barang masuk berdasarkan ID yang dipilih.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cetakLaporanBarangMasuk(Request $request)
    {
        try {
            // Ambil array ID laporan barang masuk dari request
            $ids = json_decode($request->ids, true);

            // Ambil laporan barang masuk berdasarkan ID yang dipilih
            $barang_masuk = BarangMasuk::whereIn('id', $ids)->latest()->get();

            // Format response data
            $data = [
                'status' => true,
                'message' => 'Cetak Laporan Barang Masuk Success',
                'data' => BarangMasukResource::collection($barang_masuk),
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

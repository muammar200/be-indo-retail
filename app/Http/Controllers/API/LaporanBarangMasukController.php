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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        $barang_masuk = BarangMasuk::latest()->where('nama', 'LIKE', "%$search%")->orWhere('kode_barang', 'LIKE', "%$search%")->orWhere('harga', 'LIKE', "%$search%")->orWhere('jumlah', 'LIKE', "%$search%")->orWhere('sub_kategori', 'LIKE', "%$search%")->orWhere('tanggal_masuk', 'LIKE', "%$search%")->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Masuk Success',
            'meta' => new MetaPaginateResource($barang_masuk),
            'data' => BarangMasukResource::collection($barang_masuk),
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $laporanBarangMasuk = BarangMasuk::find($id);
        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Masuk Success',
            'data' => new BarangMasukResource($laporanBarangMasuk),
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cetakLaporanBarangMasuk(Request $request)
    {
        try {
            $ids = json_decode($request->ids, true);

            $barang_masuk = BarangMasuk::whereIn('id', $ids)->latest()->get();

            $data = [
                'status' => true,
                'message' => 'Cetak Laporan Barang Masuk Success',
                'data' => BarangMasukResource::collection($barang_masuk),
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

    }
}

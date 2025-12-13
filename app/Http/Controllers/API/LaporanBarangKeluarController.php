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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        $barang_keluar = BarangKeluar::latest()->where('nama', 'LIKE', "%$search%")->orWhere('kode_barang', 'LIKE', "%$search%")->orWhere('harga', 'LIKE', "%$search%")->orWhere('jumlah', 'LIKE', "%$search%")->orWhere('sub_kategori', 'LIKE', "%$search%")->orWhere('tanggal_keluar', 'LIKE', "%$search%")->orWhere('toko_tujuan', 'LIKE', "%$search%")->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Keluar Success',
            'meta' => new MetaPaginateResource($barang_keluar),
            'data' => BarangKeluarResource::collection($barang_keluar),
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
        $laporanBarangKeluar = BarangKeluar::find($id);
        $data = [
            'status' => true,
            'message' => 'Show Laporan Barang Keluar Success',
            'data' => new BarangKeluarResource($laporanBarangKeluar),
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
    
    public function cetakLaporanBarangKeluar(Request $request)
    {
        try{
            $ids = json_decode($request->ids, true);

            $barang_keluar = BarangKeluar::whereIn('id', $ids)->latest()->get();

            $data = [
                'status' => true,
                'message' => 'Cetak Laporan Barang Keluar Success',
                'data' => BarangKeluarResource::collection($barang_keluar),
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

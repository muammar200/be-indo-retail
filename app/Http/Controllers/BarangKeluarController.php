<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangKeluarRequest;
use App\Http\Resources\BarangKeluarResource;
use App\Http\Resources\MetaPaginateResource;
use App\Models\BarangKeluar;
use App\Models\Stok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
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
            'message' => 'Show Barang Keluar Success',
            'meta' => new MetaPaginateResource($barang_keluar),
            'data' => BarangKeluarResource::collection($barang_keluar),
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BarangKeluarRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_keluar'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_keluar'])->format('Y-m-d');

        try {
            // Begin the transaction
            DB::beginTransaction();

            // Find and update stock
            $stok = Stok::findOrFail($request->barang_id);
            $stok->stok_total -= $request->jumlah;
            $stok->tanggal_update = now();
            $stok->save();

            // Create BarangKeluar
            $barangKeluar = BarangKeluar::create([
                'kode_barang' => $stok->kode_barang,
                'nama' => $stok->nama,
                'harga' => $stok->harga,
                'jumlah' => $request->jumlah,
                'sub_kategori' => $stok->sub_kategori,
                'tanggal_keluar' => $validatedData['tanggal_keluar'],
                'toko_tujuan' => $request->toko_tujuan,
            ]);

            // Commit the transaction
            DB::commit();

            // Return success response
            $data = [
                'status' => true,
                'message' => 'Create Barang Keluar Success',
                'data' => new BarangKeluarResource($barangKeluar),
            ];

            return response()->json($data, 201);
        } catch (\Throwable $th) {
            // Rollback transaction on error
            DB::rollBack();

            // Return error response
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $data = [
            'status' => true,
            'message' => 'Get Barang Keluar by Id',
            'data' => new BarangKeluarResource($barangKeluar),
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BarangKeluarRequest $request, $id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $validatedData = $request->validated();
        $validatedData['tanggal_keluar'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_keluar'])->format('Y-m-d');
        try {
            DB::beginTransaction();
            $stok = Stok::where('kode_barang', $barangKeluar->kode_barang)->first();
            if ($stok) {
                $stok->stok_total += $barangKeluar->jumlah;
                $stok->stok_total -= $request->jumlah;
                $stok->tanggal_update = now();
                $stok->save();
            }

            $barangKeluar->update([
                'jumlah' => $request->jumlah,
                'tanggal_keluar' => $validatedData['tanggal_keluar'],
                'toko_tujuan' => $request->toko_tujuan,
            ]);

            DB::commit();

            $data = [
                'status' => true,
                'message' => 'Update Barang Keluar Success',
                'data' => new BarangKeluarResource($barangKeluar),
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barangKeluar = BarangKeluar::find($id);
        try {
            DB::beginTransaction();

            $stok = Stok::where('kode_barang', $barangKeluar->kode_barang)->first();
            if ($stok) {
                $stok->stok_total += $barangKeluar->jumlah;
                $stok->tanggal_update = now();
                $stok->save();
            }

            $barangKeluar->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Delete Barang Keluar Success',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

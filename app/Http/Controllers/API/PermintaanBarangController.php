<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermintaanBarangRequest;
use App\Http\Resources\MetaPaginateResource;
use App\Http\Resources\PermintaanBarangResource;
use App\Models\PermintaanBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PermintaanBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        $permintaan_barang = PermintaanBarang::latest()->where('nama_barang', 'LIKE', "%$search%")->orWhere('tanggal_permintaan', 'LIKE', "%$search%")->orWhere('jumlah_permintaan', 'LIKE', "%$search%")->orWhere('modal', 'LIKE', "%$search%")->orWhere('nomor_npwp', 'LIKE', "%$search%")->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Permintaan Barang Success',
            'meta' => new MetaPaginateResource($permintaan_barang),
            'data' => PermintaanBarangResource::collection($permintaan_barang),
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermintaanBarangRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_permintaan'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_permintaan'])->format('Y-m-d');
        try {

            $permintaanBarang = PermintaanBarang::create($validatedData);

            $data = [
                'status' => true,
                'message' => 'Create Permintaan Barang Success',
                'data' => new PermintaanBarangResource($permintaanBarang),
            ];

            return response()->json($data, 201);
        } catch (\Throwable $th) {
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
        $permintaanBarang = PermintaanBarang::find($id);
        $data = [
            'status' => true,
            'message' => 'Get Permintaan Barang by Id',
            'data' => new PermintaanBarangResource($permintaanBarang),
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermintaanBarangRequest $request, $id)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_permintaan'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_permintaan'])->format('Y-m-d');
        $permintaanBarang = PermintaanBarang::find($id);
        try {

            $permintaanBarang->update($validatedData);

            $data = [
                'status' => true,
                'message' => 'Update Permintaan Barang Success',
                'data' => new PermintaanBarangResource($permintaanBarang),
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
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
        $permintaanBarang = PermintaanBarang::find($id);
        try {
            $permintaanBarang->delete();

            return response()->json([
                'status' => true,
                'message' => 'Delete Permintaan Barang Success',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function cetakPermintaanBarang()
    {
        $data = PermintaanBarang::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Cetak Permintaan Barang Success',
            'data' => PermintaanBarangResource::collection($data),
        ], 200);
    }
}

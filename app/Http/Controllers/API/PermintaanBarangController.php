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
     * Menampilkan daftar permintaan barang dengan fitur pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Menambahkan filter pencarian di berbagai kolom
        $permintaan_barang = PermintaanBarang::latest()
            ->where('nama_barang', 'LIKE', "%$search%")
            ->orWhere('tanggal_permintaan', 'LIKE', "%$search%")
            ->orWhere('jumlah_permintaan', 'LIKE', "%$search%")
            ->orWhere('modal', 'LIKE', "%$search%")
            ->orWhere('nomor_npwp', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Permintaan Barang Success',
            'meta' => new MetaPaginateResource($permintaan_barang),
            'data' => PermintaanBarangResource::collection($permintaan_barang),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menyimpan permintaan barang baru.
     */
    public function store(PermintaanBarangRequest $request)
    {
        // Validasi request dan format tanggal permintaan
        $validatedData = $request->validated();
        $validatedData['tanggal_permintaan'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_permintaan'])->format('Y-m-d');

        try {
            // Membuat entri permintaan barang baru
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
     * Menampilkan detail permintaan barang berdasarkan ID.
     */
    public function show($id)
    {
        $permintaanBarang = PermintaanBarang::find($id);

        // Jika permintaan barang tidak ditemukan
        if (! $permintaanBarang) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan Barang tidak ditemukan',
            ], 404);
        }

        $data = [
            'status' => true,
            'message' => 'Get Permintaan Barang by Id',
            'data' => new PermintaanBarangResource($permintaanBarang),
        ];

        return response()->json($data, 200);
    }

    /**
     * Mengupdate permintaan barang berdasarkan ID.
     */
    public function update(PermintaanBarangRequest $request, $id)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_permintaan'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_permintaan'])->format('Y-m-d');

        $permintaanBarang = PermintaanBarang::find($id);

        // Jika permintaan barang tidak ditemukan
        if (! $permintaanBarang) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan Barang tidak ditemukan',
            ], 404);
        }

        try {
            // Melakukan update data permintaan barang
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
     * Menghapus permintaan barang berdasarkan ID.
     */
    public function destroy($id)
    {
        $permintaanBarang = PermintaanBarang::find($id);

        // Jika permintaan barang tidak ditemukan
        if (! $permintaanBarang) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan Barang tidak ditemukan',
            ], 404);
        }

        try {
            // Menghapus permintaan barang
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

    /**
     * Menampilkan seluruh data permintaan barang untuk dicetak.
     */
    public function cetakPermintaanBarang()
    {
        // Mengambil seluruh permintaan barang
        $data = PermintaanBarang::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Cetak Permintaan Barang Success',
            'data' => PermintaanBarangResource::collection($data),
        ], 200);
    }
}

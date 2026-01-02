<?php

namespace App\Http\Controllers;

use App\Http\Resources\MetaPaginateResource;
use App\Http\Resources\PermintaanBarangResource;
use App\Http\Resources\StokResource;
use App\Models\PermintaanBarang;
use App\Models\Stok;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StokController extends Controller
{
    /**
     * Menampilkan daftar stok dengan pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        $stok = Stok::latest()->where('nama', 'LIKE', "%$search%")
            ->orWhere('kode_barang', 'LIKE', "%$search%")
            ->orWhere('harga', 'LIKE', "%$search%")
            ->orWhere('stok_awal', 'LIKE', "%$search%")
            ->orWhere('stok_total', 'LIKE', "%$search%")
            ->orWhere('tanggal_masuk', 'LIKE', "%$search%")
            ->orWhere('tanggal_update', 'LIKE', "%$search%")
            ->orWhere('sub_kategori', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Stok Success',
            'meta' => new MetaPaginateResource($stok),
            'data' => StokResource::collection($stok),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menampilkan semua stok tanpa paginasi, untuk dropdown misalnya.
     */
    public function allStok()
    {
        $stok = Stok::latest()->get();

        $data = [
            'status' => true,
            'message' => 'Show Stok Success For Dropdwon',
            'data' => StokResource::collection($stok),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menampilkan stok berdasarkan ID.
     */
    public function show($id)
    {
        $stok = Stok::find($id);
        $data = [
            'status' => true,
            'message' => 'Get Stok by Id',
            'data' => new StokResource($stok),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menampilkan stok yang memiliki jumlah lebih dari 0 untuk dicetak.
     */
    public function cetakStok()
    {
        $stok = Stok::where('stok_total', '>', 0)->get();

        $data = [
            'status' => true,
            'message' => 'Cetak Stok Success',
            'data' => StokResource::collection($stok),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menangani permintaan barang dengan validasi input.
     */
    public function permintaanStok(Request $request)
    {
        $validatedData = $request->validate([
            'stok_id' => 'required|exists:stok,id',
            'tanggal_permintaan' => 'required|date_format:d-m-Y',
            'jumlah_permintaan' => 'required|integer',
            'modal' => 'required|numeric',
            'nomor_npwp' => 'required|string',
        ], [
            'stok_id.required' => 'ID stok harus diisi.',
            'stok_id.exists' => 'Stok dengan ID yang diberikan tidak ditemukan.',
            'tanggal_permintaan.required' => 'Tanggal permintaan harus diisi.',
            'tanggal_permintaan.date_format' => 'Tanggal masuk barang harus menggunakan format dd-mm-yyyy.',
            'jumlah_permintaan.required' => 'Jumlah permintaan harus diisi.',
            'jumlah_permintaan.integer' => 'Jumlah permintaan harus berupa angka bulat.',
            'modal.required' => 'Modal harus diisi.',
            'modal.numeric' => 'Modal harus berupa angka.',
            'nomor_npwp.required' => 'Nomor NPWP harus diisi.',
            'nomor_npwp.string' => 'Nomor NPWP harus berupa string.',
        ]);

        $permintaanBarang = PermintaanBarang::create([
            'nama_barang' => Stok::find($validatedData['stok_id'])->nama,
            'tanggal_permintaan' => Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_permintaan'])->format('Y-m-d'),
            'jumlah_permintaan' => $validatedData['jumlah_permintaan'],
            'modal' => $validatedData['modal'],
            'nomor_npwp' => $validatedData['nomor_npwp'],
        ]);

        $data = [
            'status' => true,
            'message' => 'Create Permintaan Barang Success',
            'data' => new PermintaanBarangResource($permintaanBarang),
        ];

        return response()->json($data, 201);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BarangMasukRequest;
use App\Http\Resources\BarangMasukResource;
use App\Http\Resources\MetaPaginateResource;
use App\Models\BarangMasuk;
use App\Models\Stok;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
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
            'message' => 'Show Barang Masuk Success',
            'meta' => new MetaPaginateResource($barang_masuk),
            'data' => BarangMasukResource::collection($barang_masuk),
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BarangMasukRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_masuk'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_masuk'])->format('Y-m-d');
        try {
            $stok = Stok::where('kode_barang', $request->kode_barang)->first();

            if ($stok) {
                $stok->stok_total += $request->jumlah;
                $stok->tanggal_update = now();

                // if needed, update other fields
                $stok->nama = $request->nama;
                $stok->harga = $request->harga;
                $stok->sub_kategori = $request->sub_kategori;
                $stok->save();

                $barangMasuk = BarangMasuk::create($validatedData);

                $data = [
                    'status' => true,
                    'message' => 'Create Barang Masuk Success dan Stok Diperbarui',
                    'data' => new BarangMasukResource($barangMasuk),
                ];
            } else {
                Stok::create([
                    'kode_barang' => $request->kode_barang,
                    'nama' => $request->nama,
                    'harga' => $request->harga,
                    'stok_awal' => $request->jumlah,
                    'stok_total' => $request->jumlah,
                    'tanggal_masuk' => $validatedData['tanggal_masuk'],
                    'sub_kategori' => $request->sub_kategori,
                    'tanggal_update' => now(),
                ]);

                $barangMasuk = BarangMasuk::create($validatedData);

                $data = [
                    'status' => true,
                    'message' => 'Create Barang Masuk Success',
                    'data' => new BarangMasukResource($barangMasuk),
                ];
            }

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
    public function show(BarangMasuk $id)
    {
        $barangMasuk = BarangMasuk::find($id);
        $data = [
            'status' => true,
            'message' => 'Get Barang Masuk by Id',
            'data' => new BarangMasukResource($barangMasuk),
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BarangMasukRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_masuk'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_masuk'])->format('Y-m-d');

        try {
            $barangMasuk = BarangMasuk::find($id);
            $cekBarangMasuk = BarangMasuk::where('kode_barang', $barangMasuk->kode_barang)->count();

            // jika hanya 1 data barang masuk dengan kode barang yang sama
            // maka hapus data stok lama dan buat data stok baru
            if ($cekBarangMasuk == 1) {
                $stok = Stok::where('kode_barang', $barangMasuk->kode_barang)->first();
                $stok->delete();

                Stok::create([
                    'kode_barang' => $request->kode_barang,
                    'nama' => $request->nama,
                    'harga' => $request->harga,
                    'stok_awal' => $request->jumlah,
                    'stok_total' => $request->jumlah,
                    'tanggal_masuk' => $validatedData['tanggal_masuk'],
                    'sub_kategori' => $request->sub_kategori,
                    'tanggal_update' => now(),
                ]);

                $barangMasukBaru = BarangMasuk::create($validatedData);

                $data = [
                    'status' => true,
                    'message' => 'Update Barang Masuk Success Dengan Cara Menambah Data Stok Baru',
                    'data' => new BarangMasukResource($barangMasukBaru),
                ];

                return response()->json($data, 201);
                dd('0');
            } elseif ($cekBarangMasuk > 1) {
                $cekBarangMasuk = BarangMasuk::where('kode_barang', $barangMasuk->kode_barang)->first();

                // jika kode barang yang diupdate sama dengan kode barang lama
                // maka update data stok lama
                if ($cekBarangMasuk->kode_barang == $request->kode_barang) {
                    $stok = Stok::where('kode_barang', $request->kode_barang)->first();
                    $stok->stok_total -= $barangMasuk->jumlah;
                    $stok->stok_total += $request->jumlah;
                    $stok->tanggal_update = now();
                    $stok->nama = $request->nama;
                    $stok->harga = $request->harga;
                    $stok->sub_kategori = $request->sub_kategori;
                    $stok->save();

                    $barangMasuk->update($validatedData);

                    $barangMasukUpdate = BarangMasuk::find($id);

                    $data = [
                        'status' => true,
                        'message' => 'Update Barang Masuk Success',
                        'data' => new BarangMasukResource($barangMasukUpdate),
                    ];

                    return response()->json($data, 200);
                } else {
                    // jika kode barang yang diupdate berbeda dengan kode barang lama
                    // maka kurangi stok lama dan buat data stok baru
                    $stok = Stok::where('kode_barang', $barangMasuk->kode_barang)->first();
                    $stok->stok_total -= $barangMasuk->jumlah;
                    $stok->tanggal_update = now();
                    $stok->save();

                    Stok::create([
                        'kode_barang' => $request->kode_barang,
                        'nama' => $request->nama,
                        'harga' => $request->harga,
                        'stok_awal' => $request->jumlah,
                        'stok_total' => $request->jumlah,
                        'tanggal_masuk' => $validatedData['tanggal_masuk'],
                        'sub_kategori' => $request->sub_kategori,
                        'tanggal_update' => now(),
                    ]);

                    $barangMasukBaru = BarangMasuk::create($validatedData);

                    $data = [
                        'status' => true,
                        'message' => 'Update Barang Masuk Success Dengan Cara Menambah Data Stok Baru',
                        'data' => new BarangMasukResource($barangMasukBaru),
                    ];

                    return response()->json($data, 201);
                }
            }
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
    public function destroy(string $id)
    {
        try {
            $barangMasuk = BarangMasuk::find($id);
            $cekBarangMasuk = BarangMasuk::where('kode_barang', $barangMasuk->kode_barang)->count();

            $stok = Stok::where('kode_barang', $barangMasuk->kode_barang)->first();

            if ($cekBarangMasuk == 1) {
                // jika hanya 1 data barang masuk dengan kode barang yang sama
                // maka hapus data stok juga
                $stok->delete();
                $barangMasuk->delete();

                $data = [
                    'status' => true,
                    'message' => 'Delete Barang Masuk Success dan Stok Dihapus',
                ];
            } else {
                // jika lebih dari 1 data barang masuk dengan kode barang yang sama
                // maka kurangi stok sesuai jumlah barang masuk yang dihapus
                $stok->stok_total -= $barangMasuk->jumlah;
                $stok->tanggal_update = now();
                $stok->save();

                $barangMasuk->delete();

                $data = [
                    'status' => true,
                    'message' => 'Delete Barang Masuk Success',
                ];
            }

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

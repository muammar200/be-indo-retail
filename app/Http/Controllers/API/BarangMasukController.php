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
     * Menampilkan daftar barang masuk dengan pagination dan pencarian.
     */
    public function index(Request $request)
    {
        // Ambil parameter halaman, jumlah per halaman, dan kata kunci pencarian
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Ambil data barang masuk dengan pencarian berdasarkan beberapa field
        $barang_masuk = BarangMasuk::latest()
            ->where('nama', 'LIKE', "%$search%")
            ->orWhere('kode_barang', 'LIKE', "%$search%")
            ->orWhere('harga', 'LIKE', "%$search%")
            ->orWhere('jumlah', 'LIKE', "%$search%")
            ->orWhere('sub_kategori', 'LIKE', "%$search%")
            ->orWhere('tanggal_masuk', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        // Format data response dengan resource
        $data = [
            'status' => true,
            'message' => 'Show Barang Masuk Success',
            'meta' => new MetaPaginateResource($barang_masuk),
            'data' => BarangMasukResource::collection($barang_masuk),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menyimpan data barang masuk baru dan memperbarui stok.
     */
    public function store(BarangMasukRequest $request)
    {
        $validatedData = $request->validated();
        // Format tanggal masuk menjadi format Y-m-d
        $validatedData['tanggal_masuk'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_masuk'])->format('Y-m-d');

        try {
            // Cek apakah barang sudah ada di stok
            $stok = Stok::where('kode_barang', $request->kode_barang)->first();

            if ($stok) {
                // Jika ada, update stok dan simpan perubahan
                $stok->stok_total += $request->jumlah;
                $stok->tanggal_update = now();
                $stok->nama = $request->nama;
                $stok->harga = $request->harga;
                $stok->sub_kategori = $request->sub_kategori;
                $stok->save();

                // Simpan data barang masuk
                $barangMasuk = BarangMasuk::create($validatedData);

                $data = [
                    'status' => true,
                    'message' => 'Create Barang Masuk Success dan Stok Diperbarui',
                    'data' => new BarangMasukResource($barangMasuk),
                ];
            } else {
                // Jika tidak ada, buat entri stok baru
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

                // Simpan data barang masuk
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
     * Menampilkan data barang masuk berdasarkan ID.
     */
    public function show($id)
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
     * Memperbarui data barang masuk dan stok terkait.
     */
    public function update(BarangMasukRequest $request, string $id)
    {
        $validatedData = $request->validated();
        // Format tanggal masuk menjadi format Y-m-d
        $validatedData['tanggal_masuk'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_masuk'])->format('Y-m-d');

        try {
            // Ambil data barang masuk yang ingin diupdate
            $barangMasuk = BarangMasuk::find($id);
            $cekBarangMasuk = BarangMasuk::where('kode_barang', $barangMasuk->kode_barang)->count();

            // Jika hanya 1 data barang masuk dengan kode barang yang sama
            if ($cekBarangMasuk == 1) {
                // Hapus stok lama dan buat stok baru
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
            } elseif ($cekBarangMasuk > 1) {
                $cekBarangMasuk = BarangMasuk::where('kode_barang', $barangMasuk->kode_barang)->first();

                // Jika kode barang yang diupdate sama dengan kode barang lama
                if ($cekBarangMasuk->kode_barang == $request->kode_barang) {
                    // Update stok lama
                    $stok = Stok::where('kode_barang', $request->kode_barang)->first();
                    $stok->stok_total -= $barangMasuk->jumlah;
                    $stok->stok_total += $request->jumlah;
                    $stok->tanggal_update = now();
                    $stok->nama = $request->nama;
                    $stok->harga = $request->harga;
                    $stok->sub_kategori = $request->sub_kategori;
                    $stok->save();

                    // Update barang masuk
                    $barangMasuk->update($validatedData);

                    $barangMasukUpdate = BarangMasuk::find($id);

                    $data = [
                        'status' => true,
                        'message' => 'Update Barang Masuk Success',
                        'data' => new BarangMasukResource($barangMasukUpdate),
                    ];

                    return response()->json($data, 200);
                } else {
                    // Jika kode barang berbeda, update stok lama dan buat stok baru
                    $stok = Stok::where('kode_barang', $barangMasuk->kode_barang)->first();
                    $stok->stok_total -= $barangMasuk->jumlah;
                    $stok->tanggal_update = now();
                    $stok->save();

                    // Buat stok baru
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
     * Menghapus data barang masuk dan menyesuaikan stok.
     */
    public function destroy(string $id)
    {
        try {
            // Ambil data barang masuk berdasarkan ID
            $barangMasuk = BarangMasuk::find($id);

            // Hitung jumlah data barang masuk dengan kode barang yang sama
            $cekBarangMasuk = BarangMasuk::where('kode_barang', $barangMasuk->kode_barang)->count();

            // Ambil data stok berdasarkan kode barang
            $stok = Stok::where('kode_barang', $barangMasuk->kode_barang)->first();

            if ($cekBarangMasuk == 1) {
                // Jika hanya ada 1 data barang masuk
                // maka hapus stok dan data barang masuk
                $stok->delete();
                $barangMasuk->delete();

                $data = [
                    'status' => true,
                    'message' => 'Delete Barang Masuk Success dan Stok Dihapus',
                ];
            } else {
                // Jika ada lebih dari 1 data barang masuk
                // maka kurangi stok sesuai jumlah barang masuk yang dihapus
                $stok->stok_total -= $barangMasuk->jumlah;
                $stok->tanggal_update = now();
                $stok->save();

                // Hapus data barang masuk
                $barangMasuk->delete();

                $data = [
                    'status' => true,
                    'message' => 'Delete Barang Masuk Success',
                ];
            }

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            // Tangani error jika terjadi kesalahan
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

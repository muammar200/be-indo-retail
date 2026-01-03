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
     * Menampilkan daftar barang keluar dengan pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Melakukan pencarian pada berbagai kolom
        $barang_keluar = BarangKeluar::latest()
            ->where('nama', 'LIKE', "%$search%")
            ->orWhere('kode_barang', 'LIKE', "%$search%")
            ->orWhere('harga', 'LIKE', "%$search%")
            ->orWhere('jumlah', 'LIKE', "%$search%")
            ->orWhere('sub_kategori', 'LIKE', "%$search%")
            ->orWhere('tanggal_keluar', 'LIKE', "%$search%")
            ->orWhere('toko_tujuan', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Barang Keluar Success',
            'meta' => new MetaPaginateResource($barang_keluar),
            'data' => BarangKeluarResource::collection($barang_keluar),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menyimpan data barang keluar baru.
     */
    public function store(BarangKeluarRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['tanggal_keluar'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_keluar'])->format('Y-m-d');

        try {
            // Memulai transaksi
            DB::beginTransaction();

            // Menemukan stok barang berdasarkan ID
            $stok = Stok::findOrFail($request->barang_id);

            // Validasi jika jumlah yang keluar lebih besar dari stok yang tersedia
            if ($request->jumlah > $stok->stok_total) {
                // Rollback transaksi dan kirim pesan error
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Jumlah barang yang dikeluarkan melebihi jumlah stok yang tersedia.',
                ], 400); // Kode status 400 untuk Bad Request
            }

            // Mengurangi stok sesuai dengan jumlah yang dikeluarkan
            $stok->stok_total -= $request->jumlah;
            $stok->tanggal_update = now();
            $stok->save();

            // Membuat record BarangKeluar
            $barangKeluar = BarangKeluar::create([
                'kode_barang' => $stok->kode_barang,
                'nama' => $stok->nama,
                'harga' => $stok->harga,
                'jumlah' => $request->jumlah,
                'sub_kategori' => $stok->sub_kategori,
                'tanggal_keluar' => $validatedData['tanggal_keluar'],
                'toko_tujuan' => $request->toko_tujuan,
            ]);

            // Commit transaksi
            DB::commit();

            $data = [
                'status' => true,
                'message' => 'Create Barang Keluar Success',
                'data' => new BarangKeluarResource($barangKeluar),
            ];

            return response()->json($data, 201);
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan data barang keluar berdasarkan ID.
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
     * Mengupdate data barang keluar.
     */
    public function update(BarangKeluarRequest $request, $id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $validatedData = $request->validated();
        $validatedData['tanggal_keluar'] = Carbon::createFromFormat('d-m-Y', $validatedData['tanggal_keluar'])->format('Y-m-d');

        try {
            // Memulai transaksi
            DB::beginTransaction();

            // Menemukan stok berdasarkan kode barang dan mengupdate jumlahnya
            $stok = Stok::where('kode_barang', $barangKeluar->kode_barang)->first();
            if ($stok) {
                // Mengembalikan stok sebelumnya
                $stok->stok_total += $barangKeluar->jumlah;
                $stok->stok_total -= $request->jumlah;
                $stok->tanggal_update = now();
                $stok->save();
            }

            // Mengupdate BarangKeluar
            $barangKeluar->update([
                'jumlah' => $request->jumlah,
                'tanggal_keluar' => $validatedData['tanggal_keluar'],
                'toko_tujuan' => $request->toko_tujuan,
            ]);

            // Commit transaksi
            DB::commit();

            $data = [
                'status' => true,
                'message' => 'Update Barang Keluar Success',
                'data' => new BarangKeluarResource($barangKeluar),
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus data barang keluar berdasarkan ID.
     */
    public function destroy($id)
    {
        $barangKeluar = BarangKeluar::find($id);
        try {
            // Memulai transaksi
            DB::beginTransaction();

            // Menemukan stok dan mengembalikan jumlah yang keluar
            $stok = Stok::where('kode_barang', $barangKeluar->kode_barang)->first();
            if ($stok) {
                $stok->stok_total += $barangKeluar->jumlah;
                $stok->tanggal_update = now();
                $stok->save();
            }

            // Menghapus barang keluar
            $barangKeluar->delete();

            // Commit transaksi
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Delete Barang Keluar Success',
            ], 200);
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

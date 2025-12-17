<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Absensi\AbsensiRekapResource;
use App\Http\Resources\Absensi\BuktiResource;
use App\Http\Resources\Absensi\UbahStatusResource;
use App\Http\Resources\AbsensiOnDayResource;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataAbsensiController extends Controller
{
    public function absensiOnDay(Request $request)
    {
        $currentDate = date('Y-m-d');
        if ($request->has('date')) {
            $currentDate = $request->input('date');

            // Parse the date from d-m-Y format and convert it to Y-m-d format
            $currentDate = Carbon::createFromFormat('d-m-Y', $currentDate)->format('Y-m-d');
        }

        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        $absensis = Absensi::where('tanggal', $currentDate);

        if ($search) {
            $absensis = $absensis->where(function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%');
                })->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('tanggal', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%')
                    ->orWhere('waktu_checkin', 'like', '%'.$search.'%')
                    ->orWhere('waktu_checkout', 'like', '%'.$search.'%');
            });
        }

        $absensis = $absensis->orderBy('created_at', 'desc')
            ->paginate($perpage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Data absensi pada tanggal '.Carbon::createFromFormat('Y-m-d', $currentDate)->format('d-m-Y'),
            'data' => AbsensiOnDayResource::collection($absensis),
        ]);

    }

    public function absensiById($id)
    {
        $absensi = Absensi::find($id);

        if (! $absensi) {
            return response()->json([
                'status' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail data absensi',
            'data' => new AbsensiOnDayResource($absensi),
        ]);
    }

    public function updateStatusAbsensi(Request $request, $id)
    {
        $absensi = Absensi::find($id);

        if (! $absensi) {
            return response()->json([
                'status' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:Hadir,Terlambat',
        ], [
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status yang dipilih tidak valid. Pilih salah satu dari: Hadir,Terlambat',
        ]);

        $absensi->status = $request->input('status');
        $absensi->save();

        return response()->json([
            'status' => true,
            'message' => 'Status absensi berhasil diubah',
            'data' => new UbahStatusResource($absensi),
        ]);
    }

    public function buktiIzinSakit($id)
    {
        $absensi = Absensi::find($id);

        if (! $absensi) {
            return response()->json([
                'status' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        if (! $absensi->image_proof) {
            return response()->json([
                'status' => false,
                'message' => 'Bukti izin/sakit tidak tersedia',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Bukti izin/sakit ditemukan',
            'data' => new BuktiResource($absensi),
        ]);
    }

    public function approveIzinSakit(Request $request, $id)
    {
        $absensi = Absensi::find($id);

        if (! $absensi) {
            return response()->json([
                'status' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        if ($absensi->status !== 'Menunggu Konfirmasi') {
            return response()->json([
                'status' => false,
                'message' => 'Absensi ini sudah diproses sebelumnya.',
            ], 400);
        }

        $request->validate([
            'status' => 'required|in:Izin,Sakit,Tidak Disetujui',
        ], [
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status yang dipilih tidak valid. Pilih salah satu dari: Izin, Sakit, atau Tidak Disetujui.',
        ]);

        $absensi->status = $request->input('status');
        $absensi->save();

        return response()->json([
            'status' => true,
            'message' => 'Izin/sakit disetujui.',
            'data' => new UbahStatusResource($absensi),
        ]);
    }

    public function rekapAbsensiByBulan(Request $request)
    {
        // Validasi input 'date' dengan format m-Y
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:m-Y',
        ], [
            'date.required' => 'Tanggal wajib diisi.',
            'date.date_format' => 'Format tanggal tidak valid. Harus menggunakan format bulan-tahun (mm-yyyy), contohnya 12-2025.',
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        // $date = $request->input('date', date('Y-m'));

        // $month = date('m', strtotime($date));
        // $year = date('Y', strtotime($date));

        if ($request->has('date')) {
            $date = $request->input('date');  // Input format is m-Y (e.g., 12-2025)

            // Parse the date from m-Y format and convert it to Y-m format
            $formattedDate = Carbon::createFromFormat('m-Y', $date)->format('Y-m');

            // Extract year and month
            $year = Carbon::createFromFormat('m-Y', $date)->year;
            $month = Carbon::createFromFormat('m-Y', $date)->month;
        }

        $absensis = Absensi::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        $rekap = $absensis->groupBy('user_id')->map(function ($absensiGroup, $userId) {
            $user = User::find($userId);

            // Inisialisasi counters
            $hadir = 0;
            $terlambat = 0;
            $hadirTidakAbsenPulang = 0;
            $izin = 0;
            $sakit = 0;

            // Hitung status per absensi
            foreach ($absensiGroup as $absensi) {
                switch ($absensi->status) {
                    case 'Hadir':
                        $hadir += 1;
                        break;
                    case 'Terlambat':
                        $terlambat += 0.5;
                        break;
                    case 'Hadir(Tidak Absen Pulang)':
                        $hadirTidakAbsenPulang += 0.5;
                    case 'Izin':
                        $izin += 1;
                        break;
                    case 'Sakit':
                        $sakit += 1;
                        break;
                }
            }

            return (object) [
                'user_id' => $userId,
                'nama' => $user->name,
                'jabatan' => $user->jabatan,
                'hadir' => $hadir + $terlambat + $hadirTidakAbsenPulang,
                'izin' => $izin,
                'sakit' => $sakit,
            ];
        });

        $rekap = $rekap->sortBy(function ($item) {
            return $item->nama;
        });

        return response()->json([
            'status' => true,
            'message' => 'Rekap absensi bulan '.$month.' tahun '.$year,
            'data' => AbsensiRekapResource::collection($rekap),
        ], 200);
    }
}

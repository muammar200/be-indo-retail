<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IzinSakitRequest;
use App\Http\Resources\Absensi\ClockInResource;
use App\Http\Resources\Absensi\ClockOutResource;
use App\Http\Resources\MetaPaginateResource;
use App\Http\Resources\RiwayatAbsenResource;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function clockIn(Request $request)
    {
        // Ambil tanggal dan waktu dari request
        $tanggal = $request->input('tanggal');  // Format: YYYY-DD-MM (misalnya 2025-26-12)
        $waktu = $request->input('waktu');      // Format: HH:MM (misalnya 07:56)

        // Menghapus tanda kutip ganda
        $tanggal = str_replace('"', '', $tanggal);
        $waktu = str_replace('"', '', $waktu);

        // Validasi input tanggal dan waktu
        if (empty($tanggal) || empty($waktu)) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal dan Waktu harus diisi.',
            ], 400);
        }

        // Mengonversi tanggal dari format YYYY-DD-MM ke YYYY-MM-DD
        try {
            // Pecah tanggal yang dikirim (YYYY-DD-MM) menjadi komponen tanggal
            // [$tahun, $hari, $bulan] = explode('-', $tanggal);

            // Buat objek Carbon dari tanggal yang sudah diperbaiki menjadi format YYYY-MM-DD
            $formattedDate = $tanggal;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Format tanggal tidak valid.',
            ], 400);
        }

        // Gabungkan tanggal dan waktu untuk membuat timestamp
        $tanggalWaktu = $formattedDate.' '.$waktu.':00';  // Menggabungkan menjadi format: YYYY-MM-DD HH:MM:00

        try {
            // Cek apakah absensi sudah ada untuk tanggal yang diberikan
            $absensi = Absensi::where('user_id', $request->user()->id)
                ->where('tanggal', $formattedDate)  // Menggunakan tanggal yang sudah diperbaiki
                ->first();

            if ($absensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan absensi masuk pada tanggal ini.',
                ], 400);
            }

            // Cek apakah waktu check-in lebih dari jam 08:00
            $jamKerja = '08:00:00';  // Batas waktu hadir

            // Bandingkan waktu check-in dengan jam 08:00:00
            $status = Carbon::createFromFormat('Y-m-d H:i:s', $tanggalWaktu)->format('H:i:s') <= $jamKerja ? 'Hadir' : 'Terlambat';

            // Menghitung jarak untuk absensi berdasarkan latitude dan longitude
            // $userLatitude = $request->input('latitude');
            // $userLongitude = $request->input('longitude');
            $userLatitude = floatval($request->input('latitude'));
            $userLongitude = floatval($request->input('longitude'));

            if (empty($userLatitude) || empty($userLongitude)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses lokasi harus diaktifkan.',
                ], 400);
            }

            // Koordinat kantor
            $officeLatitude = -5.2052646;
            $officeLongitude = 119.4948216;

            // Maksimum jarak absensi (meter)
            $maxDistance = 10000;

            // Menghitung jarak
            $distance = $this->calculateDistance($userLatitude, $userLongitude, $officeLatitude, $officeLongitude);

            if ($distance > $maxDistance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda terlalu jauh dari area yang ditentukan untuk melakukan absensi.',
                ], 400);
            }

            // Data absensi
            $userAbsensi = [
                'user_id' => $request->user()->id,
                'tanggal' => $formattedDate,  // Menggunakan tanggal yang sudah diperbaiki
                'waktu_checkin' => Carbon::createFromFormat('Y-m-d H:i:s', $tanggalWaktu)->format('H:i:s'),
                'status' => $status,  // Status ditentukan berdasarkan waktu check-in
            ];

            // Membuat absensi baru
            $absensi = Absensi::create($userAbsensi);

            // Response sukses
            $data = [
                'status' => true,
                'message' => 'Clock In Success',
                'data' => new ClockInResource($absensi),
            ];

            return response()->json($data, 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function clockOut(Request $request)
    {
        // Ambil tanggal dan waktu dari request
        $tanggal = $request->input('tanggal');  // Format: YYYY-DD-MM (misalnya 2025-26-12)
        $waktu = $request->input('waktu');      // Format: HH:MM (misalnya 17:56)

        // Validasi input tanggal dan waktu
        if (empty($tanggal) || empty($waktu)) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal dan Waktu harus diisi.',
            ], 400);
        }

        // Mengonversi tanggal dari format YYYY-DD-MM ke YYYY-MM-DD
        try {
            // Pecah tanggal yang dikirim (YYYY-DD-MM) menjadi komponen tanggal
            // [$tahun, $hari, $bulan] = explode('-', $tanggal);

            // Buat objek Carbon dari tanggal yang sudah diperbaiki menjadi format YYYY-MM-DD
            $formattedDate = $tanggal;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Format tanggal tidak valid.',
            ], 400);
        }

        try {
            // Cek apakah absensi sudah ada untuk tanggal yang diberikan
            $absensi = Absensi::where('user_id', $request->user()->id)
                ->where('tanggal', $formattedDate)  // Menggunakan tanggal yang sudah diperbaiki
                ->first();

            if (! $absensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum melakukan absen masuk pada tanggal ini.',
                ], 400);
            }

            if (empty($absensi->waktu_checkin)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum melakukan absen masuk, tidak bisa absen pulang.',
                ], 400);
            }

            // Cek apakah sudah jam 17:00
            $currentTime = $waktu;
            $jamPulang = '17:00:00';

            if ($currentTime < $jamPulang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Belum waktu pulang. Clock out hanya bisa dilakukan mulai jam 17:00.',
                ], 400);
            }

            if (! empty($absensi->waktu_checkout)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan absensi pulang hari ini.',
                ], 400);
            }

            // $userLatitude = $request->input('latitude');
            // $userLongitude = $request->input('longitude');
            $userLatitude = floatval($request->input('latitude'));
            $userLongitude = floatval($request->input('longitude'));

            if (empty($userLatitude) || empty($userLongitude)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses lokasi harus diaktifkan.',
                ], 400);
            }

            // Koordinat kantor
            $officeLatitude = -5.2052646;
            $officeLongitude = 119.4948216;

            // Maksimum jarak absensi (meter)
            $maxDistance = 10000;

            // Menghitung jarak
            $distance = $this->calculateDistance($userLatitude, $userLongitude, $officeLatitude, $officeLongitude);

            if ($distance > $maxDistance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda terlalu jauh dari area yang ditentukan untuk melakukan absensi.',
                ], 400);
            }

            // Update waktu checkout
            $absensi->update([
                'waktu_checkout' => $currentTime,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Clock Out Success',
                'data' => new ClockOutResource($absensi),
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function izinSakit(IzinSakitRequest $request)
    {
        try {
            // Memeriksa apakah sudah ada absensi untuk hari ini
            $absensi = Absensi::where('user_id', $request->user()->id)
                ->where('tanggal', date('Y-m-d'))
                ->where('image_proof', '!=', null)
                ->first();

            if ($absensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah mengirim bukti izin/sakit hari ini.',
                ], 400);
            }

            // Menyiapkan data absensi yang akan disimpan
            $userAbsensi = [
                'user_id' => $request->user()->id,
                'tanggal' => date('Y-m-d'),
                'status' => 'Menunggu Konfirmasi',
                'kategori' => $request->kategori,
            ];

            // Menambahkan keterangan jika ada
            if ($request->keterangan) {
                $userAbsensi['keterangan'] = $request->keterangan;
            }

            // Menyimpan bukti gambar
            $imagePath = $request->file('image_proof')->store('images/absensi', 'public');
            $imageFileName = basename($imagePath);

            $userAbsensi['image_proof'] = $imageFileName;

            // Menyimpan data absensi ke database
            Absensi::create($userAbsensi);

            return response()->json([
                'status' => true,
                'message' => 'Bukti '.$request->kategori.' Berhasil Dikirim',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function riwayatAbsenByBulan(Request $request)
    {
        try {
            $month = date('m');
            $year = date('Y');

            $page = $request->input('page', 1);
            $perpage = $request->input('perpage', 10);
            $search = $request->input('search', '');

            $absensiRecords = Absensi::where('user_id', $request->user()->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);

            if ($search) {
                $absensiRecords = $absensiRecords->where(function ($query) use ($search) {
                    $query->where('tanggal', 'like', '%'.$search.'%')
                        ->orWhere('status', 'like', '%'.$search.'%')
                        ->orWhere('keterangan', 'like', '%'.$search.'%')
                        ->orWhere('waktu_checkin', 'like', '%'.$search.'%')
                        ->orWhere('waktu_checkout', 'like', '%'.$search.'%');
                });
            }

            $absensiRecords = $absensiRecords->orderBy('tanggal')
                ->paginate($perpage, ['*'], 'page', $page);

            $data = [
                'status' => true,
                'message' => 'Riwayat Absen on Month Retrieved Successfully',
                'meta' => new MetaPaginateResource($absensiRecords),
                'data' => RiwayatAbsenResource::collection($absensiRecords),
            ];

            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

    }

    private function calculateDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($latitude1);
        $lonFrom = deg2rad($longitude1);
        $latTo = deg2rad($latitude2);
        $lonTo = deg2rad($longitude2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }
}

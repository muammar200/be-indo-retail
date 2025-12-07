<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IzinSakitRequest;
use App\Http\Resources\Absensi\ClockInResource;
use App\Http\Resources\Absensi\ClockOutResource;
use App\Http\Resources\RiwayatAbsenResource;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function clockIn(Request $request)
    {
        $absensi = Absensi::where('user_id', $request->user()->id)
            ->where('tanggal', date('Y-m-d'))
            ->first();
        try {

            if ($absensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah melakukan absensi masuk hari ini.',
                ], 400);
            }

            $userLatitude = $request->input('latitude');
            $userLongitude = $request->input('longitude');

            $officeLatitude = -5.2052646; 
            $officeLongitude = 119.4948216;  

            // Maks jarak (meter)
            $maxDistance = 1000;

            $distance = $this->calculateDistance($userLatitude, $userLongitude, $officeLatitude, $officeLongitude);

            if ($distance > $maxDistance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda terlalu jauh dari area yang ditentukan untuk melakukan absensi.',
                ], 400);
            }

            $userAbsensi = [
                'user_id' => $request->user()->id,
                'tanggal' => date('Y-m-d'),
                'waktu_checkin' => date('H:i:s'),
                'status' => 'Hadir',
            ];

            $absensi = Absensi::create($userAbsensi);

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
        $absensi = Absensi::where('user_id', $request->user()->id)
            ->where('tanggal', date('Y-m-d'))
            ->first();

        try {

            if (! $absensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum melakukan absen masuk hari ini.',
                ], 400);
            }

            if (empty($absensi->waktu_checkin)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum melakukan absen masuk, tidak bisa absen pulang.',
                ], 400);
            }

            // Cek apakah sudah jam 17:00
            $currentTime = date('H:i:s');
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

            $userLatitude = $request->input('latitude');
            $userLongitude = $request->input('longitude');

          
            $officeLatitude = -5.2052646;  
            $officeLongitude = 119.4948216;  

            // Maks jarak (meter)
            $maxDistance = 1000;

            $distance = $this->calculateDistance($userLatitude, $userLongitude, $officeLatitude, $officeLongitude);

            if ($distance > $maxDistance) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda terlalu jauh dari area yang ditentukan untuk melakukan absensi.',
                ], 400);
            }

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

            $absensi = Absensi::where('user_id', $request->user()->id)
                ->where('tanggal', date('Y-m-d'))->where('image_proof', '!=', null)
                ->first();

            if ($absensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah mengirim bukti izin/sakit hari ini.',
                ], 400);
            }

            $userAbsensi = [
                'user_id' => $request->user()->id,
                'tanggal' => date('Y-m-d'),
                'status' => 'Menunggu Konfirmasi',
            ];

            //store image proof
            if ($request->hasFile('image_proof')) {
                $image = $request->file('image_proof');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/absensi'), $imageName);
                $userAbsensi['image_proof'] = 'uploads/absensi/' . $imageName;
            }

            Absensi::create($userAbsensi);

            return response()->json([
                'status' => true,
                'message' => 'Bukti Izin/Sakit Berhasil Dikirim',
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
            // $month = $request->input('month');
            // $year = $request->input('year');
            $month = date('m');
            $year = date('Y');

            $absensiRecords = Absensi::where('user_id', $request->user()->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderBy('tanggal')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Riwayat Absen on Month Retrieved Successfully',
                'data' => RiwayatAbsenResource::collection($absensiRecords),
            ], 200);

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

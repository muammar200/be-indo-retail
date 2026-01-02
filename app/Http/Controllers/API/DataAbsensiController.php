<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Absensi\AbsensiRekapResource;
use App\Http\Resources\Absensi\BuktiResource;
use App\Http\Resources\Absensi\UbahStatusResource;
use App\Http\Resources\AbsensiOnDayResource;
use App\Http\Resources\MetaPaginateResource;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DataAbsensiController extends Controller
{
    /**
     * Menampilkan data absensi pada tanggal tertentu (default: hari ini)
     * Mendukung pagination dan pencarian.
     */
    public function absensiOnDay(Request $request)
    {
        // Set default tanggal hari ini
        $currentDate = date('Y-m-d');

        // Jika ada parameter date, parsing dari format Indonesia (d F Y)
        if ($request->has('date')) {
            $currentDate = $request->input('date');

            try {
                // Mapping nama bulan Indonesia ke angka
                $bulanIndonesia = [
                    'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
                    'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
                    'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
                ];

                // Ganti nama bulan dengan angka agar bisa diparse Carbon
                $inputDate = strtolower($currentDate);
                foreach ($bulanIndonesia as $bulanIndo => $bulanNumber) {
                    if (strpos($inputDate, strtolower($bulanIndo)) !== false) {
                        $currentDate = preg_replace('/'.preg_quote($bulanIndo, '/').'/', $bulanNumber, $currentDate);
                        break;
                    }
                }

                // Konversi ke format Y-m-d
                $currentDate = Carbon::createFromFormat('d m Y', $currentDate)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid'], 400);
            }
        }

        // Ambil parameter pagination dan pencarian
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Query absensi berdasarkan tanggal
        $absensis = Absensi::where('tanggal', $currentDate);

        // Jika ada pencarian, filter berdasarkan user dan field absensi
        if ($search) {
            $absensis->where(function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%');
                })
                ->orWhere('status', 'like', '%'.$search.'%')
                ->orWhere('tanggal', 'like', '%'.$search.'%')
                ->orWhere('keterangan', 'like', '%'.$search.'%')
                ->orWhere('waktu_checkin', 'like', '%'.$search.'%')
                ->orWhere('waktu_checkout', 'like', '%'.$search.'%');
            });
        }

        // Pagination dan sorting
        $absensis = $absensis->orderBy('created_at', 'desc')
            ->paginate($perpage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Data absensi pada tanggal '.Carbon::createFromFormat('Y-m-d', $currentDate)->format('d F Y'),
            'meta' => new MetaPaginateResource($absensis),
            'data' => AbsensiOnDayResource::collection($absensis),
        ]);
    }

    /**
     * Menampilkan detail absensi berdasarkan ID.
     */
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

    /**
     * Mengubah status absensi (Hadir / Terlambat).
     */
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
        ]);

        $absensi->status = $request->status;
        $absensi->save();

        return response()->json([
            'status' => true,
            'message' => 'Status absensi berhasil diubah',
            'data' => new UbahStatusResource($absensi),
        ]);
    }

    /**
     * Menampilkan bukti izin atau sakit (gambar).
     */
    public function buktiIzinSakit($id)
    {
        $absensi = Absensi::find($id);

        if (! $absensi || ! $absensi->image_proof) {
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

    /**
     * Approve atau tolak izin / sakit.
     */
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
            'status' => 'required|in:Disetujui,Tidak Disetujui',
        ]);

        // Tentukan status akhir berdasarkan kategori
        if ($request->status == 'Disetujui') {
            $absensi->status = $absensi->kategori;
        } else {
            $absensi->status = 'Tidak Disetujui';
        }

        $absensi->save();

        return response()->json([
            'status' => true,
            'message' => 'Izin/sakit telah ditangani.',
            'data' => new UbahStatusResource($absensi),
        ]);
    }

    /**
     * Rekap absensi per user berdasarkan bulan dan tahun.
     */
    public function rekapAbsensiByBulan(Request $request)
    {
        // Ambil dan validasi bulan & tahun
        $bulan = $request->month;
        $tahun = $request->year;

        if (! $bulan || ! $tahun) {
            return response()->json(['error' => 'Bulan dan Tahun harus diisi'], 400);
        }

        // Mapping bulan Indonesia ke angka
        $bulanIndonesia = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
        ];

        if (! isset($bulanIndonesia[$bulan])) {
            return response()->json(['error' => 'Nama bulan tidak valid'], 400);
        }

        $bulanAngka = $bulanIndonesia[$bulan];

        // Ambil data absensi dalam bulan tersebut
        $absensis = Absensi::whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Rekap absensi per user
        $rekap = $absensis->groupBy('user_id')->map(function ($items, $userId) {
            $user = User::find($userId);

            $hadir = $izin = $sakit = 0;

            foreach ($items as $absensi) {
                match ($absensi->status) {
                    'Hadir' => $hadir++,
                    'Terlambat', 'Hadir(Tidak Absen Pulang)' => $hadir += 0.5,
                    'Izin' => $izin++,
                    'Sakit' => $sakit++,
                };
            }

            return (object) [
                'user_id' => $userId,
                'nama' => $user->name,
                'jabatan' => $user->jabatan,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
            ];
        })->sortBy('nama');

        return response()->json([
            'status' => true,
            'message' => "Rekap absensi bulan $bulan tahun $tahun",
            'data' => AbsensiRekapResource::collection($rekap),
        ]);
    }

    /**
     * Mengambil daftar tanggal dalam satu bulan.
     */
    public function getTanggalDiBulan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Ambil jumlah hari dalam bulan
        $jumlahHari = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        // Generate list tanggal
        $period = CarbonPeriod::create(
            Carbon::createFromDate($tahun, $bulan, 1),
            Carbon::createFromDate($tahun, $bulan, $jumlahHari)
        );

        $result = array_map(fn ($date) => [
            'id' => $date->day,
            'label' => $date->format('d').' '.$this->getBulanName($bulan).' '.$date->year,
        ], $period->toArray());

        return response()->json([
            'status' => true,
            'message' => 'Get Tanggal di '.$this->getBulanName($bulan).' '.$tahun,
            'data' => $result,
        ]);
    }

    /**
     * Helper nama bulan Indonesia.
     */
    private function getBulanName($bulan)
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$bulan] ?? 'Bulan Tidak Valid';
    }

    /**
     * Mengambil list bulan (1–12).
     */
    public function getMonth()
    {
        return response()->json([
            'status' => true,
            'message' => 'Get Bulan',
            'data' => collect(range(1, 12))->map(fn ($i) => [
                'id' => $i,
                'label' => $this->getBulanName($i),
            ]),
        ]);
    }
}

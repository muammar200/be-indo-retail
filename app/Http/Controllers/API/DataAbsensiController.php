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
use Illuminate\Support\Facades\Validator;

class DataAbsensiController extends Controller
{
    // public function absensiOnDay(Request $request)
    // {
    //     $currentDate = date('Y-m-d');
    //     if ($request->has('date')) {
    //         $currentDate = $request->input('date');

    //         // Parse the date from d-m-Y format and convert it to Y-m-d format
    //         $currentDate = Carbon::createFromFormat('d-m-Y', $currentDate)->format('Y-m-d');
    //     }

    //     $page = $request->input('page', 1);
    //     $perpage = $request->input('perpage', 10);
    //     $search = $request->input('search', '');

    //     $absensis = Absensi::where('tanggal', $currentDate);

    //     if ($search) {
    //         $absensis = $absensis->where(function ($query) use ($search) {
    //             $query->whereHas('user', function ($q) use ($search) {
    //                 $q->where('name', 'like', '%'.$search.'%');
    //             })->orWhere('status', 'like', '%'.$search.'%')
    //                 ->orWhere('tanggal', 'like', '%'.$search.'%')
    //                 ->orWhere('keterangan', 'like', '%'.$search.'%')
    //                 ->orWhere('waktu_checkin', 'like', '%'.$search.'%')
    //                 ->orWhere('waktu_checkout', 'like', '%'.$search.'%');
    //         });
    //     }

    //     $absensis = $absensis->orderBy('created_at', 'desc')
    //         ->paginate($perpage, ['*'], 'page', $page);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data absensi pada tanggal '.Carbon::createFromFormat('Y-m-d', $currentDate)->format('d-m-Y'),
    //         'meta' => new MetaPaginateResource($absensis),
    //         'data' => AbsensiOnDayResource::collection($absensis),
    //     ]);

    // }

    public function absensiOnDay(Request $request)
    {
        $currentDate = date('Y-m-d');

        if ($request->has('date')) {
            $currentDate = $request->input('date');

            // Parsing tanggal dari format 'd F Y' dengan bulan dalam Bahasa Indonesia
            try {
                // Definisikan array bulan dalam bahasa Indonesia
                $bulanIndonesia = [
                    'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
                    'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
                    'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
                ];

                // Ubah input menjadi format yang dapat diproses Carbon
                $inputDate = strtolower($currentDate); // Lowercase input
                foreach ($bulanIndonesia as $bulanIndo => $bulanNumber) {
                    // Cari nama bulan dan ganti dengan angka bulan
                    if (strpos($inputDate, strtolower($bulanIndo)) !== false) {
                        $currentDate = preg_replace('/'.preg_quote($bulanIndo, '/').'/', $bulanNumber, $currentDate);
                        break;
                    }
                }

                // Parsing tanggal setelah bulan diganti dengan angka
                $currentDate = Carbon::createFromFormat('d m Y', $currentDate)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Format tanggal tidak valid'], 400);
            }
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
            'message' => 'Data absensi pada tanggal '.Carbon::createFromFormat('Y-m-d', $currentDate)->format('d F Y'),
            'meta' => new MetaPaginateResource($absensis),
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
            'status' => 'required|in:Disetujui,Tidak Disetujui',
        ], [
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status yang dipilih tidak valid. Pilih salah satu dari: Izin, Sakit, atau Tidak Disetujui.',
        ]);

        if ($request->status == 'Disetujui') {
            if ($absensi->kategori == 'Izin') {
                $absensi->status = 'Izin';
                $absensi->save();
            } elseif ($absensi->kategori == 'Sakit') {
                $absensi->status = 'Sakit';
                $absensi->save();
            }
        } elseif ($request->status == 'Tidak Disetujui') {
            $absensi->status = 'Tidak Disetujui';
            $absensi->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Izin/sakit telah ditangani.',
            'data' => new UbahStatusResource($absensi),
        ]);
    }

    // public function rekapAbsensiByBulan(Request $request)
    // {
    //     // Validasi input 'date' dengan format m-Y
    //     $validator = Validator::make($request->all(), [
    //         'date' => 'required|date_format:m-Y',
    //     ], [
    //         'date.required' => 'Tanggal wajib diisi.',
    //         'date.date_format' => 'Format tanggal tidak valid. Harus menggunakan format bulan-tahun (mm-yyyy), contohnya 12-2025.',
    //     ]);

    //     // Jika validasi gagal, kembalikan error
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors(),
    //         ], 400);
    //     }

    //     // $date = $request->input('date', date('Y-m'));

    //     // $month = date('m', strtotime($date));
    //     // $year = date('Y', strtotime($date));

    //     if ($request->has('date')) {
    //         $date = $request->input('date');  // Input format is m-Y (e.g., 12-2025)

    //         // Parse the date from m-Y format and convert it to Y-m format
    //         $formattedDate = Carbon::createFromFormat('m-Y', $date)->format('Y-m');

    //         // Extract year and month
    //         $year = Carbon::createFromFormat('m-Y', $date)->year;
    //         $month = Carbon::createFromFormat('m-Y', $date)->month;
    //     }

    //     $absensis = Absensi::whereMonth('tanggal', $month)
    //         ->whereYear('tanggal', $year)
    //         ->get();

    //     $rekap = $absensis->groupBy('user_id')->map(function ($absensiGroup, $userId) {
    //         $user = User::find($userId);

    //         // Inisialisasi counters
    //         $hadir = 0;
    //         $terlambat = 0;
    //         $hadirTidakAbsenPulang = 0;
    //         $izin = 0;
    //         $sakit = 0;

    //         // Hitung status per absensi
    //         foreach ($absensiGroup as $absensi) {
    //             switch ($absensi->status) {
    //                 case 'Hadir':
    //                     $hadir += 1;
    //                     break;
    //                 case 'Terlambat':
    //                     $terlambat += 0.5;
    //                     break;
    //                 case 'Hadir(Tidak Absen Pulang)':
    //                     $hadirTidakAbsenPulang += 0.5;
    //                 case 'Izin':
    //                     $izin += 1;
    //                     break;
    //                 case 'Sakit':
    //                     $sakit += 1;
    //                     break;
    //             }
    //         }

    //         return (object) [
    //             'user_id' => $userId,
    //             'nama' => $user->name,
    //             'jabatan' => $user->jabatan,
    //             'hadir' => $hadir + $terlambat + $hadirTidakAbsenPulang,
    //             'izin' => $izin,
    //             'sakit' => $sakit,
    //         ];
    //     });

    //     $rekap = $rekap->sortBy(function ($item) {
    //         return $item->nama;
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Rekap absensi bulan '.$month.' tahun '.$year,
    //         'data' => AbsensiRekapResource::collection($rekap),
    //     ], 200);
    // }

    public function rekapAbsensiByBulan(Request $request)
    {
        // Ambil bulan dan tahun dari request
        $bulan = $request->input('month');  // Nama bulan dalam Bahasa Indonesia (e.g., Desember)
        $tahun = $request->input('year');  // Tahun (e.g., 2025)

        // Validasi input bulan dan tahun
        if (empty($bulan) || empty($tahun)) {
            return response()->json(['error' => 'Bulan dan Tahun harus diisi'], 400);
        }

        // Definisikan array bulan dalam bahasa Indonesia
        $bulanIndonesia = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
        ];

        // Validasi bulan yang diberikan
        if (! array_key_exists($bulan, $bulanIndonesia)) {
            return response()->json(['error' => 'Nama bulan tidak valid'], 400);
        }

        // Mengonversi nama bulan menjadi angka
        $bulanAngka = $bulanIndonesia[$bulan];

        // Format tanggal menjadi Y-m-01 (tanggal pertama bulan tersebut)
        $date = $tahun.'-'.$bulanAngka.'-01';

        // Ambil data absensi berdasarkan bulan dan tahun yang diberikan
        $absensis = Absensi::whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Proses rekap absensi per user
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
                        break;
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

        // Sortir berdasarkan nama
        $rekap = $rekap->sortBy(function ($item) {
            return $item->nama;
        });

        return response()->json([
            'status' => true,
            'message' => 'Rekap absensi bulan '.$bulan.' tahun '.$tahun,
            'data' => AbsensiRekapResource::collection($rekap),
        ], 200);
    }

    // public function getTanggalDiBulan(Request $request)
    // {
    //     // Ambil bulan dan tahun dari request atau set default ke bulan dan tahun sekarang
    //     $bulan = $request->input('bulan', Carbon::now()->month);
    //     $tahun = $request->input('tahun', Carbon::now()->year);

    //     // Validasi apakah bulan dan tahun valid
    //     if ($bulan < 1 || $bulan > 12) {
    //         return response()->json(['error' => 'Bulan tidak valid'], 400);
    //     }

    //     // Tentukan jumlah hari dalam bulan tersebut
    //     $jumlahHari = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

    //     // Buat range tanggal dari 1 sampai jumlah hari di bulan tersebut
    //     $period = CarbonPeriod::create(
    //         Carbon::createFromDate($tahun, $bulan, 1),
    //         Carbon::createFromDate($tahun, $bulan, $jumlahHari)
    //     );

    //     // Ambil tanggal dalam format DD-MM-YYYY
    //     $tanggal = $period->toArray();
    //     $result = array_map(function ($date) {
    //         return $date->format('d-m-Y');
    //     }, $tanggal);

    //     $data = [
    //         'status' => true,
    //         'message' => 'Get Tanggal di ' .$bulan. ' '. $tahun,
    //         'data' => $result,
    //     ];

    //     return response()->json($data, 200);
    // }

    public function getTanggalDiBulan(Request $request)
    {
        // Ambil bulan dan tahun dari request atau set default ke bulan dan tahun sekarang
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Validasi apakah bulan dan tahun valid
        if ($bulan < 1 || $bulan > 12) {
            return response()->json(['error' => 'Bulan tidak valid'], 400);
        }

        // Tentukan jumlah hari dalam bulan tersebut
        $jumlahHari = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        // Buat range tanggal dari 1 sampai jumlah hari di bulan tersebut
        $period = CarbonPeriod::create(
            Carbon::createFromDate($tahun, $bulan, 1),
            Carbon::createFromDate($tahun, $bulan, $jumlahHari)
        );

        // Ambil tanggal dalam format 'd F Y' (contoh: 20 Desember 2025)
        $tanggal = $period->toArray();
        $result = array_map(function ($date) use ($bulan) {
            // Gantikan nama bulan menggunakan array bulan Indonesia
            $bulanIndonesia = $this->getBulanName($bulan);

            return [
                'id' => $date->day,
                'label' => $date->format('d').' '.$bulanIndonesia.' '.$date->format('Y'),
            ];
        }, $tanggal);

        $data = [
            'status' => true,
            'message' => 'Get Tanggal di '.$this->getBulanName($bulan).' '.$tahun,
            'data' => $result,
        ];

        return response()->json($data, 200);
    }

    // Fungsi untuk mendapatkan nama bulan dalam bahasa Indonesia
    private function getBulanName($bulan)
    {
        $bulanArray = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulanArray[$bulan] ?? 'Bulan Tidak Valid';
    }

    public function getMonth()
    {
        return response()->json([
            'status' => true,
            'message' => 'Get Bulan',
            'data' => [
                [
                    'id' => 1,
                    'label' => 'Januari',
                ],
                [
                    'id' => 2,
                    'label' => 'Februari',
                ],
                [
                    'id' => 3,
                    'label' => 'Maret',
                ],
                [
                    'id' => 4,
                    'label' => 'April',
                ],
                [
                    'id' => 5,
                    'label' => 'Mei',
                ],
                [
                    'id' => 6,
                    'label' => 'Juni',
                ],
                [
                    'id' => 7,
                    'label' => 'Juli',
                ],
                [
                    'id' => 8,
                    'label' => 'Agustus',
                ],
                [
                    'id' => 9,
                    'label' => 'September',
                ],
                [
                    'id' => 10,
                    'label' => 'Oktober',
                ],
                [
                    'id' => 11,
                    'label' => 'November',
                ],
                [
                    'id' => 12,
                    'label' => 'Desember',
                ],
            ],
        ], 200);
    }
}

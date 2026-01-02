<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Fungsi untuk menangani proses login
    public function login(Request $request)
    {
        // Validasi input, pastikan nomor HP dan password diberikan
        $credentials = $request->validate([
            'no_hp' => 'required',
            'password' => 'required',
        ], [
            'no_hp.required' => 'Nomor handphone harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        try {
            // Cek apakah kredensial valid, jika tidak, kirim respons error
            if (! $token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No HP atau password salah',
                ], 401);
            }

            // Ambil data pengguna yang berhasil login
            $user = auth()->guard('api')->user();

            // Kirim respons dengan token dan data pengguna
            $data = [
                'status' => true,
                'message' => 'Login Success',
                'token' => $token,
                'data' => [
                    'name' => $user->name,
                    'no_hp' => $user->no_hp,
                    'jabatan' => $user->jabatan,
                ],
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            // Jika terjadi kesalahan, kirimkan error
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Fungsi untuk logout pengguna
    public function logout(Request $request)
    {
        try {
            // Invalidate token JWT
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
            if ($removeToken) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Success',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Fungsi untuk mendapatkan data pengguna yang sudah terautentikasi
    public function me(Request $request)
    {
        $data = [
            'status' => true,
            'message' => 'Get Authenticated User Success',
            'data' => new UserResource($request->user()),
        ];

        return response()->json($data, 200);
    }

    // Fungsi untuk mengirimkan OTP ke nomor WhatsApp pengguna
    public function sendOtp(Request $request)
    {
        try {
            // Validasi nomor WhatsApp
            $request->validate([
                'whatsapp' => 'required',
            ]);

            // Normalisasi nomor WhatsApp
            $wa = $this->normalizeWa($request->whatsapp);
            $user = User::where('no_hp', $wa)->first();

            // Jika nomor WhatsApp tidak ditemukan, kirim error
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nomor WhatsApp tidak terdaftar',
                ], 404);
            }

            // Generate OTP acak dan simpan ke database
            $otp = rand(100000, 999999);
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            // Atur throttle cache untuk OTP
            $cacheKey = "otp_throttle:{$wa}";
            Cache::put($cacheKey, now()->addMinutes(5)->timestamp, 300);

            // Kirim OTP melalui layanan WhatsApp (FonnteService)
            $res = FonnteService::send($wa, "Kode OTP kamu adalah: $otp (berlaku 5 menit)");

            // Jika pengiriman OTP gagal, kirim error
            if (! isset($res['status']) || $res['status'] != true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengirim pesan WhatsApp',
                ], 500);
            }

            // Jika berhasil, kirimkan respons sukses
            return response()->json([
                'status' => true,
                'message' => 'OTP berhasil dikirim!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Fungsi untuk memverifikasi OTP yang dimasukkan pengguna
    public function verifyOtp(Request $request)
    {
        try {
            // Validasi input OTP
            $request->validate([
                'otp' => 'required',
            ]);

            // Cek apakah OTP valid dan tidak kedaluwarsa
            $user = User::where('otp', $request->otp)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kode OTP salah',
                ], 404);
            }

            if (now()->greaterThan($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP kadaluarsa',
                ], 400);
            }

            // Generate token untuk reset password dan simpan ke cache
            $token = Str::random(64);
            $cacheKey = "password_reset_token:{$token}";
            Cache::put($cacheKey, $user->no_hp, 900);

            return response()->json([
                'status' => true,
                'message' => 'OTP valid',
                'password_reset_token' => $token,
                'expires_in_seconds' => 900,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Fungsi untuk mereset password pengguna
    public function resetPassword(Request $request)
    {
        try {
            // Validasi input password reset token dan password baru
            $request->validate([
                'password_reset_token' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            // Cek apakah token valid
            $token = $request->password_reset_token;
            $cacheKey = "password_reset_token:{$token}";

            if (! Cache::has($cacheKey)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak valid atau telah kedaluwarsa',
                ], 400);
            }

            // Ambil nomor WhatsApp pengguna dari cache
            $wa = Cache::get($cacheKey);
            $user = User::where('no_hp', $wa)->first();

            // Jika pengguna tidak ditemukan, kirim error
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            // Update password pengguna dan hapus OTP
            $user->update([
                'password' => $request->password,
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            // Hapus token dari cache
            Cache::forget($cacheKey);

            return response()->json([
                'status' => true,
                'message' => 'Password berhasil direset',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Fungsi untuk menormalkan nomor WhatsApp agar sesuai format internasional
    private function normalizeWa($number)
    {
        // Hilangkan karakter non-digit
        $number = preg_replace('/[^0-9]/', '', $number);

        // Ubah nomor lokal ke format internasional jika perlu
        if (substr($number, 0, 1) === '0') {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }
}

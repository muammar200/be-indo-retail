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
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'no_hp' => 'required',
            'password' => 'required',
        ], [
            'no_hp.required' => 'Nomor handphone harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        try {
            if (! $token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No HP atau password salah',
                ], 401);
            }

            $user = auth()->guard('api')->user();

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
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
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

    public function me(Request $request)
    {
        $data = [
            'status' => true,
            'message' => 'Get Authenticated User Success',
            'data' => new UserResource($request->user()),
        ];

        return response()->json($data, 200);
    }

    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'whatsapp' => 'required',
            ]);

            $wa = $this->normalizeWa($request->whatsapp);
            $user = User::where('no_hp', $wa)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nomor WhatsApp tidak terdaftar',
                ], 404);
            }

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            $user = User::where('no_hp', $wa)->first();

            // set throttle cache (simpan expiry timestamp agar response dapat beri retry_after)
            $cacheKey = "otp_throttle:{$wa}";
            Cache::put($cacheKey, now()->addMinutes(5)->timestamp, 300); // 300 detik

            $res = FonnteService::send($wa, "Kode OTP kamu adalah: $otp (berlaku 5 menit)");

            if (! isset($res['status']) || $res['status'] != true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengirim pesan WhatsApp',
                ], 500);
            }

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

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required',
            ]);

            // $user = User::where('otp', $request->no_hp)->first();
            $user = User::where('otp', $request->otp)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kode OTP salah',
                ], 404);
            }

            // if ($user->otp !== $request->otp) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'OTP salah',
            //     ], 400);
            // }

            if (now()->greaterThan($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP kadaluarsa',
                ], 400);
            }

            // generate temporary token untuk reset password
            $token = Str::random(64);
            $cacheKey = "password_reset_token:{$token}";

            // simpan mapping token -> whatsapp (15 menit)
            Cache::put($cacheKey, $user->no_hp, 900); // 900 detik = 15 menit

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

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'password_reset_token' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            $token = $request->password_reset_token;
            $cacheKey = "password_reset_token:{$token}";

            if (! Cache::has($cacheKey)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token tidak valid atau telah kedaluwarsa',
                ], 400);
            }

            $wa = Cache::get($cacheKey);

            $user = User::where('no_hp', $wa)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            $user->update([
                'password' => $request->password,
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            // hapus token dari cache
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

    private function normalizeWa($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (substr($number, 0, 1) === '0') {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }
}

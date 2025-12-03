<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
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

    // public function sendOtp(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'whatsapp' => 'required',
    //         ]);

    //         $user = User::where('no_hp', $request->whatsapp)->first();

    //         if (! $user) {
    //             return response()->json(['message' => 'Nomor WhatsApp tidak terdaftar'], 404);
    //         }

    //         $otp = rand(100000, 999999);

    //         // Kirim WhatsApp
    //         FonnteService::send(
    //             $request->whatsapp,
    //             "Kode OTP kamu adalah: $otp (berlaku 5 menit)"
    //         );

    //         return response()->json([
    //             'message' => 'OTP berhasil dikirim!',
    //             'otp' => $otp, // biasanya tidak ditampilkan ke frontend
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage(),
    //         ], 500);
    //     }
    // }

    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'whatsapp' => 'required',
            ]);

            // Normalisasi nomor (08123 → 628123)
            $wa = $this->normalizeWa($request->whatsapp);

            // CARI USER BERDASARKAN NO HP
            $user = User::where('no_hp', $wa)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nomor WhatsApp tidak terdaftar',
                ], 404);
            }

            // Generate OTP
            $otp = rand(100000, 999999);

            // Simpan OTP ke database
            // $user->update([
            //     'otp' => $otp,
            //     'otp_expires_at' => now()->addMinutes(5),
            // ]);

            // Kirim WhatsApp via Fonnte
            $res = FonnteService::send($wa, "Kode OTP kamu adalah: $otp (berlaku 5 menit)");

            // Bisa cek response dari Fonnte
            if (! isset($res['status']) || $res['status'] != true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengirim pesan WhatsApp',
                    'detail' => $res,
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

    private function normalizeWa($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (substr($number, 0, 1) === '0') {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }
}

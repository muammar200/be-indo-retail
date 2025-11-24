<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'no_hp' => 'required',
            'password' => 'required',
        ]);

        try {
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    "status" => false,
                    "message" => "No HP atau password salah"
                ], 401);
            }

            $user = auth()->guard('api')->user();

            $data = [
                "status" => true,
                "message" => "Login Success",
                "token" => $token,  
                "data" => [
                    "name" => $user->name,
                    "no_hp" => $user->no_hp,
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
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}

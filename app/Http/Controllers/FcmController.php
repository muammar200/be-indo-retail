<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',  // Validasi untuk memastikan token ada
        ]);

        // Mendapatkan pengguna yang sedang terautentikasi
        $user = auth()->user();

        // Menyimpan atau memperbarui token FCM dengan user yang sedang login
        FcmToken::updateOrCreate(
            ['token' => $request->token],  // Kunci pencarian untuk menemukan token yang sudah ada
            ['user_id' => $user->id]       // Menyimpan ID pengguna yang terautentikasi
        );

        // Mengembalikan respons setelah token berhasil disimpan
        return response()->json(['message' => 'Token saved']);
    }
}

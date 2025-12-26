<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $user = auth()->user();

        FcmToken::updateOrCreate(
            ['token' => $request->token],
            ['user_id' => $user->id]
        );

        return response()->json(['message' => 'Token saved']);
    }
}


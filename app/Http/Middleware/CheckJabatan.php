<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJabatan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Cek apakah jabatan user termasuk role yg diizinkan
        if (!in_array($user->jabatan, $roles)) {
            return response()->json([
                'message' => 'Forbidden — Jabatan tidak memiliki akses'
            ], 403);
        }

        return $next($request);
    }
}

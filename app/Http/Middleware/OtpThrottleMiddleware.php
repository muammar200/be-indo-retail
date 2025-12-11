<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class OtpThrottleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $whatsapp = $request->input('whatsapp');

        if (! $whatsapp) {
            // If no whatsapp provided, just proceed (validation later)
            return $next($request);
        }

        // normalisasi sederhana (sama helper di controller)
        $normalized = preg_replace('/[^0-9]/', '', $whatsapp);
        if (substr($normalized, 0, 1) === '0') {
            $normalized = '62'.substr($normalized, 1);
        }

        $cacheKey = "otp_throttle:{$normalized}";

        if (Cache::has($cacheKey)) {
            $ttl = Cache::get($cacheKey) - time(); // store expiry timestamp

            return response()->json([
                'status' => false,
                'message' => 'Terlalu sering meminta OTP. Coba lagi nanti.',
                'retry_after_seconds' => $ttl > 0 ? $ttl : 0,
            ], 429);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya blokir jika session flag 'otp_pending' aktif
        // Flag ini hanya di-set saat registrasi baru, bukan saat login biasa
        if (auth()->check() && session()->has('otp_pending')) {
            return redirect()->route('otp.verify');
        }

        return $next($request);
    }
}

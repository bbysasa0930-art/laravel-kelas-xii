<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Memastikan hanya user yang sudah login yang dapat mengakses route tertentu.
     * Jika belum login, simpan URL tujuan lalu arahkan ke halaman login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah login via Laravel Auth atau session manual
        if (!Auth::check() && !$request->session()->has('user_id')) {
            // Simpan URL yang ingin dituju ke session (untuk redirect setelah login)
            // Hanya simpan URL GET agar tidak menyimpan URL POST form
            if ($request->method() === 'GET') {
                $request->session()->put('url.intended', $request->url());
            }

            return redirect()->route('login')->withErrors([
                'login' => 'Silakan login terlebih dahulu.',
            ]);
        }

        return $next($request);
    }
}


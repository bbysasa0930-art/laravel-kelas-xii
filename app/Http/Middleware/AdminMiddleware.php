<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Memastikan hanya user dengan role 'admin' yang dapat mengakses halaman admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check() && !$request->session()->has('user_id')) {
            return redirect()->route('login')->withErrors([
                'login' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.',
            ]);
        }

        $user = Auth::user();
        $role = $user ? $user->role : $request->session()->get('role');

        // Periksa apakah role pengguna adalah admin
        if ($role !== 'admin' && (!$user || $user->role_id != 2)) {
            return redirect()->route('home')->with('error', 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        return $next($request);
    }
}


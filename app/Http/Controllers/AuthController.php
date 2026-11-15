<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan form halaman login.
     * Jika pengguna sudah login, arahkan langsung ke halaman utama.
     */
    public function showLogin()
    {
        if (Auth::check() || session()->has('user_id')) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Memproses autentikasi dan login pengguna.
     */
    public function login(Request $request)
    {
        // Cegah pengguna yang sudah login untuk memproses form login
        if (Auth::check() || session()->has('user_id')) {
            return redirect()->route('home');
        }

        // Menyelaraskan nama input form jika menggunakan nama field alternatif
        if (!$request->has('login')) {
            $inputLogin = $request->input('email') ?? $request->input('username') ?? $request->input('Username');
            if ($inputLogin !== null) {
                $request->merge(['login' => $inputLogin]);
            }
        }
        if (!$request->has('password') && $request->has('Password')) {
            $request->merge(['password' => $request->input('Password')]);
        }

        // Validasi input di sisi server
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = $request->input('login');
        $password = $request->input('password');

        // Menggunakan Eloquent ORM (Prepared Statements) untuk mencari user berdasarkan email atau username
        $user = User::where('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->first();

        // Verifikasi apakah user ditemukan dan password hash cocok
        if (!$user || !Hash::check($password, $user->password)) {
            // Pesan umum untuk keamanan, jangan membocorkan mana yang salah
            return back()->withInput($request->only('login'))->withErrors([
                'login' => 'Email atau password salah',
            ]);
        }

        // Login ke sistem otentikasi Laravel
        Auth::login($user);
        $request->session()->regenerate();

        // Simpan data penting ke session: id user, nama, dan role
        $roleName = $user->role ?? ($user->role_id == 2 ? 'admin' : 'user');
        $request->session()->put('user_id', $user->id);
        $request->session()->put('name', $user->name);
        $request->session()->put('role', $roleName);

        // Pengalihan berdasarkan role pengguna
        if ($roleName === 'admin' || $user->role_id == 2) {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang di Dashboard Admin, ' . $user->name . '!');
        }

        // Redirect ke URL yang semula dituju (misalnya halaman film saat mau komentar),
        // atau ke home jika tidak ada URL tersimpan.
        // intended() aman: hanya redirect ke URL internal (host yang sama)
        return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Menampilkan form halaman register.
     * Jika pengguna sudah login, arahkan langsung ke halaman utama.
     */
    public function showRegister()
    {
        if (Auth::check() || session()->has('user_id')) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Memproses pendaftaran user baru.
     */
    public function register(Request $request)
    {
        // Cegah pengguna yang sudah login
        if (Auth::check() || session()->has('user_id')) {
            return redirect()->route('home');
        }

        // Menyelaraskan field form jika terdapat variasi nama input
        if (!$request->has('name') && $request->has('Username')) {
            $request->merge(['name' => $request->input('Username')]);
        }
        if (!$request->has('email') && $request->has('Email')) {
            $request->merge(['email' => $request->input('Email')]);
        }
        if (!$request->has('password') && $request->has('Password')) {
            $request->merge(['password' => $request->input('Password')]);
        }
        if (!$request->has('password_confirmation') && $request->has('Password_confirmation')) {
            $request->merge(['password_confirmation' => $request->input('Password_confirmation')]);
        }

        // Validasi di sisi server
        // Semua field wajib diisi, format email valid, password min 8 karakter, konfirmasi sama, nama & email unik
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ], [
            'name.required' => 'Nama / username wajib diisi.',
            'name.unique' => 'Nama / username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password harus sama.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        // Buat record profil default untuk memenuhi constraint relasi tabel users ke profiles
        $profile = Profile::create([
            'umur' => 0,
            'bio' => 'Pengguna baru',
            'alamat' => '-',
        ]);

        // Buat user baru dengan password yang di-hash (bcrypt) dan role otomatis 'user'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password wajib di-hash menggunakan bcrypt
            'role' => 'user',                             // Role otomatis 'user'
            'role_id' => 1,                               // Relasi role_id 1 (user)
            'profile_id' => $profile->id,
        ]);

        // Arahkan ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    /**
     * Memproses logout pengguna.
     * Menghapus seluruh session dan mengarahkan ke halaman login.
     */
    public function logout(Request $request)
    {
        // Logout dari guard Laravel
        Auth::logout();

        // Hapus dan invalidasi session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Menampilkan dashboard admin.
     * Hanya dapat diakses oleh user dengan role admin.
     */
    public function adminDashboard()
    {
        $totalUsers = User::count();
        $totalFilms = Film::count();
        $totalComments = \App\Models\Kritik::count();
        $totalCasts = \App\Models\Cast::count();
        $recentFilms = Film::with('genre')->orderByDesc('id')->limit(5)->get();
        $recentComments = \App\Models\Kritik::with(['user', 'film'])->orderByDesc('id')->limit(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalFilms', 'totalComments', 'totalCasts', 'recentFilms', 'recentComments'));
    }
}


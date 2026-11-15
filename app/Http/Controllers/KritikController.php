<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Kritik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KritikController extends Controller
{
    /**
     * Menyimpan komentar baru untuk sebuah film.
     * Hanya bisa diakses oleh user yang sudah login (dijaga oleh middleware 'auth').
     * user_id diambil dari session/Auth, BUKAN dari input form.
     */
    public function store(Request $request, Film $film)
    {
        // Perlindungan ganda di sisi server: pastikan user benar-benar sudah login
        // (tidak boleh hanya mengandalkan middleware, karena request bisa dimanipulasi)
        if (!Auth::check() && !$request->session()->has('user_id')) {
            return redirect()->route('login')->withErrors([
                'login' => 'Silakan login untuk mengirim komentar.',
            ]);
        }

        // Validasi input komentar di sisi server
        $request->validate([
            'comment' => [
                'required',
                'string',
                'min:3',
                'max:500',
            ],
            'rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:5',
            ],
        ], [
            'comment.required' => 'Komentar tidak boleh kosong.',
            'comment.min'      => 'Komentar minimal 3 karakter.',
            'comment.max'      => 'Komentar maksimal 500 karakter.',
            'rating.integer'   => 'Rating harus berupa angka.',
            'rating.min'       => 'Rating minimal 1.',
            'rating.max'       => 'Rating maksimal 5.',
        ]);

        // Ambil ID user dari Auth atau session — BUKAN dari input form
        // agar user tidak bisa memalsukan komentar atas nama orang lain
        $userId = Auth::id() ?? $request->session()->get('user_id');

        // Simpan komentar ke database menggunakan Eloquent ORM (Prepared Statement)
        // Catatan: kolom rating di skema database bersifat NOT NULL, jadi jika kosong beri nilai default 5 atau nilai input
        Kritik::create([
            'comment' => $request->input('comment'), // Blade {{ }} akan escape output saat ditampilkan
            'rating'  => $request->filled('rating') ? (int) $request->input('rating') : 5,
            'film_id' => $film->id,
            'user_id' => $userId,
        ]);

        // Arahkan kembali ke halaman film yang sama dengan pesan sukses
        return redirect()
            ->route('movies.show', $film->id)
            ->with('success_comment', 'Komentar berhasil dikirim!');
    }
}

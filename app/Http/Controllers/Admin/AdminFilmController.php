<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Cast;
use App\Models\Peran;
use App\Models\Kritik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFilmController extends Controller
{
    /**
     * Menampilkan daftar semua film dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Query menggunakan Prepared Statements ORM
        $films = Film::with('genre')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.films.index', compact('films', 'search'));
    }

    /**
     * Menampilkan form untuk menambah data film baru.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        $casts = Cast::orderBy('name')->get();

        return view('admin.films.create', compact('genres', 'casts'));
    }

    /**
     * Menyimpan data film baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi server-side lengkap
        $request->validate([
            'title' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'genre_id' => 'required|exists:genres,id',
            'poster' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // Maksimal 2 MB
            'cast_ids' => 'nullable|array',
            'cast_ids.*' => 'exists:casts,id',
        ], [
            'title.required' => 'Judul film wajib diisi.',
            'sinopsis.required' => 'Sinopsis / deskripsi film wajib diisi.',
            'year.required' => 'Tahun rilis wajib diisi.',
            'year.integer' => 'Tahun rilis harus berupa angka.',
            'genre_id.required' => 'Genre film wajib dipilih.',
            'genre_id.exists' => 'Genre yang dipilih tidak valid.',
            'poster.required' => 'File poster film wajib diunggah.',
            'poster.image' => 'File yang diunggah harus berupa gambar.',
            'poster.mimes' => 'Format poster yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'poster.max' => 'Ukuran file poster maksimal adalah 2 MB.',
        ]);

        $posterPath = null;
        if ($request->hasFile('poster')) {
            // Simpan poster ke storage/app/public/posters dengan nama unik
            $path = $request->file('poster')->store('posters', 'public');
            $posterPath = 'storage/' . $path;
        }

        // Simpan data film
        $film = Film::create([
            'title' => $request->title,
            'sinopsis' => $request->sinopsis,
            'year' => $request->year,
            'genre_id' => $request->genre_id,
            'poster' => $posterPath,
        ]);

        // Hubungkan cast yang dipilih melalui tabel relasi perans
        if ($request->filled('cast_ids')) {
            foreach ($request->cast_ids as $castId) {
                $cast = Cast::find($castId);
                Peran::create([
                    'film_id' => $film->id,
                    'cast_id' => $castId,
                    'actor' => $cast ? $cast->name : 'Pemeran',
                ]);
            }
        }

        return redirect()->route('admin.films.index')->with('success', 'Film "' . $film->title . '" berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit film.
     */
    public function edit(Film $film)
    {
        $genres = Genre::orderBy('name')->get();
        $casts = Cast::orderBy('name')->get();
        $selectedCastIds = $film->castMembers()->pluck('casts.id')->toArray();

        return view('admin.films.edit', compact('film', 'genres', 'casts', 'selectedCastIds'));
    }

    /**
     * Memperbarui data film di database.
     */
    public function update(Request $request, Film $film)
    {
        // Validasi data
        $request->validate([
            'title' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'genre_id' => 'required|exists:genres,id',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Opsional saat update, maks 2 MB
            'cast_ids' => 'nullable|array',
            'cast_ids.*' => 'exists:casts,id',
        ], [
            'title.required' => 'Judul film wajib diisi.',
            'sinopsis.required' => 'Sinopsis / deskripsi film wajib diisi.',
            'year.required' => 'Tahun rilis wajib diisi.',
            'genre_id.required' => 'Genre film wajib dipilih.',
            'genre_id.exists' => 'Genre yang dipilih tidak valid.',
            'poster.image' => 'File yang diunggah harus berupa gambar.',
            'poster.mimes' => 'Format poster yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'poster.max' => 'Ukuran file poster maksimal adalah 2 MB.',
        ]);

        $data = [
            'title' => $request->title,
            'sinopsis' => $request->sinopsis,
            'year' => $request->year,
            'genre_id' => $request->genre_id,
        ];

        // Jika ada poster baru yang di-upload
        if ($request->hasFile('poster')) {
            // Hapus file poster lama jika ada
            if ($film->poster) {
                $oldPath = str_replace('storage/', '', $film->poster);
                Storage::disk('public')->delete($oldPath);
            }

            // Simpan file poster baru
            $path = $request->file('poster')->store('posters', 'public');
            $data['poster'] = 'storage/' . $path;
        }

        $film->update($data);

        // Sinkronisasi relasi cast di tabel perans
        Peran::where('film_id', $film->id)->delete();
        if ($request->filled('cast_ids')) {
            foreach ($request->cast_ids as $castId) {
                $cast = Cast::find($castId);
                Peran::create([
                    'film_id' => $film->id,
                    'cast_id' => $castId,
                    'actor' => $cast ? $cast->name : 'Pemeran',
                ]);
            }
        }

        return redirect()->route('admin.films.index')->with('success', 'Data film "' . $film->title . '" berhasil diperbarui.');
    }

    /**
     * Menghapus film dan menangani relasi terkait (cast di perans & komentar di kritiks).
     */
    public function destroy(Film $film)
    {
        $title = $film->title;

        // 1. Hapus relasi cast pada film tersebut (tabel perans)
        Peran::where('film_id', $film->id)->delete();

        // 2. Hapus komentar/kritik pada film tersebut (tabel kritiks)
        Kritik::where('film_id', $film->id)->delete();

        // 3. Hapus file fisik poster jika ada di storage
        if ($film->poster) {
            $oldPath = str_replace('storage/', '', $film->poster);
            Storage::disk('public')->delete($oldPath);
        }

        // 4. Hapus data film
        $film->delete();

        return redirect()->route('admin.films.index')->with('success', 'Film "' . $title . '" beserta data relasinya berhasil dihapus.');
    }
}


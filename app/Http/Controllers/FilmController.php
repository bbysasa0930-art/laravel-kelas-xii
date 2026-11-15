<?php

namespace App\Http\Controllers;

use App\Models\{
    Film,
    Genre,
    Kritik,
    Peran,
};
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    /**
     * Menampilkan daftar semua film untuk pengunjung.
     */
    public function movies()
    {
        $genreFilm = null;
        // Mengambil daftar film beserta 2-3 cast utama dengan eager loading
        $films = Film::select('id', 'title', 'poster', 'year')
            ->with(['castMembers' => function ($query) {
                $query->select('casts.id', 'casts.name');
            }])
            ->orderByDesc('year')
            ->orderBy('created_at', 'asc')
            ->paginate(18);

        return view('components/movies', compact('films', 'genreFilm'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilmRequest $request)
    {
        //
    }

    /**
     * Menampilkan halaman detail film beserta daftar cast (menggunakan SATU query JOIN).
     */
    public function show(Film $film)
    {
        $filmByGenre = Film::select('id', 'title', 'poster', 'year', 'sinopsis')
            ->where('genre_id', '=', $film->genre_id)
            ->orderBy('created_at', 'asc')
            ->limit(7)
            ->get();

        $filmByRelease = Film::select('id', 'title', 'poster', 'year')
            ->where('year', '=', Carbon::now()->format('Y'))
            ->orderBy('created_at', 'asc')
            ->limit(9)
            ->get();

        // Mengambil daftar komentar dengan LEFT JOIN ke tabel users (Prepared Statement)
        // Diurutkan dari komentar yang terbaru (created_at DESC)
        $comments = DB::table('kritiks')
            ->leftJoin('users', 'kritiks.user_id', '=', 'users.id')
            ->where('kritiks.film_id', '=', $film->id)
            ->select(
                'kritiks.id',
                'kritiks.comment',
                'kritiks.rating',
                'kritiks.user_id',
                'kritiks.created_at',
                'users.name as user_name'
            )
            ->orderBy('kritiks.created_at', 'desc')
            ->limit(20)
            ->get();

        // Mengambil data cast dan perannya menggunakan SATU query dengan JOIN (Prepared Statement)
        $casts = DB::table('perans')
            ->join('casts', 'perans.cast_id', '=', 'casts.id')
            ->where('perans.film_id', $film->id)
            ->select(
                'casts.id as cast_id',
                'casts.name as cast_name',
                'casts.foto as cast_foto',
                'casts.age as cast_age',
                'casts.bio as cast_bio',
                'perans.actor as role_name'
            )
            ->orderBy('casts.name', 'asc')
            ->get();

        // Menyimpan alias $perans agar kompatibel dengan view yang lama
        $perans = $casts;

        return view('components.movie-show', compact('film', 'filmByGenre', 'filmByRelease', 'comments', 'casts', 'perans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Film $film)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film)
    {
        //
    }

    /**
     * Menampilkan beranda film (Home).
     */
    public function movieHome()
    {
        $films = Film::select('id', 'title', 'poster', 'year')
            ->with(['castMembers' => function ($query) {
                $query->select('casts.id', 'casts.name');
            }])
            ->orderBy('created_at', 'asc')
            ->limit(9)
            ->get();

        $filmFutured = Film::select('id', 'title', 'poster', 'year')
            ->orderByDesc('year')
            ->orderBy('created_at', 'asc')
            ->limit(6)
            ->get();

        $filmRecentAdded = Film::select('id', 'title', 'poster', 'year')
            ->orderByDesc('created_at', 'asc')
            ->limit(6)
            ->get();

        $filmSlidey = Film::select('title', 'poster', 'sinopsis')
            ->orderByDesc('year')
            ->orderBy('created_at', 'asc')
            ->limit(6)
            ->get();

        return view('components/home', compact('filmSlidey', 'films', 'filmFutured', 'filmRecentAdded'));
    }

    /**
     * Menampilkan daftar film berdasarkan genre.
     */
    public function moviesByGenre($genre)
    {
        $genreFilm = Genre::where('id', '=', $genre)->first();
        $films = Film::select('id', 'title', 'poster', 'year')
            ->with(['castMembers' => function ($query) {
                $query->select('casts.id', 'casts.name');
            }])
            ->where('genre_id', '=', $genre)
            ->orderByDesc('year')
            ->orderBy('created_at', 'asc')
            ->paginate(18);

        return view('components/movies', compact('films', 'genreFilm'));
    }
}

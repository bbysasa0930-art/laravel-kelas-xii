<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FilmController,
    AuthController,
    KritikController,
};
use App\Http\Controllers\Admin\{
    AdminFilmController,
    AdminCastController,
    AdminCommentController,
};

// Rute Halaman Publik
Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');

// Rute Komentar - hanya bisa diakses user yang sudah login (middleware 'auth')
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

// Rute Autentikasi untuk Tamu (Guest) - Tidak dapat diakses jika sudah login
Route::middleware('guest')->group(function () {
    // Form & proses Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Form & proses Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Rute Logout (Mendukung POST dan GET agar fleksibel)
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Khusus Administrator (Wajib Login & Memiliki Role 'admin')
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [AuthController::class, 'adminDashboard'])->name('dashboard');

    // CRUD Film
    Route::resource('films', AdminFilmController::class)->except(['show']);

    // CRUD Cast
    Route::resource('casts', AdminCastController::class)->except(['show']);

    // Kelola Komentar
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});

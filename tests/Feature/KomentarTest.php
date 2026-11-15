<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KomentarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan role user (1) dan admin (2) tersedia untuk memenuhi FK role_id
     */
    protected function pastikanRoles()
    {
        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);
    }

    /**
     * Buat genre dummy (syarat FK film)
     */
    protected function buatGenre(): Genre
    {
        return Genre::firstOrCreate(['name' => 'Action']);
    }

    /**
     * Buat film dummy yang memenuhi semua constraint FK
     */
    protected function buatFilm(): Film
    {
        $genre = $this->buatGenre();
        return Film::create([
            'title'    => 'Film Tes Komentar',
            'sinopsis' => 'Sinopsis tes',
            'year'     => 2024,
            'genre_id' => $genre->id,
            'poster'   => 'storage/posters/dummy.jpg',
        ]);
    }

    /**
     * Buat user dummy yang memenuhi semua constraint FK
     */
    protected function buatUser(string $role = 'user', string $password = 'password123'): User
    {
        $this->pastikanRoles();

        $profile = Profile::create(['umur' => 20, 'bio' => 'tes', 'alamat' => 'tes']);

        return User::create([
            'name'       => 'User Tes ' . uniqid(),
            'email'      => 'tes' . uniqid() . '@mail.com',
            'password'   => Hash::make($password),
            'role'       => $role,
            'role_id'    => $role === 'admin' ? 2 : 1,
            'profile_id' => $profile->id,
        ]);
    }

    // ─── Skenario 1: User BELUM login (tamu) ──────────────────────────────

    /** Halaman detail film bisa diakses siapapun */
    public function test_tamu_bisa_lihat_halaman_film()
    {
        $film = $this->buatFilm();
        $res  = $this->get(route('movies.show', $film->id));
        $res->assertStatus(200);
    }

    /** Tamu melihat pesan login, bukan form komentar */
    public function test_tamu_melihat_pesan_login_bukan_form_komentar()
    {
        $film = $this->buatFilm();
        $res  = $this->get(route('movies.show', $film->id));
        $res->assertStatus(200);
        $res->assertSee('Silakan login untuk memberikan komentar');
    }

    /** Tamu POST komentar langsung → redirect ke login (proteksi server) */
    public function test_tamu_tidak_bisa_kirim_komentar_via_post()
    {
        $film = $this->buatFilm();

        // Kirim POST tanpa login (tamu)
        $res = $this->post(route('comments.store', $film->id), [
            'comment' => 'Komentar dari tamu yang tidak login',
        ]);

        // Harus redirect ke login — BUKAN menyimpan komentar
        $res->assertRedirect(route('login'));

        // Pastikan tidak ada komentar tersimpan di DB
        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $film->id,
        ]);
    }

    // ─── Skenario 2: User SUDAH login ─────────────────────────────────────

    /** User login bisa melihat form komentar */
    public function test_user_login_melihat_form_komentar()
    {
        $user = $this->buatUser();
        $film = $this->buatFilm();

        $res = $this->actingAs($user)->get(route('movies.show', $film->id));
        $res->assertStatus(200);
        $res->assertSee('Tulis Komentar');
    }

    /** User login bisa kirim komentar dan komentar tersimpan di DB */
    public function test_user_login_bisa_kirim_komentar()
    {
        $user = $this->buatUser();
        $film = $this->buatFilm();

        $res = $this->actingAs($user)->post(route('comments.store', $film->id), [
            'comment' => 'Komentar valid dari user yang login.',
            'rating'  => 4,
        ]);

        // Redirect kembali ke halaman film
        $res->assertRedirect(route('movies.show', $film->id));

        // Komentar harus tersimpan dengan user_id dari sesi (Auth), bukan input form
        $this->assertDatabaseHas('kritiks', [
            'film_id' => $film->id,
            'user_id' => $user->id,
            'comment' => 'Komentar valid dari user yang login.',
            'rating'  => 4,
        ]);
    }

    /** Komentar kosong ditolak dengan pesan error */
    public function test_komentar_kosong_ditolak()
    {
        $user = $this->buatUser();
        $film = $this->buatFilm();

        $res = $this->actingAs($user)->post(route('comments.store', $film->id), [
            'comment' => '',
        ]);

        $res->assertSessionHasErrors('comment');
        $this->assertDatabaseCount('kritiks', 0);
    }

    /** Komentar melebihi 500 karakter ditolak */
    public function test_komentar_terlalu_panjang_ditolak()
    {
        $user = $this->buatUser();
        $film = $this->buatFilm();

        $res = $this->actingAs($user)->post(route('comments.store', $film->id), [
            'comment' => str_repeat('x', 501),
        ]);

        $res->assertSessionHasErrors('comment');
        $this->assertDatabaseCount('kritiks', 0);
    }

    /** user_id TIDAK bisa dipalsukan lewat input form */
    public function test_user_id_diambil_dari_session_bukan_form()
    {
        $user1 = $this->buatUser();
        $user2 = $this->buatUser();
        $film  = $this->buatFilm();

        // Login sebagai user1, kirim user_id=user2 lewat form (coba spoofing)
        $res = $this->actingAs($user1)->post(route('comments.store', $film->id), [
            'comment' => 'Komentar dengan user_id palsu',
            'user_id' => $user2->id, // server wajib mengabaikan ini
        ]);

        $res->assertRedirect(route('movies.show', $film->id));

        // Harus tersimpan atas nama user1 (dari Auth)
        $this->assertDatabaseHas('kritiks', [
            'film_id' => $film->id,
            'user_id' => $user1->id,
        ]);
        // Jangan tersimpan atas nama user2
        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $film->id,
            'user_id' => $user2->id,
        ]);
    }

    // ─── Skenario 3: Redirect setelah login ───────────────────────────────

    /** Setelah login, user diarahkan ke URL film yang semula dituju */
    public function test_redirect_ke_url_semula_setelah_login()
    {
        $user = $this->buatUser(password: 'password123');
        $film = $this->buatFilm();

        // Simulasikan: session menyimpan URL tujuan seperti yang dilakukan AuthMiddleware
        $this->withSession(['url.intended' => route('movies.show', $film->id)]);

        $res = $this->post(route('login.post'), [
            'login'    => $user->email,
            'password' => 'password123',
        ]);

        // Harus diarahkan ke URL film yang semula dituju
        $res->assertRedirect(route('movies.show', $film->id));
    }
}

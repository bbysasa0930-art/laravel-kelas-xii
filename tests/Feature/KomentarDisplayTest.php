<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KomentarDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
    }

    public function test_menampilkan_nama_user_dan_format_tanggal_pada_komentar()
    {
        $genre = Genre::firstOrCreate(['name' => 'Action']);
        $film = Film::create([
            'title'    => 'Film Tes Komentar',
            'sinopsis' => 'Sinopsis',
            'year'     => 2024,
            'genre_id' => $genre->id,
            'poster'   => 'storage/posters/dummy.jpg',
        ]);

        $profile = Profile::create(['umur' => 20, 'bio' => 'bio', 'alamat' => 'alamat']);
        $user = User::create([
            'name'       => 'Budi Santoso',
            'email'      => 'budi@mail.com',
            'password'   => bcrypt('password123'),
            'role'       => 'user',
            'role_id'    => 1,
            'profile_id' => $profile->id,
        ]);

        $k = new Kritik([
            'comment'    => 'Film ini sangat mendalam ceritanya.',
            'rating'     => 5,
            'film_id'    => $film->id,
            'user_id'    => $user->id,
        ]);
        $k->created_at = '2026-10-27 14:30:00';
        $k->save();

        $res = $this->get(route('movies.show', $film->id));

        $res->assertStatus(200);
        // Memastikan nama user muncul, bukan id
        $res->assertSee('Budi Santoso');
        $res->assertDontSee('User #' . $user->id);
        // Memastikan komentar muncul
        $res->assertSee('Film ini sangat mendalam ceritanya.');
        // Memastikan tanggal dan jam muncul
        $res->assertSee('27');
        $res->assertSee('2026');
        $res->assertSee('14:30');
    }

    public function test_fallback_pengguna_jika_user_name_kosong()
    {
        $genre = Genre::firstOrCreate(['name' => 'Action']);
        $film = Film::create([
            'title'    => 'Film Tanpa User Cocok',
            'sinopsis' => 'Sinopsis',
            'year'     => 2024,
            'genre_id' => $genre->id,
            'poster'   => 'storage/posters/dummy.jpg',
        ]);

        // Buat user dengan nama string kosong atau tanpa nama untuk menguji fallback
        $profile = Profile::create(['umur' => 20, 'bio' => 'bio', 'alamat' => 'alamat']);
        $user = User::create([
            'name'       => '',
            'email'      => 'noname@mail.com',
            'password'   => bcrypt('password123'),
            'role'       => 'user',
            'role_id'    => 1,
            'profile_id' => $profile->id,
        ]);

        Kritik::create([
            'comment'    => 'Komentar dari user tanpa nama.',
            'rating'     => 4,
            'film_id'    => $film->id,
            'user_id'    => $user->id,
            'created_at' => now(),
        ]);

        $res = $this->get(route('movies.show', $film->id));

        $res->assertStatus(200);
        // Harus menampilkan fallback 'Pengguna'
        $res->assertSee('Pengguna');
    }
}

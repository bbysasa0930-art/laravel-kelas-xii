<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Cast;
use App\Models\Peran;
use App\Models\Kritik;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminCrudTest extends TestCase
{
    protected User $admin;
    protected User $regularUser;
    protected Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Pastikan role id 1 (user) dan id 2 (admin) tersedia untuk relasi FK
        \App\Models\Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        \App\Models\Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        // Buat data genre untuk pengujian jika belum ada
        $this->genre = Genre::firstOrCreate(['name' => 'Action Test']);

        // Buat akun admin
        $adminProfile = Profile::create(['umur' => 30, 'bio' => 'Admin Bio', 'alamat' => 'Alamat']);
        $this->admin = User::create([
            'name' => 'Admin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $adminProfile->id,
        ]);

        // Buat akun user biasa
        $userProfile = Profile::create(['umur' => 22, 'bio' => 'User Bio', 'alamat' => 'Alamat']);
        $this->regularUser = User::create([
            'name' => 'User_' . uniqid(),
            'email' => 'user_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $userProfile->id,
        ]);
    }

    /**
     * Memastikan akses dashboard dan seluruh aksi admin ditolak untuk guest & user biasa.
     */
    public function test_admin_access_protection_for_guest_and_regular_user(): void
    {
        // 1. Guest diarahkan ke login
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin/films')->assertRedirect('/login');
        $this->get('/admin/casts')->assertRedirect('/login');
        $this->get('/admin/comments')->assertRedirect('/login');

        // 2. User biasa diarahkan ke home dengan pesan akses ditolak
        $this->actingAs($this->regularUser)->get('/admin/dashboard')->assertRedirect(route('home'));
        $this->actingAs($this->regularUser)->get('/admin/films')->assertRedirect(route('home'));
        $this->actingAs($this->regularUser)->get('/admin/casts')->assertRedirect(route('home'));
        $this->actingAs($this->regularUser)->get('/admin/comments')->assertRedirect(route('home'));

        // Coba aksi POST/DELETE oleh user biasa
        $this->actingAs($this->regularUser)->post('/admin/films', [])->assertRedirect(route('home'));
        $this->actingAs($this->regularUser)->post('/admin/casts', [])->assertRedirect(route('home'));
    }

    /**
     * Memastikan dashboard admin menampilkan ringkasan statistik.
     */
    public function test_admin_dashboard_displays_statistics(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Utama');
        $response->assertSee('Total Film');
        $response->assertSee('Total Pengguna');
        $response->assertSee('Total Komentar');
    }

    /**
     * Memastikan halaman index film dapat dibuka dan fitur pencarian berfungsi.
     */
    public function test_admin_can_read_and_search_films(): void
    {
        $uniqueTitle = 'Film_Unik_' . uniqid();
        Film::create([
            'title' => $uniqueTitle,
            'sinopsis' => 'Sinopsis film unik',
            'year' => 2024,
            'genre_id' => $this->genre->id,
            'poster' => 'storage/posters/dummy.jpg',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/films');
        $response->assertStatus(200);
        $response->assertSee($uniqueTitle);

        // Uji pencarian
        $searchResponse = $this->actingAs($this->admin)->get('/admin/films?search=' . $uniqueTitle);
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($uniqueTitle);
    }

    /**
     * Memastikan validasi create film gagal jika field kosong atau poster tidak valid.
     */
    public function test_admin_film_create_validation_fails(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/films', []);
        $response->assertSessionHasErrors(['title', 'sinopsis', 'year', 'genre_id', 'poster']);

        // Uji file poster bukan gambar
        $fakePdf = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');
        $responsePdf = $this->actingAs($this->admin)->post('/admin/films', [
            'title' => 'Film Baru',
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'genre_id' => $this->genre->id,
            'poster' => $fakePdf,
        ]);
        $responsePdf->assertSessionHasErrors(['poster']);
    }

    /**
     * Memastikan admin dapat menambah film baru beserta upload poster dan relasi cast.
     */
    public function test_admin_can_create_film_with_poster_and_cast(): void
    {
        $cast1 = Cast::create(['name' => 'Actor Satu', 'age' => 35, 'bio' => 'Bio 1']);
        $cast2 = Cast::create(['name' => 'Actor Dua', 'age' => 28, 'bio' => 'Bio 2']);

        $fakeImage = UploadedFile::fake()->image('poster_baru.jpg', 600, 800);
        $filmTitle = 'Film Action ' . uniqid();

        $response = $this->actingAs($this->admin)->post('/admin/films', [
            'title' => $filmTitle,
            'sinopsis' => 'Sinopsis film action seru',
            'year' => 2024,
            'genre_id' => $this->genre->id,
            'poster' => $fakeImage,
            'cast_ids' => [$cast1->id, $cast2->id],
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        // Pastikan film tersimpan di database
        $film = Film::where('title', $filmTitle)->first();
        $this->assertNotNull($film);
        $this->assertEquals(2024, $film->year);
        $this->assertNotNull($film->poster);

        $storagePath = str_replace('storage/', '', $film->poster);
        Storage::disk('public')->assertExists($storagePath);

        // Pastikan relasi cast tersimpan di perans
        $perans = Peran::where('film_id', $film->id)->pluck('cast_id')->toArray();
        $this->assertContains($cast1->id, $perans);
        $this->assertContains($cast2->id, $perans);
    }

    /**
     * Memastikan admin dapat mengedit dan memperbarui data film serta mengganti posternya.
     */
    public function test_admin_can_update_film(): void
    {
        $filmTitle = 'Film Update ' . uniqid();

        $film = Film::create([
            'title' => $filmTitle,
            'sinopsis' => 'Sinopsis awal',
            'year' => 2023,
            'genre_id' => $this->genre->id,
            'poster' => 'storage/posters/old_poster.jpg',
        ]);
        Storage::disk('public')->put('posters/old_poster.jpg', 'fake image content');

        // Form edit bisa dibuka
        $responseEdit = $this->actingAs($this->admin)->get('/admin/films/' . $film->id . '/edit');
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee($filmTitle);

        // Upload poster baru pengganti
        $newPoster = UploadedFile::fake()->image('poster_pengganti.png', 600, 800);
        $updatedTitle = 'Film Judul Baru ' . uniqid();

        $responseUpdate = $this->actingAs($this->admin)->put('/admin/films/' . $film->id, [
            'title' => $updatedTitle,
            'sinopsis' => 'Sinopsis telah diperbarui',
            'year' => 2025,
            'genre_id' => $this->genre->id,
            'poster' => $newPoster,
        ]);

        $responseUpdate->assertRedirect(route('admin.films.index'));
        $responseUpdate->assertSessionHas('success');

        $film->refresh();
        $this->assertEquals($updatedTitle, $film->title);
        $this->assertEquals(2025, $film->year);
        Storage::disk('public')->assertMissing('posters/old_poster.jpg'); // Poster lama terhapus

        $newStoragePath = str_replace('storage/', '', $film->poster);
        Storage::disk('public')->assertExists($newStoragePath); // Poster baru ada
    }

    /**
     * Memastikan penghapusan film menangani relasi cast (perans) dan komentar (kritiks).
     */
    public function test_admin_can_delete_film_and_cascade_relations(): void
    {
        $cast = Cast::create(['name' => 'Cast Aktor', 'age' => 40, 'bio' => 'Bio']);
        $film = Film::create([
            'title' => 'Film Dihapus ' . uniqid(),
            'sinopsis' => 'Sinopsis',
            'year' => 2024,
            'genre_id' => $this->genre->id,
            'poster' => 'storage/posters/to_delete.jpg',
        ]);
        Storage::disk('public')->put('posters/to_delete.jpg', 'image content');

        // Hubungkan cast
        Peran::create(['film_id' => $film->id, 'cast_id' => $cast->id, 'actor' => 'Pemeran']);

        // Tambah komentar pada film ini
        Kritik::create([
            'film_id' => $film->id,
            'user_id' => $this->regularUser->id,
            'comment' => 'Komentar pada film ini',
            'rating' => 8,
        ]);

        $this->assertEquals(1, Peran::where('film_id', $film->id)->count());
        $this->assertEquals(1, Kritik::where('film_id', $film->id)->count());

        $response = $this->actingAs($this->admin)->delete('/admin/films/' . $film->id);
        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        // Pastikan film terhapus
        $this->assertNull(Film::find($film->id));
        // Pastikan relasi peran terhapus
        $this->assertEquals(0, Peran::where('film_id', $film->id)->count());
        // Pastikan komentar film terhapus
        $this->assertEquals(0, Kritik::where('film_id', $film->id)->count());
        // Pastikan file poster terhapus
        Storage::disk('public')->assertMissing('posters/to_delete.jpg');
    }

    /**
     * Memastikan CRUD Cast berfungsi (Index, Create, Update, Delete).
     */
    public function test_admin_crud_cast(): void
    {
        // 1. Index
        $responseIndex = $this->actingAs($this->admin)->get('/admin/casts');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Kelola Data Cast');

        // 2. Create Cast
        $castName = 'Aktor Bintang ' . uniqid();
        $fakePhoto = UploadedFile::fake()->image('aktor.jpg', 300, 300);

        $responseCreate = $this->actingAs($this->admin)->post('/admin/casts', [
            'name' => $castName,
            'age' => 38,
            'bio' => 'Aktor pemenang penghargaan',
            'foto' => $fakePhoto,
        ]);

        $responseCreate->assertRedirect(route('admin.casts.index'));
        $responseCreate->assertSessionHas('success');

        $cast = Cast::where('name', $castName)->first();
        $this->assertNotNull($cast);
        $this->assertEquals(38, $cast->age);
        $this->assertNotNull($cast->foto);

        $fotoStoragePath = str_replace('storage/', '', $cast->foto);
        Storage::disk('public')->assertExists($fotoStoragePath);

        // 3. Edit & Update Cast
        $responseEdit = $this->actingAs($this->admin)->get('/admin/casts/' . $cast->id . '/edit');
        $responseEdit->assertStatus(200);

        $updatedCastName = 'Aktor Diperbarui ' . uniqid();
        $responseUpdate = $this->actingAs($this->admin)->put('/admin/casts/' . $cast->id, [
            'name' => $updatedCastName,
            'age' => 39,
            'bio' => 'Bio baru',
        ]);
        $responseUpdate->assertRedirect(route('admin.casts.index'));

        $cast->refresh();
        $this->assertEquals($updatedCastName, $cast->name);
        $this->assertEquals(39, $cast->age);

        // 4. Delete Cast
        $responseDelete = $this->actingAs($this->admin)->delete('/admin/casts/' . $cast->id);
        $responseDelete->assertRedirect(route('admin.casts.index'));
        $this->assertNull(Cast::find($cast->id));
        Storage::disk('public')->assertMissing($fotoStoragePath);
    }

    /**
     * Memastikan Kelola Komentar berfungsi (Index dan Delete).
     */
    public function test_admin_can_read_and_delete_comments(): void
    {
        $film = Film::create([
            'title' => 'Film Review ' . uniqid(),
            'sinopsis' => 'Sinopsis review',
            'year' => 2024,
            'genre_id' => $this->genre->id,
            'poster' => 'storage/posters/test.jpg',
        ]);

        $uniqueCommentText = 'Ini komentar testing unik ' . uniqid();
        $comment = Kritik::create([
            'film_id' => $film->id,
            'user_id' => $this->regularUser->id,
            'comment' => $uniqueCommentText,
            'rating' => 9,
        ]);

        // 1. Tampilkan komentar
        $responseIndex = $this->actingAs($this->admin)->get('/admin/comments');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee($uniqueCommentText);

        // 2. Hapus komentar
        $responseDelete = $this->actingAs($this->admin)->delete('/admin/comments/' . $comment->id);
        $responseDelete->assertRedirect(route('admin.comments.index'));
        $responseDelete->assertSessionHas('success');

        // Pastikan komentar terhapus
        $this->assertNull(Kritik::find($comment->id));
    }
}


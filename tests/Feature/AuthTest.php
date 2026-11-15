<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthTest extends TestCase
{
    /**
     * Memastikan halaman register dapat diakses oleh guest.
     */
    public function test_registration_page_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
    }

    /**
     * Memastikan validasi server-side gagal jika ada field yang kosong.
     */
    public function test_registration_validation_fails_when_fields_are_empty(): void
    {
        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['name', 'email', 'password', 'password_confirmation']);
    }

    /**
     * Memastikan format email harus valid.
     */
    public function test_registration_validation_fails_on_invalid_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Testing User',
            'email' => 'bukan-email-valid',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Memastikan password minimal 8 karakter.
     */
    public function test_registration_validation_fails_when_password_less_than_8_chars(): void
    {
        $response = $this->post('/register', [
            'name' => 'Testing User',
            'email' => 'testuser@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Memastikan konfirmasi password harus sama dengan password.
     */
    public function test_registration_validation_fails_when_password_confirmation_mismatches(): void
    {
        $response = $this->post('/register', [
            'name' => 'Testing User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'berbeda123',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Memastikan email dan username tidak boleh duplikat.
     */
    public function test_registration_validation_fails_on_duplicate_email_or_username(): void
    {
        $uniqueName = 'user_unique_' . uniqid();
        $uniqueEmail = 'user_' . uniqid() . '@example.com';

        $profile = Profile::create(['umur' => 20, 'bio' => '-', 'alamat' => '-']);
        User::create([
            'name' => $uniqueName,
            'email' => $uniqueEmail,
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        // Coba register dengan email yang sama
        $responseEmail = $this->post('/register', [
            'name' => 'other_name_' . uniqid(),
            'email' => $uniqueEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responseEmail->assertSessionHasErrors(['email']);

        // Coba register dengan name yang sama
        $responseName = $this->post('/register', [
            'name' => $uniqueName,
            'email' => 'other_' . uniqid() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responseName->assertSessionHasErrors(['name']);
    }

    /**
     * Memastikan registrasi berhasil:
     * - Password di-hash (bukan plain text)
     * - Role otomatis 'user'
     * - Redirect ke halaman login dengan pesan sukses
     */
    public function test_registration_success_hashes_password_sets_role_user_and_redirects(): void
    {
        $name = 'newuser_' . uniqid();
        $email = 'newuser_' . uniqid() . '@example.com';
        $plainPassword = 'SecretPassword123';

        $response = $this->post('/register', [
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        // Pastikan user tersimpan di database
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals($name, $user->name);
        $this->assertEquals('user', $user->role);
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    /**
     * Memastikan halaman login dapat dibuka.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun Anda');
    }

    /**
     * Memastikan login gagal menampilkan pesan 'Email atau password salah' tanpa membocorkan mana yang salah.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => 'user_tidak_ada@example.com',
            'password' => 'salahpassword',
        ]);

        $response->assertSessionHasErrors(['login']);
        $errors = session('errors')->get('login');
        $this->assertContains('Email atau password salah', $errors);
        $this->assertFalse(Auth::check());
    }

    /**
     * Memastikan login berhasil dengan email, menyimpan data ke session, dan mengarahkan user ke halaman utama.
     */
    public function test_regular_user_login_success_stores_session_and_redirects_home(): void
    {
        $email = 'user_login_' . uniqid() . '@example.com';
        $password = 'mypassword123';
        $profile = Profile::create(['umur' => 22, 'bio' => 'User bio', 'alamat' => 'Alamat']);

        $user = User::create([
            'name' => 'Regular User ' . uniqid(),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login' => $email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertEquals($user->id, session('user_id'));
        $this->assertEquals($user->name, session('name'));
        $this->assertEquals('user', session('role'));
    }

    /**
     * Memastikan login berhasil menggunakan username (nama).
     */
    public function test_login_success_with_username(): void
    {
        $name = 'UserNama_' . uniqid();
        $password = 'mypassword123';
        $profile = Profile::create(['umur' => 25, 'bio' => 'bio', 'alamat' => 'alamat']);

        $user = User::create([
            'name' => $name,
            'email' => $name . '@example.com',
            'password' => Hash::make($password),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login' => $name,
            'password' => $password,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Memastikan login admin mengarahkan ke dashboard admin.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $email = 'admin_' . uniqid() . '@example.com';
        $password = 'admin12345';
        $profile = Profile::create(['umur' => 30, 'bio' => 'Admin Bio', 'alamat' => 'Kantor']);

        $admin = User::create([
            'name' => 'Admin Test ' . uniqid(),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login' => $email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertEquals('admin', session('role'));
    }

    /**
     * Memastikan user yang sudah login tidak dapat mengakses halaman login atau register.
     */
    public function test_authenticated_user_cannot_access_login_or_register_pages(): void
    {
        $profile = Profile::create(['umur' => 20, 'bio' => '-', 'alamat' => '-']);
        $user = User::create([
            'name' => 'Logged User ' . uniqid(),
            'email' => 'logged_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $responseLogin = $this->actingAs($user)->get('/login');
        $responseLogin->assertRedirect(route('home'));

        $responseRegister = $this->actingAs($user)->get('/register');
        $responseRegister->assertRedirect(route('home'));
    }

    /**
     * Memastikan tamu (guest) dan user biasa tidak dapat mengakses dashboard admin.
     */
    public function test_admin_dashboard_protection(): void
    {
        // 1. Tamu tidak bisa akses -> redirect ke login
        $responseGuest = $this->get('/admin/dashboard');
        $responseGuest->assertRedirect('/login');

        // 2. User biasa tidak bisa akses -> redirect ke home
        $profile = Profile::create(['umur' => 20, 'bio' => '-', 'alamat' => '-']);
        $regularUser = User::create([
            'name' => 'Reg User ' . uniqid(),
            'email' => 'reg_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $responseUser = $this->actingAs($regularUser)->get('/admin/dashboard');
        $responseUser->assertRedirect(route('home'));

        // 3. Admin bisa akses -> status 200
        $adminUser = User::create([
            'name' => 'Admin Role ' . uniqid(),
            'email' => 'adm_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $profile->id,
        ]);

        $responseAdmin = $this->actingAs($adminUser)->get('/admin/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Dashboard Administrator');
    }

    /**
     * Memastikan fitur logout menghapus session dan mengarahkan ke login.
     */
    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $profile = Profile::create(['umur' => 20, 'bio' => '-', 'alamat' => '-']);
        $user = User::create([
            'name' => 'Logout User ' . uniqid(),
            'email' => 'logout_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $this->actingAs($user);
        session(['user_id' => $user->id, 'name' => $user->name, 'role' => 'user']);

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $response->assertSessionHas('success');
        $this->assertFalse(Auth::check());
        $this->assertFalse(session()->has('user_id'));
    }

    /**
     * Memastikan navbar menampilkan Login dan Register jika belum login.
     */
    public function test_navbar_displays_login_and_register_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Login');
        $response->assertSee('Register');
    }

    /**
     * Memastikan navbar menampilkan nama user dan tombol Logout jika sudah login.
     */
    public function test_navbar_displays_user_name_and_logout_when_authenticated(): void
    {
        $userName = 'Anto_' . uniqid();
        $profile = Profile::create(['umur' => 20, 'bio' => '-', 'alamat' => '-']);
        $user = User::create([
            'name' => $userName,
            'email' => $userName . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee($userName);
        $response->assertSee('Logout');
    }
}


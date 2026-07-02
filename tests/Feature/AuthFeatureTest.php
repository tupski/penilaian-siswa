<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman login mengembalikan 200 untuk tamu.
     */
    public function test_login_page_returns_200(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test: Halaman register mengembalikan 200 untuk tamu.
     */
    public function test_register_page_returns_200(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * Test: Login berhasil dengan kredensial valid redirect ke /dashboard.
     */
    public function test_successful_login_redirects_to_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Login gagal dengan password salah redirect kembali dengan error.
     */
    public function test_failed_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test: Registrasi berhasil membuat user dengan role='guru' dan redirect ke /dashboard.
     */
    public function test_successful_register_creates_user_with_guru_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Guru',
            'email' => 'guru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'guru@example.com',
            'name' => 'Test Guru',
            'role' => 'guru',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Test: Register gagal dengan password tidak cocok mengembalikan error validasi.
     */
    public function test_failed_register_with_mismatched_passwords(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Guru',
            'email' => 'guru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test: Registrasi dengan email duplikat mengembalikan error validasi.
     */
    public function test_duplicate_email_register_returns_validation_error(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'guru',
        ]);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test: Logout redirect ke '/' dan menghapus session.
     */
    public function test_logout_redirects_and_invalidates_session(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test: User yang sudah login tidak bisa akses halaman login, redirect ke /dashboard.
     */
    public function test_authenticated_user_cannot_access_login(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test: Tamu tidak bisa akses dashboard, redirect ke /login.
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}

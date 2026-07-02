<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Middleware auth - akses rute yang dilindungi tanpa login redirect ke /login.
     */
    public function test_auth_middleware_redirects_guest_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test: Middleware admin sebagai tamu - akses rute admin redirect ke login (auth didahulukan).
     */
    public function test_admin_route_as_guest_redirects_to_login(): void
    {
        // Middleware admin mengecek Auth::check() dulu, tapi karena middleware 'auth'
        // membungkus rute admin di routes/web.php, middleware auth
        // akan redirect duluan. Jadi, untuk rute yang menggabungkan
        // middleware auth dan admin, pengecekan auth terjadi lebih dulu.
        $response = $this->get('/kriteria/create');

        // Middleware auth redirect sebelum middleware admin sempat dijalankan
        $response->assertRedirect('/login');
    }

    /**
     * Test: Middleware admin sebagai non-admin (guru) mengembalikan 403.
     */
    public function test_admin_route_as_non_admin_returns_403(): void
    {
        /** @var User $guru */
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get('/kriteria/create');

        $response->assertStatus(403);
    }

    /**
     * Test: Middleware admin sebagai admin mengembalikan 200.
     */
    public function test_admin_route_as_admin_returns_200(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/kriteria/create');

        $response->assertStatus(200);
    }

    /**
     * Test: Operasi CRUD admin bekerja untuk user admin.
     */
    public function test_admin_crud_operations_as_admin(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        // Simpan kriteria
        $storeResponse = $this->actingAs($admin)->post('/kriteria', [
            'selected_kriteria' => 'custom',
            'custom_nama' => 'Test',
            'kode_kriteria' => 'T1',
            'bobot' => 10,
            'jenis' => 'benefit',
        ]);

        $storeResponse->assertRedirect('/kriteria');
    }

    /**
     * Test: Operasi CRUD admin diblokir untuk non-admin (guru).
     */
    public function test_admin_crud_blocked_for_non_admin(): void
    {
        /** @var User $guru */
        $guru = User::factory()->create(['role' => 'guru']);

        $storeResponse = $this->actingAs($guru)->post('/kriteria', [
            'selected_kriteria' => 'custom',
            'custom_nama' => 'Test',
            'kode_kriteria' => 'T1',
            'bobot' => 10,
            'jenis' => 'benefit',
        ]);

        $storeResponse->assertStatus(403);
    }
}

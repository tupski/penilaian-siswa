<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index mengembalikan 200 untuk user yang sudah login.
     */
    public function test_index_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/laporan');

        $response->assertStatus(200);
    }

    /**
     * Test: Generate PDF mengembalikan 200 dengan header PDF.
     */
    public function test_cetak_pdf_returns_200_with_pdf_headers(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/laporan/cetak-pdf');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Test: Tamu tidak bisa akses rute laporan.
     */
    public function test_guest_cannot_access_laporan(): void
    {
        $this->get('/laporan')->assertRedirect('/login');
        $this->get('/laporan/cetak-pdf')->assertRedirect('/login');
    }
}

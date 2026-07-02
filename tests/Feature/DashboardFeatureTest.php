<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman dashboard mengembalikan 200 dengan data view (jumlah) yang benar.
     */
    public function test_dashboard_returns_200_with_counts(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalSiswa');
        $response->assertViewHas('totalKriteria');
        $response->assertViewHas('totalPenilaian');
    }

    /**
     * Test: Data jumlah di view dashboard akurat.
     */
    public function test_dashboard_counts_are_accurate(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        Alternatif::factory()->count(5)->create();
        Kriteria::factory()->count(3)->create();

        $alternatif = Alternatif::first();
        $kriteria = Kriteria::first();
        Penilaian::factory()->count(4)->create([
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $this->assertEquals(5, $response->viewData('totalSiswa'));
        $this->assertEquals(3, $response->viewData('totalKriteria'));
        $this->assertEquals(4, $response->viewData('totalPenilaian'));
    }

    /**
     * Test: Tamu tidak bisa akses dashboard.
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RangkingFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index mengembalikan 200 untuk user yang sudah login.
     */
    public function test_index_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/rangking');

        $response->assertStatus(200);
    }

    /**
     * Test: Perhitungan peringkat menghitung dan menampilkan ranking dengan data.
     */
    public function test_ranking_calculation_computes_and_displays(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $alternatif = Alternatif::factory()->create([
            'nama_siswa' => 'Budi',
        ]);
        $kriteria = Kriteria::factory()->create([
            'nama_kriteria' => 'Akademik',
            'bobot' => 40,
            'jenis' => 'benefit',
        ]);

        Penilaian::factory()->create([
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
            'nilai' => 85,
        ]);

        $response = $this->actingAs($user)->get('/rangking');

        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertViewHas('kriterias');
    }

    /**
     * Test: Halaman ranking dengan data kosong tetap mengembalikan 200.
     */
    public function test_empty_data_page_still_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/rangking');

        $response->assertStatus(200);
    }

    /**
     * Test: Tamu tidak bisa akses halaman ranking.
     */
    public function test_guest_cannot_access_rangking(): void
    {
        $this->get('/rangking')->assertRedirect('/login');
    }
}

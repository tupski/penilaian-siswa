<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenilaianFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index mengembalikan 200 untuk user yang sudah login.
     */
    public function test_index_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/penilaian');

        $response->assertStatus(200);
    }

    /**
     * Test: Halaman create mengembalikan 200 untuk user yang sudah login.
     */
    public function test_create_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/penilaian/create');

        $response->assertStatus(200);
    }

    /**
     * Test: Store membuat data penilaian untuk alternatif yang dipilih.
     */
    public function test_store_creates_penilaian_records(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();
        $kriteria = Kriteria::factory()->create([
            'nama_kriteria' => 'Akademik',
        ]);

        $response = $this->actingAs($user)->post('/penilaian', [
            'alternatif_id' => $alternatif->id,
            'nilai' => [
                $kriteria->id => 85,
            ],
        ]);

        $response->assertRedirect('/penilaian');
        $this->assertDatabaseHas('penilaians', [
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
            'nilai' => 85,
        ]);
    }

    /**
     * Test: Store melewati kriteria Kehadiran - nilai Kehadiran tidak disimpan.
     */
    public function test_store_skips_kehadiran_kriteria(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();
        $kehadiran = Kriteria::factory()->kehadiran()->create();
        $akademik = Kriteria::factory()->create([
            'nama_kriteria' => 'Akademik',
        ]);

        $response = $this->actingAs($user)->post('/penilaian', [
            'alternatif_id' => $alternatif->id,
            'nilai' => [
                $kehadiran->id => 90,
                $akademik->id => 85,
            ],
        ]);

        $response->assertRedirect('/penilaian');

        // Kehadiran harus dilewati (tidak disimpan)
        $this->assertDatabaseMissing('penilaians', [
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kehadiran->id,
        ]);

        // Tapi Akademik harus tetap disimpan
        $this->assertDatabaseHas('penilaians', [
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $akademik->id,
            'nilai' => 85,
        ]);
    }

    /**
     * Test: Halaman edit mengembalikan 200 untuk user yang sudah login.
     */
    public function test_edit_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();

        $response = $this->actingAs($user)->get("/penilaian/{$alternatif->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test: Update nilai penilaian untuk suatu alternatif.
     */
    public function test_update_penilaian_values(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();
        $kriteria = Kriteria::factory()->create([
            'nama_kriteria' => 'Akademik',
        ]);

        // Buat penilaian dulu
        Penilaian::factory()->create([
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
            'nilai' => 70,
        ]);

        // Update
        $response = $this->actingAs($user)->put("/penilaian/{$alternatif->id}", [
            'nilai' => [
                $kriteria->id => 95,
            ],
        ]);

        $response->assertRedirect('/penilaian');
        $this->assertDatabaseHas('penilaians', [
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
            'nilai' => 95,
        ]);
    }

    /**
     * Test: Delete penilaian berdasarkan alternatif_id dan kriteria_id.
     */
    public function test_delete_removes_penilaian(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();
        $kriteria = Kriteria::factory()->create([
            'nama_kriteria' => 'Akademik',
        ]);

        Penilaian::factory()->create([
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
            'nilai' => 80,
        ]);

        $response = $this->actingAs($user)->delete("/penilaian/{$alternatif->id}/{$kriteria->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('penilaians', [
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
        ]);
    }

    /**
     * Test: Export PDF mengembalikan 200 dengan content disposition PDF.
     */
    public function test_export_pdf_returns_200_with_pdf_headers(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/penilaian/export-pdf');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Test: Export CSV mengembalikan 200 dengan header CSV yang benar.
     */
    public function test_export_csv_returns_200_with_csv_headers(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/penilaian/export-csv');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    /**
     * Test: Tamu tidak bisa akses rute penilaian.
     */
    public function test_guest_cannot_access_penilaian_routes(): void
    {
        $alternatif = Alternatif::factory()->create();
        $kriteria = Kriteria::factory()->create();

        $this->get('/penilaian')->assertRedirect('/login');
        $this->get('/penilaian/create')->assertRedirect('/login');
        $this->post('/penilaian', [])->assertRedirect('/login');
        $this->get("/penilaian/{$alternatif->id}/edit")->assertRedirect('/login');
        $this->put("/penilaian/{$alternatif->id}", [])->assertRedirect('/login');
        $this->delete("/penilaian/{$alternatif->id}/{$kriteria->id}")->assertRedirect('/login');
        $this->get('/penilaian/export-pdf')->assertRedirect('/login');
        $this->get('/penilaian/export-csv')->assertRedirect('/login');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlternatifFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index mengembalikan 200 untuk user yang sudah login.
     */
    public function test_index_page_returns_200_for_authenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/alternatif');

        $response->assertStatus(200);
    }

    /**
     * Test: Halaman create mengembalikan 200 untuk user yang sudah login.
     */
    public function test_create_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/alternatif/create');

        $response->assertStatus(200);
    }

    /**
     * Test: Store membuat data Alternatif baru dan redirect.
     */
    public function test_store_creates_record_and_redirects(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->post('/alternatif', [
            'nis' => 'NIS12345',
            'nama_siswa' => 'Budi Santoso',
            'kelas' => 'VII-A',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', [
            'nis' => 'NIS12345',
            'nama_siswa' => 'Budi Santoso',
            'kelas' => 'VII-A',
            'jenis_kelamin' => 'L',
        ]);
    }

    /**
     * Test: Store dengan NIS duplikat mengembalikan error validasi.
     */
    public function test_store_duplicate_nis_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        Alternatif::factory()->create(['nis' => 'NIS12345']);

        $response = $this->actingAs($user)->post('/alternatif', [
            'nis' => 'NIS12345',
            'nama_siswa' => 'Another Student',
            'kelas' => 'VII-B',
            'jenis_kelamin' => 'P',
        ]);

        $response->assertSessionHasErrors(['nis']);
    }

    /**
     * Test: Store dengan field kosong mengembalikan error validasi.
     */
    public function test_store_empty_fields_validation_errors(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->post('/alternatif', []);

        $response->assertSessionHasErrors(['nis', 'nama_siswa', 'kelas', 'jenis_kelamin']);
    }

    /**
     * Test: Halaman edit mengembalikan 200 untuk user yang sudah login.
     */
    public function test_edit_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();

        $response = $this->actingAs($user)->get("/alternatif/{$alternatif->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test: Update dengan data valid memperbarui data Alternatif.
     */
    public function test_update_updates_alternatif_record(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create([
            'nis' => 'NISOLD01',
            'nama_siswa' => 'Old Name',
            'kelas' => 'VII-A',
            'jenis_kelamin' => 'L',
        ]);

        $response = $this->actingAs($user)->put("/alternatif/{$alternatif->id}", [
            'nis' => 'NISNEW01',
            'nama_siswa' => 'New Name',
            'kelas' => 'VIII-B',
            'jenis_kelamin' => 'P',
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', [
            'id' => $alternatif->id,
            'nis' => 'NISNEW01',
            'nama_siswa' => 'New Name',
            'kelas' => 'VIII-B',
            'jenis_kelamin' => 'P',
        ]);
    }

    /**
     * Test: Update dengan NIS yang sama (abaikan diri sendiri) berhasil.
     */
    public function test_update_with_same_nis_succeeds_ignoring_self(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create([
            'nis' => 'NIS12345',
            'nama_siswa' => 'Student',
        ]);

        $response = $this->actingAs($user)->put("/alternatif/{$alternatif->id}", [
            'nis' => 'NIS12345',
            'nama_siswa' => 'Student Updated',
            'kelas' => 'IX-A',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', [
            'id' => $alternatif->id,
            'nis' => 'NIS12345',
            'nama_siswa' => 'Student Updated',
        ]);
    }

    /**
     * Test: Delete menghapus data Alternatif dan redirect.
     */
    public function test_delete_removes_alternatif_and_redirects(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();

        $response = $this->actingAs($user)->delete("/alternatif/{$alternatif->id}");

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseMissing('alternatifs', ['id' => $alternatif->id]);
    }

    /**
     * Test: Delete juga menghapus data penilaian dan absensi terkait (cascade).
     */
    public function test_delete_removes_associated_records(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);
        $alternatif = Alternatif::factory()->create();
        $kriteria = Kriteria::factory()->create();

        Penilaian::factory()->create([
            'alternatif_id' => $alternatif->id,
            'kriteria_id' => $kriteria->id,
        ]);

        $response = $this->actingAs($user)->delete("/alternatif/{$alternatif->id}");

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseMissing('alternatifs', ['id' => $alternatif->id]);
        $this->assertDatabaseMissing('penilaians', [
            'alternatif_id' => $alternatif->id,
        ]);
    }

    /**
     * Test: Tamu tidak bisa mengakses rute alternatif (perlu login).
     */
    public function test_guest_cannot_access_alternatif_routes(): void
    {
        $alternatif = Alternatif::factory()->create();

        $this->get('/alternatif')->assertRedirect('/login');
        $this->get('/alternatif/create')->assertRedirect('/login');
        $this->post('/alternatif', [])->assertRedirect('/login');
        $this->get("/alternatif/{$alternatif->id}/edit")->assertRedirect('/login');
        $this->put("/alternatif/{$alternatif->id}", [])->assertRedirect('/login');
        $this->delete("/alternatif/{$alternatif->id}")->assertRedirect('/login');
    }
}

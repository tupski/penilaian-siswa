<?php

namespace Tests\Feature;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriteriaFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index (sub page) mengembalikan 200 untuk user yang sudah login.
     * Rute GET /kriteria mengarah ke KriteriaController@index
     */
    public function test_index_page_returns_200_for_auth_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/kriteria');

        $response->assertStatus(200);
    }

    /**
     * Test: Halaman create mengembalikan 200 untuk user admin.
     */
    public function test_create_page_returns_200_for_admin(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/kriteria/create');

        $response->assertStatus(200);
    }

    /**
     * Test: Halaman create mengembalikan 403 untuk user non-admin (guru).
     */
    public function test_create_page_returns_403_for_non_admin(): void
    {
        /** @var User $guru */
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get('/kriteria/create');

        $response->assertStatus(403);
    }

    /**
     * Test: Store membuat Kriteria baru dengan data valid (khusus admin).
     */
    public function test_store_creates_kriteria_for_admin(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/kriteria', [
            'selected_kriteria' => 'custom',
            'custom_nama' => 'Test Kriteria',
            'kode_kriteria' => 'TX',
            'bobot' => 15,
            'jenis' => 'benefit',
        ]);

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseHas('kriterias', [
            'kode_kriteria' => 'TX',
            'nama_kriteria' => 'Test Kriteria',
            'bobot' => 15,
            'jenis' => 'benefit',
        ]);
    }

    /**
     * Test: Store dengan bobot > 100 mengembalikan error validasi.
     */
    public function test_store_with_bobot_over_100_validation_error(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/kriteria', [
            'selected_kriteria' => 'custom',
            'custom_nama' => 'Test',
            'kode_kriteria' => 'TX',
            'bobot' => 150,
            'jenis' => 'benefit',
        ]);

        $response->assertSessionHasErrors(['bobot']);
    }

    /**
     * Test: Halaman edit mengembalikan 200 untuk user admin.
     */
    public function test_edit_page_returns_200_for_admin(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();

        $response = $this->actingAs($admin)->get("/kriteria/{$kriteria->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * Test: Update memperbarui data Kriteria (khusus admin).
     */
    public function test_update_updates_kriteria_for_admin(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create([
            'kode_kriteria' => 'AA',
            'nama_kriteria' => 'Old',
            'bobot' => 10,
            'jenis' => 'benefit',
        ]);

        $response = $this->actingAs($admin)->put("/kriteria/{$kriteria->id}", [
            'kode_kriteria' => 'BB',
            'nama_kriteria' => 'Updated',
            'bobot' => 25,
            'jenis' => 'cost',
        ]);

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseHas('kriterias', [
            'id' => $kriteria->id,
            'kode_kriteria' => 'BB',
            'nama_kriteria' => 'Updated',
            'bobot' => 25,
            'jenis' => 'cost',
        ]);
    }

    /**
     * Test: Delete menghapus Kriteria dan cascade ke sub-kriteria dan penilaian (khusus admin).
     */
    public function test_delete_removes_kriteria_cascade_for_admin(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();

        $sub = SubKriteria::create([
            'kriteria_id' => $kriteria->id,
            'nama_sub' => 'Test Sub',
            'nilai' => 50,
        ]);

        $response = $this->actingAs($admin)->delete("/kriteria/{$kriteria->id}");

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseMissing('kriterias', ['id' => $kriteria->id]);
        $this->assertDatabaseMissing('sub_kriterias', ['id' => $sub->id]);
    }

    /**
     * Test: Sub-kriteria store membuat data sub-kriteria (khusus admin).
     */
    public function test_sub_kriteria_store_creates_record(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();

        $response = $this->actingAs($admin)->post("/kriteria/{$kriteria->id}/sub", [
            'nama_sub' => 'Sub Test',
            'nilai' => 75,
        ]);

        $response->assertRedirect("/kriteria/{$kriteria->id}/sub");
        $this->assertDatabaseHas('sub_kriterias', [
            'kriteria_id' => $kriteria->id,
            'nama_sub' => 'Sub Test',
            'nilai' => 75,
        ]);
    }

    /**
     * Test: Sub-kriteria store dengan nilai > 100 mengembalikan error validasi.
     */
    public function test_sub_kriteria_store_nilai_over_100_validation_error(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();

        $response = $this->actingAs($admin)->post("/kriteria/{$kriteria->id}/sub", [
            'nama_sub' => 'Sub Test',
            'nilai' => 150,
        ]);

        $response->assertSessionHasErrors(['nilai']);
    }

    /**
     * Test: Sub-kriteria store dengan nilai bukan integer mengembalikan error validasi.
     */
    public function test_sub_kriteria_store_non_integer_nilai_validation_error(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();

        $response = $this->actingAs($admin)->post("/kriteria/{$kriteria->id}/sub", [
            'nama_sub' => 'Sub Test',
            'nilai' => 'abc',
        ]);

        $response->assertSessionHasErrors(['nilai']);
    }

    /**
     * Test: Sub-kriteria store dengan nilai negatif mengembalikan error validasi.
     */
    public function test_sub_kriteria_store_negative_nilai_validation_error(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();

        $response = $this->actingAs($admin)->post("/kriteria/{$kriteria->id}/sub", [
            'nama_sub' => 'Sub Test',
            'nilai' => -5,
        ]);

        $response->assertSessionHasErrors(['nilai']);
    }

    /**
     * Test: Sub-kriteria delete menghapus data (khusus admin).
     */
    public function test_sub_kriteria_delete_removes_record(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $kriteria = Kriteria::factory()->create();
        $sub = SubKriteria::create([
            'kriteria_id' => $kriteria->id,
            'nama_sub' => 'Sub to Delete',
            'nilai' => 50,
        ]);

        $response = $this->actingAs($admin)->delete("/kriteria/sub-kriteria/{$sub->id}");

        $response->assertRedirect("/kriteria/{$kriteria->id}/sub");
        $this->assertDatabaseMissing('sub_kriterias', ['id' => $sub->id]);
    }
}

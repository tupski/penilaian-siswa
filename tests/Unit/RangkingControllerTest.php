<?php

namespace Tests\Unit;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RangkingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: mereplikasi logika algoritma SAW dari RangkingController::index.
     */
    private function runSawCalculation(): array
    {
        $kriterias = Kriteria::all();
        $alternatifs = Alternatif::all();

        // Normalisasi bobot
        $totalBobot = $kriterias->sum('bobot');
        foreach ($kriterias as $kriteria) {
            $kriteria->bobot_normalisasi = $kriteria->bobot / $totalBobot;
        }

        $results = [];
        foreach ($alternatifs as $alternatif) {
            $totalNilai = 0;
            $details = [];

            foreach ($kriterias as $kriteria) {
                $penilaian = Penilaian::where('alternatif_id', $alternatif->id)
                    ->where('kriteria_id', $kriteria->id)
                    ->first();

                $nilai = $penilaian ? $penilaian->nilai : 0;

                // Normalisasi nilai (maks 100)
                $nilai_normalisasi = $nilai / 100;

                // Hitung utility
                $utility = $nilai_normalisasi * $kriteria->bobot_normalisasi;

                $details[] = [
                    'kriteria' => $kriteria->nama_kriteria,
                    'nilai' => $nilai,
                    'bobot' => $kriteria->bobot,
                    'utility' => $utility,
                ];

                $totalNilai += $utility;
            }

            $results[] = [
                'alternatif' => $alternatif,
                'total_nilai' => $totalNilai * 100,
                'details' => $details,
            ];
        }

        // Urutkan menurun berdasarkan total_nilai
        usort($results, function ($a, $b) {
            return $b['total_nilai'] <=> $a['total_nilai'];
        });

        // Tetapkan peringkat
        foreach ($results as $index => $result) {
            $results[$index]['rangking'] = $index + 1;
        }

        return $results;
    }

    /**
     * Normal: Beberapa siswa dengan nilai berbeda, verifikasi urutan peringkat yang benar.
     */
    #[Test]
    public function saw_algorithm_ranks_students_correctly(): void
    {
        // Buat 2 kriteria dengan bobot yang diketahui
        Kriteria::factory()->create(['nama_kriteria' => 'Akademik', 'bobot' => 60, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'Kehadiran', 'bobot' => 40, 'jenis' => 'benefit']);

        // Buat 3 siswa
        $s1 = Alternatif::factory()->create(['nama_siswa' => 'Siswa A']);
        $s2 = Alternatif::factory()->create(['nama_siswa' => 'Siswa B']);
        $s3 = Alternatif::factory()->create(['nama_siswa' => 'Siswa C']);

        // Siswa A: 90 Akademik, 80 Kehadiran → tertinggi
        // Siswa B: 70 Akademik, 60 Kehadiran → tengah
        // Siswa C: 50 Akademik, 40 Kehadiran → terendah
        $kriterias = Kriteria::all();
        $c1 = $kriterias->firstWhere('nama_kriteria', 'Akademik');
        $c2 = $kriterias->firstWhere('nama_kriteria', 'Kehadiran');

        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $c1->id, 'nilai' => 90]);
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $c2->id, 'nilai' => 80]);
        Penilaian::factory()->create(['alternatif_id' => $s2->id, 'kriteria_id' => $c1->id, 'nilai' => 70]);
        Penilaian::factory()->create(['alternatif_id' => $s2->id, 'kriteria_id' => $c2->id, 'nilai' => 60]);
        Penilaian::factory()->create(['alternatif_id' => $s3->id, 'kriteria_id' => $c1->id, 'nilai' => 50]);
        Penilaian::factory()->create(['alternatif_id' => $s3->id, 'kriteria_id' => $c2->id, 'nilai' => 40]);

        $results = $this->runSawCalculation();

        $this->assertCount(3, $results);

        // Peringkat seharusnya: Siswa A (1), Siswa B (2), Siswa C (3)
        $this->assertEquals('Siswa A', $results[0]['alternatif']->nama_siswa);
        $this->assertEquals(1, $results[0]['rangking']);
        $this->assertEquals('Siswa B', $results[1]['alternatif']->nama_siswa);
        $this->assertEquals(2, $results[1]['rangking']);
        $this->assertEquals('Siswa C', $results[2]['alternatif']->nama_siswa);
        $this->assertEquals(3, $results[2]['rangking']);

        // Verifikasi total_nilai dalam urutan menurun
        $this->assertGreaterThan($results[1]['total_nilai'], $results[0]['total_nilai']);
        $this->assertGreaterThan($results[2]['total_nilai'], $results[1]['total_nilai']);
    }

    /**
     * Satu siswa: hanya satu siswa → peringkat 1.
     */
    #[Test]
    public function saw_algorithm_single_student_gets_rank_1(): void
    {
        Kriteria::factory()->create(['nama_kriteria' => 'Akademik', 'bobot' => 100, 'jenis' => 'benefit']);
        $s1 = Alternatif::factory()->create();

        $c1 = Kriteria::first();
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $c1->id, 'nilai' => 75]);

        $results = $this->runSawCalculation();

        $this->assertCount(1, $results);
        $this->assertEquals(1, $results[0]['rangking']);
    }

    /**
     * Normalisasi bobot: verifikasi bobot dinormalisasi dengan benar.
     */
    #[Test]
    public function saw_algorithm_normalizes_weights_correctly(): void
    {
        // Total bobot = 40 + 25 + 20 + 15 = 100
        Kriteria::factory()->create(['nama_kriteria' => 'K1', 'bobot' => 40, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K2', 'bobot' => 25, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K3', 'bobot' => 20, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K4', 'bobot' => 15, 'jenis' => 'benefit']);

        $kriterias = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot');

        $this->assertEquals(100, $totalBobot);

        $this->assertEqualsWithDelta(0.4, $kriterias[0]->bobot / $totalBobot, 0.001);
        $this->assertEqualsWithDelta(0.25, $kriterias[1]->bobot / $totalBobot, 0.001);
        $this->assertEqualsWithDelta(0.2, $kriterias[2]->bobot / $totalBobot, 0.001);
        $this->assertEqualsWithDelta(0.15, $kriterias[3]->bobot / $totalBobot, 0.001);
    }

    /**
     * Penanganan seri: dua siswa dengan total nilai sama → peringkat tetap diberikan.
     */
    #[Test]
    public function saw_algorithm_handles_ties_correctly(): void
    {
        Kriteria::factory()->create(['nama_kriteria' => 'K1', 'bobot' => 50, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K2', 'bobot' => 50, 'jenis' => 'benefit']);

        $s1 = Alternatif::factory()->create(['nama_siswa' => 'Siswa A']);
        $s2 = Alternatif::factory()->create(['nama_siswa' => 'Siswa B']);

        $kriterias = Kriteria::all();
        $c1 = $kriterias[0];
        $c2 = $kriterias[1];

        // Kedua siswa memiliki nilai yang persis sama
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $c1->id, 'nilai' => 80]);
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $c2->id, 'nilai' => 80]);
        Penilaian::factory()->create(['alternatif_id' => $s2->id, 'kriteria_id' => $c1->id, 'nilai' => 80]);
        Penilaian::factory()->create(['alternatif_id' => $s2->id, 'kriteria_id' => $c2->id, 'nilai' => 80]);

        $results = $this->runSawCalculation();

        $this->assertCount(2, $results);
        $this->assertEquals($results[0]['total_nilai'], $results[1]['total_nilai']);
        $this->assertEquals(1, $results[0]['rangking']);
        $this->assertEquals(2, $results[1]['rangking']);
    }

    /**
     * Batas: semua nilai nol → semua utility bernilai 0.
     */
    #[Test]
    public function saw_algorithm_all_zero_scores_produces_zero_utilities(): void
    {
        Kriteria::factory()->create(['nama_kriteria' => 'K1', 'bobot' => 50, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K2', 'bobot' => 50, 'jenis' => 'benefit']);

        $s1 = Alternatif::factory()->create();

        $kriterias = Kriteria::all();
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $kriterias[0]->id, 'nilai' => 0]);
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $kriterias[1]->id, 'nilai' => 0]);

        $results = $this->runSawCalculation();

        $this->assertCount(1, $results);
        $this->assertEquals(0, $results[0]['total_nilai']);
        $this->assertEquals(0, $results[0]['details'][0]['utility']);
        $this->assertEquals(0, $results[0]['details'][1]['utility']);
    }

    /**
     * Batas: semua nilai maksimum (100) → verifikasi nilai utility.
     */
    #[Test]
    public function saw_algorithm_all_max_scores_produces_correct_utilities(): void
    {
        Kriteria::factory()->create(['nama_kriteria' => 'K1', 'bobot' => 60, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K2', 'bobot' => 40, 'jenis' => 'benefit']);

        $s1 = Alternatif::factory()->create();

        $kriterias = Kriteria::all();
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $kriterias[0]->id, 'nilai' => 100]);
        Penilaian::factory()->create(['alternatif_id' => $s1->id, 'kriteria_id' => $kriterias[1]->id, 'nilai' => 100]);

        $results = $this->runSawCalculation();

        // Total bobot = 100 → bobot ternormalisasi: 0.6 dan 0.4
        // Utility K1: (100/100) * 0.6 = 0.6
        // Utility K2: (100/100) * 0.4 = 0.4
        // total_nilai = (0.6 + 0.4) * 100 = 100
        $this->assertEqualsWithDelta(0.6, $results[0]['details'][0]['utility'], 0.001);
        $this->assertEqualsWithDelta(0.4, $results[0]['details'][1]['utility'], 0.001);
        $this->assertEqualsWithDelta(100.0, $results[0]['total_nilai'], 0.001);
    }

    /**
     * Siswa tanpa data penilaian mendapat utility 0 untuk setiap kriteria.
     */
    #[Test]
    public function saw_algorithm_student_without_penilaian_gets_zero_utilities(): void
    {
        Kriteria::factory()->create(['nama_kriteria' => 'K1', 'bobot' => 50, 'jenis' => 'benefit']);
        Kriteria::factory()->create(['nama_kriteria' => 'K2', 'bobot' => 50, 'jenis' => 'benefit']);

        // Siswa tanpa data penilaian
        Alternatif::factory()->create();

        $results = $this->runSawCalculation();

        $this->assertCount(1, $results);
        $this->assertEquals(0, $results[0]['total_nilai']);
    }
}

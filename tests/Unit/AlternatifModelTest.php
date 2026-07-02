<?php

namespace Tests\Unit;

use App\Models\Alternatif;
use App\Models\Absensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlternatifModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Kondisi normal: 5 hadir dari 10 total → mengembalikan 50.
     */
    #[Test]
    public function nilai_kehadiran_returns_50_when_5_hadir_out_of_10_total(): void
    {
        $alternatif = Alternatif::factory()->create();

        // Buat 5 data absensi "hadir" dan 5 "alpa"
        Absensi::factory()->count(5)->hadir()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(5)->alpa()->create(['alternatif_id' => $alternatif->id]);

        $this->assertEquals(50, $alternatif->nilai_kehadiran);
    }

    /**
     * Kehadiran sempurna: 10 hadir dari 10 total → mengembalikan 100.
     */
    #[Test]
    public function nilai_kehadiran_returns_100_when_10_hadir_out_of_10_total(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->count(10)->hadir()->create(['alternatif_id' => $alternatif->id]);

        $this->assertEquals(100, $alternatif->nilai_kehadiran);
    }

    /**
     * Nol kehadiran: tidak ada data absensi → mengembalikan 0.
     */
    #[Test]
    public function nilai_kehadiran_returns_0_when_no_absensi_records(): void
    {
        $alternatif = Alternatif::factory()->create();

        $this->assertEquals(0, $alternatif->nilai_kehadiran);
    }

    /**
     * Semua alpa: 0 hadir dari 10 total → mengembalikan 0.
     */
    #[Test]
    public function nilai_kehadiran_returns_0_when_all_alpa(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->count(10)->alpa()->create(['alternatif_id' => $alternatif->id]);

        $this->assertEquals(0, $alternatif->nilai_kehadiran);
    }

    /**
     * Batas: 1 hadir dari 1 total → mengembalikan 100.
     */
    #[Test]
    public function nilai_kehadiran_returns_100_when_1_hadir_out_of_1_total(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->hadir()->create(['alternatif_id' => $alternatif->id]);

        $this->assertEquals(100, $alternatif->nilai_kehadiran);
    }

    /**
     * Pemotongan: persentase di atas 100 harus dipotong ke 100 lewat min().
     */
    #[Test]
    public function nilai_kehadiran_is_capped_at_100(): void
    {
        $alternatif = Alternatif::factory()->create();

        // Buat 11 "hadir" tanpa status lain → 110% mentah, harus dipotong ke 100
        Absensi::factory()->count(11)->hadir()->create(['alternatif_id' => $alternatif->id]);

        $this->assertEquals(100, $alternatif->nilai_kehadiran);
    }

    /**
     * detail_kehadiran mengembalikan struktur array yang benar dengan semua key dan value yang diharapkan.
     */
    #[Test]
    public function detail_kehadiran_returns_correct_array_structure(): void
    {
        $alternatif = Alternatif::factory()->create();

        // Buat campuran status: 5 hadir, 2 sakit, 2 izin, 1 alpa = 10 total
        Absensi::factory()->count(5)->hadir()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(2)->sakit()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(2)->izin()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(1)->alpa()->create(['alternatif_id' => $alternatif->id]);

        $detail = $alternatif->detail_kehadiran;

        $this->assertIsArray($detail);
        $this->assertArrayHasKey('hadir', $detail);
        $this->assertArrayHasKey('sakit', $detail);
        $this->assertArrayHasKey('izin', $detail);
        $this->assertArrayHasKey('alpa', $detail);
        $this->assertArrayHasKey('total_absensi', $detail);
        $this->assertArrayHasKey('persentase', $detail);
        $this->assertEquals(5, $detail['hadir']);
        $this->assertEquals(2, $detail['sakit']);
        $this->assertEquals(2, $detail['izin']);
        $this->assertEquals(1, $detail['alpa']);
        $this->assertEquals(10, $detail['total_absensi']);
        $this->assertEquals(50, $detail['persentase']);
    }

    /**
     * detail_kehadiran tanpa data absensi mengembalikan nol semua.
     */
    #[Test]
    public function detail_kehadiran_returns_zeros_when_no_absensi(): void
    {
        $alternatif = Alternatif::factory()->create();

        $detail = $alternatif->detail_kehadiran;

        $this->assertEquals(0, $detail['hadir']);
        $this->assertEquals(0, $detail['sakit']);
        $this->assertEquals(0, $detail['izin']);
        $this->assertEquals(0, $detail['alpa']);
        $this->assertEquals(0, $detail['total_absensi']);
        $this->assertEquals(0, $detail['persentase']);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Absensi;
use App\Models\Alternatif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AbsensiModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * hitungPersentaseKehadiran: Normal - 8 hadir dari 10 → mengembalikan 80.
     */
    #[Test]
    public function hitung_persentase_kehadiran_returns_80_when_8_hadir_out_of_10(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->count(8)->hadir()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(2)->alpa()->create(['alternatif_id' => $alternatif->id]);

        $result = Absensi::hitungPersentaseKehadiran($alternatif->id);

        $this->assertEquals(80, $result);
    }

    /**
     * hitungPersentaseKehadiran: Sempurna - 10/10 → 100.
     */
    #[Test]
    public function hitung_persentase_kehadiran_returns_100_when_perfect_attendance(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->count(10)->hadir()->create(['alternatif_id' => $alternatif->id]);

        $result = Absensi::hitungPersentaseKehadiran($alternatif->id);

        $this->assertEquals(100, $result);
    }

    /**
     * hitungPersentaseKehadiran: Nol - 0/10 → 0.
     */
    #[Test]
    public function hitung_persentase_kehadiran_returns_0_with_no_hadir(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->count(10)->alpa()->create(['alternatif_id' => $alternatif->id]);

        $result = Absensi::hitungPersentaseKehadiran($alternatif->id);

        $this->assertEquals(0, $result);
    }

    /**
     * hitungPersentaseKehadiran: Tidak ada data absensi → mengembalikan 0.
     */
    #[Test]
    public function hitung_persentase_kehadiran_returns_0_when_no_records(): void
    {
        $alternatif = Alternatif::factory()->create();

        $result = Absensi::hitungPersentaseKehadiran($alternatif->id);

        $this->assertEquals(0, $result);
    }

    /**
     * Memverifikasi bahwa hitungPersentaseKehadiran cocok dengan hasil accessor nilai_kehadiran.
     */
    #[Test]
    public function hitung_persentase_kehadiran_matches_nilai_kehadiran_accessor(): void
    {
        $alternatif = Alternatif::factory()->create();

        Absensi::factory()->count(7)->hadir()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(3)->sakit()->create(['alternatif_id' => $alternatif->id]);

        $staticResult = Absensi::hitungPersentaseKehadiran($alternatif->id);
        $accessorResult = $alternatif->nilai_kehadiran;

        $this->assertEquals($accessorResult, $staticResult);
    }

    /**
     * getRekap mengembalikan array yang benar dengan key yang diharapkan untuk data kehadiran campuran.
     */
    #[Test]
    public function get_rekap_returns_correct_array_with_all_keys(): void
    {
        $alternatif = Alternatif::factory()->create();

        // Buat: 6 hadir, 1 sakit, 2 izin, 1 alpa = 10 total
        Absensi::factory()->count(6)->hadir()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->sakit()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->count(2)->izin()->create(['alternatif_id' => $alternatif->id]);
        Absensi::factory()->alpa()->create(['alternatif_id' => $alternatif->id]);

        $rekap = Absensi::getRekap($alternatif->id);

        $this->assertIsArray($rekap);
        $this->assertArrayHasKey('hadir', $rekap);
        $this->assertArrayHasKey('sakit', $rekap);
        $this->assertArrayHasKey('izin', $rekap);
        $this->assertArrayHasKey('alpa', $rekap);
        $this->assertArrayHasKey('total_absensi', $rekap);
        $this->assertArrayHasKey('persentase', $rekap);
        $this->assertEquals(6, $rekap['hadir']);
        $this->assertEquals(1, $rekap['sakit']);
        $this->assertEquals(2, $rekap['izin']);
        $this->assertEquals(1, $rekap['alpa']);
        $this->assertEquals(10, $rekap['total_absensi']);
        $this->assertEquals(60, $rekap['persentase']);
    }

    /**
     * getRekap tanpa data mengembalikan nol untuk semua penghitung.
     */
    #[Test]
    public function get_rekap_returns_zeros_when_no_records(): void
    {
        $alternatif = Alternatif::factory()->create();

        $rekap = Absensi::getRekap($alternatif->id);

        $this->assertEquals(0, $rekap['hadir']);
        $this->assertEquals(0, $rekap['sakit']);
        $this->assertEquals(0, $rekap['izin']);
        $this->assertEquals(0, $rekap['alpa']);
        $this->assertEquals(0, $rekap['total_absensi']);
        $this->assertEquals(0, $rekap['persentase']);
    }
}

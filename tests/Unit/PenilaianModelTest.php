<?php

namespace Tests\Unit;

use App\Models\Alternatif;
use App\Models\Absensi;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PenilaianModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * syncKehadiranForAllSiswa membuat data penilaian untuk kriteria "Kehadiran" pada setiap siswa.
     */
    #[Test]
    public function sync_kehadiran_creates_records_for_all_students(): void
    {
        // Buat kriteria "Kehadiran"
        $kriteriaKehadiran = Kriteria::factory()->kehadiran()->create();

        // Buat 3 siswa, masing-masing dengan data kehadiran berbeda
        $siswa1 = Alternatif::factory()->create();
        $siswa2 = Alternatif::factory()->create();
        $siswa3 = Alternatif::factory()->create();

        // Siswa1: 8 hadir dari 10 = 80%
        Absensi::factory()->count(8)->hadir()->create(['alternatif_id' => $siswa1->id]);
        Absensi::factory()->count(2)->alpa()->create(['alternatif_id' => $siswa1->id]);

        // Siswa2: 5 hadir dari 10 = 50%
        Absensi::factory()->count(5)->hadir()->create(['alternatif_id' => $siswa2->id]);
        Absensi::factory()->count(5)->sakit()->create(['alternatif_id' => $siswa2->id]);

        // Siswa3: tidak ada data absensi = 0%

        $count = Penilaian::syncKehadiranForAllSiswa();

        $this->assertEquals(3, $count);

        // Verifikasi penilaian kehadiran Siswa1
        $penilaian1 = Penilaian::where('alternatif_id', $siswa1->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->first();
        $this->assertNotNull($penilaian1);
        $this->assertEquals(80, $penilaian1->nilai);

        // Verifikasi penilaian kehadiran Siswa2
        $penilaian2 = Penilaian::where('alternatif_id', $siswa2->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->first();
        $this->assertNotNull($penilaian2);
        $this->assertEquals(50, $penilaian2->nilai);

        // Verifikasi penilaian kehadiran Siswa3
        $penilaian3 = Penilaian::where('alternatif_id', $siswa3->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->first();
        $this->assertNotNull($penilaian3);
        $this->assertEquals(0, $penilaian3->nilai);
    }

    /**
     * syncKehadiranForAllSiswa pakai updateOrCreate: dipanggil dua kali tidak boleh membuat duplikat.
     */
    #[Test]
    public function sync_kehadiran_does_not_create_duplicates_on_second_call(): void
    {
        $kriteriaKehadiran = Kriteria::factory()->kehadiran()->create();
        $siswa = Alternatif::factory()->create();

        // 5 hadir dari 10 = 50%
        Absensi::factory()->count(5)->hadir()->create(['alternatif_id' => $siswa->id]);
        Absensi::factory()->count(5)->sakit()->create(['alternatif_id' => $siswa->id]);

        // Sinkronisasi pertama
        Penilaian::syncKehadiranForAllSiswa();

        // Verifikasi jumlah data
        $countAfterFirst = Penilaian::where('alternatif_id', $siswa->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->count();
        $this->assertEquals(1, $countAfterFirst);

        // Sinkronisasi kedua harus update, bukan buat baru
        Penilaian::syncKehadiranForAllSiswa();

        $countAfterSecond = Penilaian::where('alternatif_id', $siswa->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->count();
        $this->assertEquals(1, $countAfterSecond);

        // Nilai harus tetap 50
        $penilaian = Penilaian::where('alternatif_id', $siswa->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->first();
        $this->assertEquals(50, $penilaian->nilai);
    }

    /**
     * syncKehadiranForAllSiswa memperbarui penilaian yang sudah ada saat data kehadiran berubah.
     */
    #[Test]
    public function sync_kehadiran_updates_value_when_attendance_changes(): void
    {
        $kriteriaKehadiran = Kriteria::factory()->kehadiran()->create();
        $siswa = Alternatif::factory()->create();

        // Awal: 5 hadir dari 10 = 50%
        Absensi::factory()->count(5)->hadir()->create(['alternatif_id' => $siswa->id]);
        Absensi::factory()->count(5)->sakit()->create(['alternatif_id' => $siswa->id]);

        Penilaian::syncKehadiranForAllSiswa();

        $penilaian = Penilaian::where('alternatif_id', $siswa->id)
            ->where('kriteria_id', $kriteriaKehadiran->id)
            ->first();
        $this->assertEquals(50, $penilaian->nilai);

        // Tambah 5 hadir lagi (sekarang 10 hadir dari 15)
        Absensi::factory()->count(5)->hadir()->create(['alternatif_id' => $siswa->id]);

        Penilaian::syncKehadiranForAllSiswa();

        $penilaian->refresh();
        // 10 hadir / 15 total = 66.67% → dibulatkan ke 67%
        $this->assertEquals(67, $penilaian->nilai);
    }

    /**
     * syncKehadiranForAllSiswa mengembalikan false saat kriteria "Kehadiran" tidak ada.
     */
    #[Test]
    public function sync_kehadiran_returns_false_when_no_kehadiran_criteria(): void
    {
        // Jangan buat kriteria apa pun
        Alternatif::factory()->create();

        $result = Penilaian::syncKehadiranForAllSiswa();

        $this->assertFalse($result);
    }

    /**
     * syncKehadiranForAllSiswa mengembalikan 0 saat tidak ada siswa.
     */
    #[Test]
    public function sync_kehadiran_returns_zero_when_no_students(): void
    {
        Kriteria::factory()->kehadiran()->create();

        $count = Penilaian::syncKehadiranForAllSiswa();

        $this->assertEquals(0, $count);
    }
}

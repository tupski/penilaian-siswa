<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $kriterias = [
            ['kode_kriteria' => 'C1', 'nama_kriteria' => 'Akademik', 'bobot' => 40, 'jenis' => 'benefit'],
            ['kode_kriteria' => 'C2', 'nama_kriteria' => 'Kehadiran', 'bobot' => 25, 'jenis' => 'benefit'],
            ['kode_kriteria' => 'C3', 'nama_kriteria' => 'Sikap', 'bobot' => 20, 'jenis' => 'benefit'],
            ['kode_kriteria' => 'C4', 'nama_kriteria' => 'Pengetahuan Agama', 'bobot' => 15, 'jenis' => 'benefit'],
        ];

        foreach ($kriterias as $kriteria) {
            Kriteria::create($kriteria);
        }
    }
}
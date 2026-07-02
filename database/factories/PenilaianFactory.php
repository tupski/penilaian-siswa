<?php

namespace Database\Factories;

use App\Models\Penilaian;
use App\Models\Alternatif;
use App\Models\Kriteria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penilaian>
 */
class PenilaianFactory extends Factory
{
    protected $model = Penilaian::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alternatif_id' => Alternatif::factory(),
            'kriteria_id' => Kriteria::factory(),
            'nilai' => fake()->numberBetween(0, 100),
        ];
    }
}

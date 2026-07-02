<?php

namespace Database\Factories;

use App\Models\Alternatif;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alternatif>
 */
class AlternatifFactory extends Factory
{
    protected $model = Alternatif::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('NIS#####'),
            'nama_siswa' => fake()->name(),
            'kelas' => fake()->randomElement(['VII-A', 'VII-B', 'VIII-A', 'VIII-B', 'IX-A', 'IX-B']),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
        ];
    }
}

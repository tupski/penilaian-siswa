<?php

namespace Database\Factories;

use App\Models\Kriteria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kriteria>
 */
class KriteriaFactory extends Factory
{
    protected $model = Kriteria::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_kriteria' => fake()->unique()->lexify('C??'),
            'nama_kriteria' => fake()->word(),
            'bobot' => fake()->randomFloat(2, 5, 40),
            'jenis' => fake()->randomElement(['benefit', 'cost']),
        ];
    }

    /**
     * Set the criteria as "Kehadiran" type.
     */
    public function kehadiran(): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kehadiran',
            'bobot' => 30,
            'jenis' => 'benefit',
        ]);
    }
}

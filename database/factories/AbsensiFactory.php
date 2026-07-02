<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\Alternatif;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    protected $model = Absensi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alternatif_id' => Alternatif::factory(),
            'tanggal' => fake()->date(),
            'status' => fake()->randomElement(['hadir', 'sakit', 'izin', 'alpa']),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Set the status to "hadir".
     */
    public function hadir(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'hadir',
        ]);
    }

    /**
     * Set the status to "sakit".
     */
    public function sakit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sakit',
        ]);
    }

    /**
     * Set the status to "izin".
     */
    public function izin(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'izin',
        ]);
    }

    /**
     * Set the status to "alpa".
     */
    public function alpa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'alpa',
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Discipline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discipline>
 */
class DisciplineFactory extends Factory
{
    protected $model = Discipline::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $disciplines = ['Football', 'Basketball', 'Handball', 'Volleyball', 'Athlétisme', 'Natation', 'Tennis', 'Judo', 'Karaté', 'Taekwondo'];
        
        return [
            'nom' => fake()->unique()->randomElement($disciplines),
            'description' => fake()->sentence(10),
            'tarif_mensuel' => fake()->randomElement([10000, 15000, 20000, 25000]),
            'actif' => true,
        ];
    }

    /**
     * Indicate that the discipline is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }

    /**
     * Set a specific tariff.
     */
    public function withTarif(int $tarif): static
    {
        return $this->state(fn (array $attributes) => [
            'tarif_mensuel' => $tarif,
        ]);
    }
}

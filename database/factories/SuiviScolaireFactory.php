<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\SuiviScolaire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuiviScolaire>
 */
class SuiviScolaireFactory extends Factory
{
    protected $model = SuiviScolaire::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $annee = fake()->numberBetween(2022, 2024);
        
        return [
            'athlete_id' => Athlete::factory(),
            'etablissement' => fake()->company() . ' School',
            'classe' => fake()->randomElement(['6ème', '5ème', '4ème', '3ème', '2nde', '1ère', 'Terminale']),
            'annee_scolaire' => $annee . '-' . ($annee + 1),
            'moyenne_generale' => fake()->randomFloat(2, 5, 18),
            'rang' => fake()->numberBetween(1, 40),
            'observations' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate satisfactory results (>= 10).
     */
    public function satisfaisant(): static
    {
        return $this->state(fn (array $attributes) => [
            'moyenne_generale' => fake()->randomFloat(2, 10, 14),
        ]);
    }

    /**
     * Indicate excellent results (>= 14).
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'moyenne_generale' => fake()->randomFloat(2, 14, 20),
            'rang' => fake()->numberBetween(1, 5),
        ]);
    }

    /**
     * Indicate insufficient results (< 10).
     */
    public function insuffisant(): static
    {
        return $this->state(fn (array $attributes) => [
            'moyenne_generale' => fake()->randomFloat(2, 0, 9.99),
        ]);
    }

    /**
     * For a specific athlete.
     */
    public function forAthlete(Athlete $athlete): static
    {
        return $this->state(fn (array $attributes) => [
            'athlete_id' => $athlete->id,
        ]);
    }
}

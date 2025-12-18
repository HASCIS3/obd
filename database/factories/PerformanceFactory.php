<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Performance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Performance>
 */
class PerformanceFactory extends Factory
{
    protected $model = Performance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'athlete_id' => Athlete::factory(),
            'discipline_id' => Discipline::factory(),
            'date_evaluation' => fake()->dateTimeBetween('-1 year', 'now'),
            'type_evaluation' => fake()->randomElement(['Test de vitesse', 'Endurance', 'Force', 'Technique', 'Match amical']),
            'score' => fake()->randomFloat(2, 10, 100),
            'unite' => fake()->randomElement(['secondes', 'mètres', 'points', 'kg']),
            'observations' => fake()->optional()->sentence(),
            'competition' => fake()->optional(0.3)->company(),
            'classement' => fake()->optional(0.3)->numberBetween(1, 20),
        ];
    }

    /**
     * Indicate that this is a competition performance.
     */
    public function enCompetition(): static
    {
        return $this->state(fn (array $attributes) => [
            'competition' => fake()->company() . ' Cup',
            'classement' => fake()->numberBetween(1, 20),
        ]);
    }

    /**
     * Indicate a podium finish.
     */
    public function podium(): static
    {
        return $this->state(fn (array $attributes) => [
            'competition' => fake()->company() . ' Cup',
            'classement' => fake()->numberBetween(1, 3),
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

    /**
     * For a specific discipline.
     */
    public function forDiscipline(Discipline $discipline): static
    {
        return $this->state(fn (array $attributes) => [
            'discipline_id' => $discipline->id,
        ]);
    }
}

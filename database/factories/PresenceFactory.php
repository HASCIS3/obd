<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Presence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presence>
 */
class PresenceFactory extends Factory
{
    protected $model = Presence::class;

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
            'coach_id' => null,
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'present' => true,
            'remarque' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Indicate that the athlete was absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'present' => false,
        ]);
    }

    /**
     * Indicate that the athlete was present.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'present' => true,
        ]);
    }

    /**
     * Set a specific date.
     */
    public function pourDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date,
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

    /**
     * Recorded by a specific coach.
     */
    public function byCoach(Coach $coach): static
    {
        return $this->state(fn (array $attributes) => [
            'coach_id' => $coach->id,
        ]);
    }
}

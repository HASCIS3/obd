<?php

namespace Database\Factories;

use App\Models\Coach;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coach>
 */
class CoachFactory extends Factory
{
    protected $model = Coach::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->coach(),
            'telephone' => fake()->phoneNumber(),
            'adresse' => fake()->address(),
            'specialite' => fake()->randomElement(['Football', 'Basketball', 'Athlétisme', 'Natation', 'Arts martiaux']),
            'date_embauche' => fake()->dateTimeBetween('-5 years', 'now'),
            'actif' => true,
        ];
    }

    /**
     * Indicate that the coach is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }

    /**
     * Use an existing user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}

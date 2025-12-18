<?php

namespace Database\Factories;

use App\Models\Athlete;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Athlete>
 */
class AthleteFactory extends Factory
{
    protected $model = Athlete::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sexe = fake()->randomElement(['M', 'F']);
        
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName($sexe === 'M' ? 'male' : 'female'),
            'date_naissance' => fake()->dateTimeBetween('-25 years', '-8 years'),
            'sexe' => $sexe,
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'adresse' => fake()->address(),
            'nom_tuteur' => fake()->name(),
            'telephone_tuteur' => fake()->phoneNumber(),
            'date_inscription' => fake()->dateTimeBetween('-2 years', 'now'),
            'actif' => true,
        ];
    }

    /**
     * Indicate that the athlete is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }

    /**
     * Indicate that the athlete is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'sexe' => 'M',
            'prenom' => fake()->firstName('male'),
        ]);
    }

    /**
     * Indicate that the athlete is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'sexe' => 'F',
            'prenom' => fake()->firstName('female'),
        ]);
    }

    /**
     * Indicate that the athlete is a minor.
     */
    public function minor(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_naissance' => fake()->dateTimeBetween('-17 years', '-8 years'),
        ]);
    }

    /**
     * Indicate that the athlete is an adult.
     */
    public function adult(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_naissance' => fake()->dateTimeBetween('-35 years', '-18 years'),
        ]);
    }
}

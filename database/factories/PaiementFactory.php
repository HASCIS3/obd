<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $montant = fake()->randomElement([15000, 20000, 25000, 30000]);
        $montantPaye = $montant;
        
        return [
            'athlete_id' => Athlete::factory(),
            'montant' => $montant,
            'montant_paye' => $montantPaye,
            'mois' => fake()->numberBetween(1, 12),
            'annee' => fake()->numberBetween(2023, 2025),
            'date_paiement' => fake()->dateTimeBetween('-6 months', 'now'),
            'mode_paiement' => fake()->randomElement([Paiement::MODE_ESPECES, Paiement::MODE_VIREMENT, Paiement::MODE_MOBILE]),
            'statut' => Paiement::STATUT_PAYE,
            'reference' => fake()->optional()->uuid(),
            'remarque' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the payment is unpaid.
     */
    public function impaye(): static
    {
        return $this->state(fn (array $attributes) => [
            'montant_paye' => 0,
            'statut' => Paiement::STATUT_IMPAYE,
            'date_paiement' => null,
        ]);
    }

    /**
     * Indicate that the payment is partial.
     */
    public function partiel(): static
    {
        return $this->state(function (array $attributes) {
            $montantPaye = $attributes['montant'] * fake()->randomFloat(2, 0.2, 0.8);
            return [
                'montant_paye' => $montantPaye,
                'statut' => Paiement::STATUT_PARTIEL,
            ];
        });
    }

    /**
     * Set a specific period.
     */
    public function pourPeriode(int $mois, int $annee): static
    {
        return $this->state(fn (array $attributes) => [
            'mois' => $mois,
            'annee' => $annee,
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

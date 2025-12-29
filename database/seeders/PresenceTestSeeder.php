<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PresenceTestSeeder extends Seeder
{
    /**
     * Génère des données de présence de test pour les 3 derniers mois
     */
    public function run(): void
    {
        $discipline = Discipline::where('actif', true)->first();
        
        if (!$discipline) {
            $this->command->error('Aucune discipline active trouvée');
            return;
        }

        $athletes = Athlete::whereHas('disciplines', function ($q) use ($discipline) {
            $q->where('disciplines.id', $discipline->id);
        })->where('actif', true)->get();

        if ($athletes->isEmpty()) {
            $this->command->error('Aucun athlète actif trouvé pour la discipline ' . $discipline->nom);
            return;
        }

        $this->command->info("Génération des présences pour {$athletes->count()} athlètes en {$discipline->nom}");

        // Générer des présences pour les 3 derniers mois
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        // Jours d'entraînement (lundi, mercredi, vendredi)
        $trainingDays = [1, 3, 5]; // 1=lundi, 3=mercredi, 5=vendredi

        $currentDate = $startDate->copy();
        $totalCreated = 0;

        while ($currentDate->lte($endDate)) {
            // Vérifier si c'est un jour d'entraînement
            if (in_array($currentDate->dayOfWeekIso, $trainingDays)) {
                foreach ($athletes as $athlete) {
                    // Vérifier si une présence existe déjà
                    $exists = Presence::where('athlete_id', $athlete->id)
                        ->where('discipline_id', $discipline->id)
                        ->whereDate('date', $currentDate)
                        ->exists();

                    if (!$exists) {
                        // Taux de présence variable par athlète (60-95%)
                        $tauxPresence = rand(60, 95);
                        $isPresent = rand(1, 100) <= $tauxPresence;

                        Presence::create([
                            'athlete_id' => $athlete->id,
                            'discipline_id' => $discipline->id,
                            'coach_id' => 1,
                            'date' => $currentDate->format('Y-m-d'),
                            'present' => $isPresent,
                            'remarque' => !$isPresent ? $this->getRandomRemarque() : null,
                        ]);

                        $totalCreated++;
                    }
                }
            }

            $currentDate->addDay();
        }

        $this->command->info("✓ {$totalCreated} présences créées avec succès");
    }

    private function getRandomRemarque(): ?string
    {
        $remarques = [
            'Malade',
            'Voyage familial',
            'Examen scolaire',
            'Blessure légère',
            'Rendez-vous médical',
            'Absence justifiée',
            null,
            null,
        ];

        return $remarques[array_rand($remarques)];
    }
}

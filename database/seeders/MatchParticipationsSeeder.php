<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\MatchParticipation;
use Illuminate\Database\Seeder;

class MatchParticipationsSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les athlètes inscrits en Basketball
        $athletes = Athlete::whereHas('disciplines', function ($q) {
            $q->where('disciplines.id', 2);
        })->get();

        // Stats exemple pour chaque joueur [titulaire, minutes, points, passes, rebonds, fautes, note]
        $statsExemple = [
            [true, 32, 18, 5, 8, 2, 8.5],
            [true, 28, 12, 3, 6, 3, 7.0],
            [true, 25, 8, 7, 4, 1, 7.5],
            [false, 15, 5, 2, 3, 2, 6.0],
            [false, 10, 2, 1, 1, 1, 5.5],
        ];

        foreach ($athletes->take(5) as $index => $athlete) {
            $stats = $statsExemple[$index] ?? $statsExemple[0];
            
            MatchParticipation::updateOrCreate(
                [
                    'match_id' => 1,
                    'athlete_id' => $athlete->id,
                ],
                [
                    'titulaire' => $stats[0],
                    'minutes_jouees' => $stats[1],
                    'points_marques' => $stats[2],
                    'passes_decisives' => $stats[3],
                    'rebonds' => $stats[4],
                    'fautes' => $stats[5],
                    'note_performance' => $stats[6],
                ]
            );
        }

        $this->command->info('Participations ajoutées pour ' . min(5, $athletes->count()) . ' athlètes');
    }
}

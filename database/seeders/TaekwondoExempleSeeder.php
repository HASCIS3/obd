<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Rencontre;
use App\Models\MatchParticipation;
use Illuminate\Database\Seeder;

class TaekwondoExempleSeeder extends Seeder
{
    public function run(): void
    {
        // Discipline Taekwondo = ID 6
        $disciplineId = 6;

        // Ajouter des athlètes existants au Taekwondo
        $athletes = Athlete::where('actif', true)->take(3)->get();
        foreach ($athletes as $athlete) {
            $athlete->disciplines()->syncWithoutDetaching([$disciplineId]);
        }
        $this->command->info('Athlètes ajoutés au Taekwondo: ' . $athletes->count());

        // Créer une rencontre de Taekwondo (compétition individuelle)
        $rencontre = Rencontre::create([
            'discipline_id' => $disciplineId,
            'adversaire' => 'Championnat National Taekwondo',
            'date_match' => now()->addDays(15),
            'heure_match' => '09:00:00',
            'lieu' => 'Palais des Sports de Bamako',
            'type_match' => 'exterieur',
            'type_competition' => 'championnat',
            'phase' => 'finale',
            'saison' => '2024-2025',
            'score_obd' => null,
            'score_adversaire' => null,
            'resultat' => 'a_jouer',
            'remarques' => 'Championnat National de Taekwondo - Catégories juniors et seniors.',
        ]);
        $this->command->info('Rencontre Taekwondo créée: ' . $rencontre->adversaire);

        // Créer une rencontre passée avec résultats
        $rencontrePasse = Rencontre::create([
            'discipline_id' => $disciplineId,
            'adversaire' => 'Tournoi Inter-Clubs Taekwondo',
            'date_match' => now()->subDays(10),
            'heure_match' => '10:00:00',
            'lieu' => 'Stade Omnisports Modibo Keita',
            'type_match' => 'domicile',
            'type_competition' => 'tournoi',
            'phase' => 'demi_finale',
            'saison' => '2024-2025',
            'score_obd' => 3, // 3 médailles
            'score_adversaire' => 1, // 1 médaille adverse
            'resultat' => 'victoire',
            'remarques' => 'Excellent tournoi! 2 médailles d\'or et 1 bronze.',
        ]);
        $this->command->info('Rencontre passée créée: ' . $rencontrePasse->adversaire);

        // Ajouter les participations pour la rencontre passée
        $athletesTkd = Athlete::whereHas('disciplines', function ($q) use ($disciplineId) {
            $q->where('disciplines.id', $disciplineId);
        })->take(3)->get();

        $participationsData = [
            ['titulaire' => true, 'points_marques' => 15, 'note_performance' => 9.0], // Or
            ['titulaire' => true, 'points_marques' => 12, 'note_performance' => 8.5], // Or
            ['titulaire' => true, 'points_marques' => 8, 'note_performance' => 7.0],  // Bronze
        ];

        foreach ($athletesTkd as $index => $athlete) {
            $data = $participationsData[$index] ?? $participationsData[0];
            
            MatchParticipation::create([
                'match_id' => $rencontrePasse->id,
                'athlete_id' => $athlete->id,
                'titulaire' => $data['titulaire'],
                'minutes_jouees' => null, // Pas applicable en Taekwondo
                'points_marques' => $data['points_marques'],
                'passes_decisives' => null,
                'rebonds' => null,
                'fautes' => null,
                'note_performance' => $data['note_performance'],
            ]);
        }
        $this->command->info('Participations ajoutées pour ' . $athletesTkd->count() . ' athlètes');
    }
}

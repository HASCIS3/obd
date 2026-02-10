<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Performance;
use App\Models\Rencontre;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRencontres();
        $this->createActivities();
        $this->createPerformances();
    }

    private function createRencontres(): void
    {
        $disciplines = Discipline::where('actif', true)->get();
        $taekwondo = $disciplines->firstWhere('nom', 'Taekwondo') ?? $disciplines->first();
        $basket = $disciplines->firstWhere('nom', 'Basket') ?? $disciplines->skip(1)->first();

        $rencontres = [
            // Matchs à venir
            [
                'discipline_id' => $taekwondo?->id ?? 1,
                'date_match' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'heure_match' => '15:00',
                'type_match' => 'domicile',
                'adversaire' => 'Club Sportif de Ségou',
                'lieu' => 'Stade Omnisports de Bamako',
                'resultat' => 'a_jouer',
                'type_competition' => 'championnat',
                'saison' => '2025-2026',
            ],
            [
                'discipline_id' => $taekwondo?->id ?? 1,
                'date_match' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'heure_match' => '10:00',
                'type_match' => 'exterieur',
                'adversaire' => 'AS Sikasso',
                'lieu' => 'Gymnase de Sikasso',
                'resultat' => 'a_jouer',
                'type_competition' => 'tournoi',
                'saison' => '2025-2026',
            ],
            [
                'discipline_id' => $basket?->id ?? 2,
                'date_match' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'heure_match' => '16:30',
                'type_match' => 'domicile',
                'adversaire' => 'Djoliba AC',
                'lieu' => 'Palais des Sports',
                'resultat' => 'a_jouer',
                'type_competition' => 'coupe',
                'saison' => '2025-2026',
                'phase' => 'quart_finale',
            ],
            // Matchs terminés (résultats)
            [
                'discipline_id' => $taekwondo?->id ?? 1,
                'date_match' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'heure_match' => '14:00',
                'type_match' => 'domicile',
                'adversaire' => 'Stade Malien',
                'lieu' => 'Stade Omnisports de Bamako',
                'score_obd' => 3,
                'score_adversaire' => 1,
                'resultat' => 'victoire',
                'type_competition' => 'championnat',
                'saison' => '2025-2026',
            ],
            [
                'discipline_id' => $taekwondo?->id ?? 1,
                'date_match' => Carbon::now()->subDays(12)->format('Y-m-d'),
                'heure_match' => '11:00',
                'type_match' => 'exterieur',
                'adversaire' => 'Real de Bamako',
                'lieu' => 'Gymnase Municipal',
                'score_obd' => 2,
                'score_adversaire' => 2,
                'resultat' => 'nul',
                'type_competition' => 'amical',
                'saison' => '2025-2026',
            ],
            [
                'discipline_id' => $basket?->id ?? 2,
                'date_match' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'heure_match' => '17:00',
                'type_match' => 'domicile',
                'adversaire' => 'COB Bamako',
                'lieu' => 'Palais des Sports',
                'score_obd' => 68,
                'score_adversaire' => 72,
                'resultat' => 'defaite',
                'type_competition' => 'championnat',
                'saison' => '2025-2026',
            ],
        ];

        foreach ($rencontres as $data) {
            Rencontre::create($data);
        }

        $this->command->info('✅ 6 rencontres créées');
    }

    private function createActivities(): void
    {
        $disciplines = Discipline::where('actif', true)->get();
        $taekwondo = $disciplines->firstWhere('nom', 'Taekwondo') ?? $disciplines->first();

        $activities = [
            // Activités à venir
            [
                'type' => 'competition',
                'titre' => 'Championnat National de Taekwondo 2026',
                'description' => 'Compétition nationale regroupant les meilleurs athlètes du Mali. Catégories juniors et seniors.',
                'lieu' => 'Palais des Sports Salamatou Maïga, Bamako',
                'debut' => Carbon::now()->addDays(15)->setTime(8, 0),
                'fin' => Carbon::now()->addDays(16)->setTime(18, 0),
                'publie' => true,
                'discipline_id' => $taekwondo?->id,
            ],
            [
                'type' => 'tournoi',
                'titre' => 'Tournoi Inter-Clubs de Bamako',
                'description' => 'Tournoi amical entre les clubs de la capitale. Ouvert à toutes les catégories d\'âge.',
                'lieu' => 'Gymnase Modibo Keïta',
                'debut' => Carbon::now()->addDays(8)->setTime(9, 0),
                'fin' => Carbon::now()->addDays(8)->setTime(17, 0),
                'publie' => true,
                'discipline_id' => $taekwondo?->id,
            ],
            [
                'type' => 'entrainement',
                'titre' => 'Stage de perfectionnement technique',
                'description' => 'Stage intensif de 3 jours avec Maître Kim, expert international. Travail des poomsae et combat.',
                'lieu' => 'Centre OBD',
                'debut' => Carbon::now()->addDays(20)->setTime(8, 0),
                'fin' => Carbon::now()->addDays(22)->setTime(16, 0),
                'publie' => true,
                'discipline_id' => $taekwondo?->id,
            ],
            [
                'type' => 'evenement',
                'titre' => 'Cérémonie de remise des ceintures',
                'description' => 'Passage de grades pour les athlètes ayant validé leur examen. Présence des parents souhaitée.',
                'lieu' => 'Salle polyvalente OBD',
                'debut' => Carbon::now()->addDays(5)->setTime(15, 0),
                'fin' => Carbon::now()->addDays(5)->setTime(18, 0),
                'publie' => true,
            ],
            // Activités passées
            [
                'type' => 'competition',
                'titre' => 'Open de Taekwondo de Kayes',
                'description' => 'Compétition régionale. 5 médailles remportées par nos athlètes.',
                'lieu' => 'Stade de Kayes',
                'debut' => Carbon::now()->subDays(10)->setTime(8, 0),
                'fin' => Carbon::now()->subDays(10)->setTime(18, 0),
                'publie' => true,
                'discipline_id' => $taekwondo?->id,
            ],
            [
                'type' => 'galerie',
                'titre' => 'Photos du Gala annuel OBD 2025',
                'description' => 'Retour en images sur notre gala annuel. Démonstrations, remises de prix et moments de convivialité.',
                'lieu' => 'Hôtel Radisson Blu, Bamako',
                'debut' => Carbon::now()->subDays(30)->setTime(19, 0),
                'fin' => Carbon::now()->subDays(30)->setTime(23, 0),
                'publie' => true,
            ],
        ];

        foreach ($activities as $data) {
            Activity::create($data);
        }

        $this->command->info('✅ 6 activités créées');
    }

    private function createPerformances(): void
    {
        $athletes = Athlete::where('actif', true)->take(5)->get();
        $disciplines = Discipline::where('actif', true)->get();
        $taekwondo = $disciplines->firstWhere('nom', 'Taekwondo') ?? $disciplines->first();

        if ($athletes->isEmpty()) {
            $this->command->warn('⚠️ Aucun athlète trouvé pour créer des performances');
            return;
        }

        $performances = [];

        foreach ($athletes as $index => $athlete) {
            // Performance en entraînement
            $performances[] = [
                'athlete_id' => $athlete->id,
                'discipline_id' => $taekwondo?->id ?? 1,
                'contexte' => 'entrainement',
                'date_evaluation' => Carbon::now()->subDays(rand(1, 15))->format('Y-m-d'),
                'note_physique' => rand(12, 18),
                'note_technique' => rand(10, 17),
                'note_comportement' => rand(14, 20),
                'note_globale' => rand(12, 18),
                'observations' => 'Bon travail général. Progression constante.',
            ];

            // Performance en match (pour certains)
            if ($index < 3) {
                $resultat = ['victoire', 'defaite', 'nul'][rand(0, 2)];
                $performances[] = [
                    'athlete_id' => $athlete->id,
                    'discipline_id' => $taekwondo?->id ?? 1,
                    'contexte' => 'match',
                    'date_evaluation' => Carbon::now()->subDays(rand(5, 20))->format('Y-m-d'),
                    'note_physique' => rand(13, 19),
                    'note_technique' => rand(12, 18),
                    'note_comportement' => rand(13, 19),
                    'note_globale' => rand(13, 18),
                    'resultat_match' => $resultat,
                    'adversaire' => ['Club Ségou', 'AS Sikasso', 'Stade Malien'][rand(0, 2)],
                    'points_marques' => rand(5, 15),
                    'points_encaisses' => rand(3, 12),
                    'observations' => $resultat === 'victoire' ? 'Excellente performance!' : 'À améliorer pour le prochain match.',
                ];
            }

            // Performance en compétition (pour certains)
            if ($index < 2) {
                $medailles = [null, 'bronze', 'argent', 'or'];
                $medaille = $medailles[rand(0, 3)];
                $performances[] = [
                    'athlete_id' => $athlete->id,
                    'discipline_id' => $taekwondo?->id ?? 1,
                    'contexte' => 'competition',
                    'date_evaluation' => Carbon::now()->subDays(rand(10, 30))->format('Y-m-d'),
                    'note_physique' => rand(14, 20),
                    'note_technique' => rand(14, 19),
                    'note_comportement' => rand(15, 20),
                    'note_globale' => rand(14, 19),
                    'competition' => 'Championnat Régional 2025',
                    'classement' => $medaille ? rand(1, 3) : rand(4, 8),
                    'medaille' => $medaille,
                    'observations' => $medaille ? "Félicitations pour la médaille de $medaille!" : 'Belle participation, continuez ainsi.',
                ];
            }
        }

        foreach ($performances as $data) {
            Performance::create($data);
        }

        $this->command->info('✅ ' . count($performances) . ' performances créées');
    }
}

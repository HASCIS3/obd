<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\CombatTaekwondo;
use App\Models\Rencontre;
use Illuminate\Database\Seeder;

class CombatsTaekwondoSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les rencontres Taekwondo (discipline_id = 6)
        $rencontresTkd = Rencontre::where('discipline_id', 6)->get();

        if ($rencontresTkd->isEmpty()) {
            $this->command->warn('Aucune rencontre Taekwondo trouvée. Exécutez d\'abord TaekwondoExempleSeeder.');
            return;
        }

        // Récupérer les athlètes Taekwondo
        $athletesTkd = Athlete::whereHas('disciplines', function ($q) {
            $q->where('disciplines.id', 6);
        })->where('actif', true)->get();

        foreach ($rencontresTkd as $rencontre) {
            $this->command->info('Création de combats pour: ' . $rencontre->adversaire);

            // Combat 1: -58kg Junior
            $combat1 = CombatTaekwondo::create([
                'rencontre_id' => $rencontre->id,
                'athlete_rouge_id' => $athletesTkd->first()?->id,
                'nom_rouge' => $athletesTkd->first()?->nom_complet ?? 'Mamadou Diallo',
                'club_rouge' => 'OBD',
                'athlete_bleu_id' => null,
                'nom_bleu' => 'Ibrahima Koné',
                'club_bleu' => 'Club Kayes',
                'categorie_poids' => '-58kg',
                'categorie_age' => 'junior',
                'rounds' => $this->generateRoundsVictoire(),
                'score_rouge' => 18,
                'score_bleu' => 12,
                'statut' => $rencontre->resultat === 'a_jouer' ? 'a_jouer' : 'termine',
                'vainqueur' => $rencontre->resultat === 'a_jouer' ? 'non_determine' : 'rouge',
                'type_victoire' => $rencontre->resultat === 'a_jouer' ? null : 'points',
                'round_actuel' => 1,
            ]);

            // Combat 2: -68kg Senior
            $combat2 = CombatTaekwondo::create([
                'rencontre_id' => $rencontre->id,
                'athlete_rouge_id' => $athletesTkd->skip(1)->first()?->id,
                'nom_rouge' => $athletesTkd->skip(1)->first()?->nom_complet ?? 'Oumar Traoré',
                'club_rouge' => 'OBD',
                'athlete_bleu_id' => null,
                'nom_bleu' => 'Seydou Coulibaly',
                'club_bleu' => 'AS Bamako',
                'categorie_poids' => '-68kg',
                'categorie_age' => 'senior',
                'rounds' => $this->generateRoundsDefaite(),
                'score_rouge' => 10,
                'score_bleu' => 15,
                'statut' => $rencontre->resultat === 'a_jouer' ? 'a_jouer' : 'termine',
                'vainqueur' => $rencontre->resultat === 'a_jouer' ? 'non_determine' : 'bleu',
                'type_victoire' => $rencontre->resultat === 'a_jouer' ? null : 'points',
                'round_actuel' => 1,
            ]);

            // Combat 3: -54kg Cadet (en cours ou à jouer)
            $combat3 = CombatTaekwondo::create([
                'rencontre_id' => $rencontre->id,
                'athlete_rouge_id' => $athletesTkd->skip(2)->first()?->id,
                'nom_rouge' => $athletesTkd->skip(2)->first()?->nom_complet ?? 'Amadou Sangaré',
                'club_rouge' => 'OBD',
                'athlete_bleu_id' => null,
                'nom_bleu' => 'Moussa Keita',
                'club_bleu' => 'Djoliba AC',
                'categorie_poids' => '-54kg',
                'categorie_age' => 'cadet',
                'rounds' => CombatTaekwondo::getDefaultRounds(),
                'score_rouge' => 0,
                'score_bleu' => 0,
                'statut' => 'a_jouer',
                'vainqueur' => 'non_determine',
                'type_victoire' => null,
                'round_actuel' => 1,
            ]);

            $this->command->info('  - 3 combats créés');
        }

        $this->command->info('Combats Taekwondo créés avec succès!');
    }

    private function generateRoundsVictoire(): array
    {
        return [
            1 => [
                'rouge' => ['poing_tronc' => 2, 'pied_tronc' => 3, 'pied_rotatif_tronc' => 0, 'pied_tete' => 1, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 1, 'pied_tronc' => 2, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 1],
            ],
            2 => [
                'rouge' => ['poing_tronc' => 1, 'pied_tronc' => 2, 'pied_rotatif_tronc' => 1, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 2, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 0, 'pied_tete' => 1, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            3 => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 1, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 1],
            ],
            'golden' => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
        ];
    }

    private function generateRoundsDefaite(): array
    {
        return [
            1 => [
                'rouge' => ['poing_tronc' => 1, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 1],
                'bleu' => ['poing_tronc' => 2, 'pied_tronc' => 2, 'pied_rotatif_tronc' => 0, 'pied_tete' => 1, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            2 => [
                'rouge' => ['poing_tronc' => 2, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 1, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 1, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            3 => [
                'rouge' => ['poing_tronc' => 1, 'pied_tronc' => 1, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            'golden' => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\StageFormation;
use App\Models\InscriptionStage;
use Illuminate\Database\Seeder;

class StageFormationSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un stage de formation terminé
        $stage = StageFormation::create([
            'titre' => 'Formation des Formateurs en Football',
            'code' => 'FF-2025-001',
            'description' => 'Formation intensive pour les entraîneurs non qualifiés souhaitant obtenir leur diplôme de formateur.',
            'type' => 'formation_formateurs',
            'date_debut' => now()->subDays(14),
            'date_fin' => now(),
            'lieu' => 'INJS Bamako',
            'organisme' => 'Institut National de la Jeunesse et des Sports (INJS)',
            'programme' => "Module 1: Techniques de base du football\nModule 2: Tactique et stratégie\nModule 3: Préparation physique\nModule 4: Psychologie sportive\nModule 5: Gestion d'équipe",
            'duree_heures' => 80,
            'places_disponibles' => 25,
            'frais_inscription' => 50000,
            'type_certification' => 'diplome',
            'intitule_certification' => 'Diplôme de Formation des Formateurs en Football',
            'statut' => 'termine',
            'objectifs' => "Former des entraîneurs qualifiés capables d'encadrer des équipes de jeunes footballeurs.",
            'conditions_admission' => "Être entraîneur en activité depuis au moins 2 ans.\nAvoir le niveau BAC minimum.",
            'encadreurs' => ['M. Amadou DIALLO', 'M. Ibrahim KEITA'],
        ]);

        // Créer des participants
        $participants = [
            [
                'nom' => 'TRAORE',
                'prenom' => 'Moussa',
                'date_naissance' => '1990-05-15',
                'lieu_naissance' => 'Bamako',
                'sexe' => 'M',
                'telephone' => '+223 76 00 00 01',
                'email' => 'moussa.traore@example.com',
                'fonction' => 'Entraîneur adjoint',
                'structure' => 'AS Réal de Bamako',
                'niveau_etude' => 'bac',
                'experience' => '3 ans comme entraîneur adjoint',
                'statut' => 'diplome',
                'note_finale' => 16.50,
                'appreciation' => 'Très bon participant, assidu et motivé',
                'certificat_delivre' => true,
            ],
            [
                'nom' => 'COULIBALY',
                'prenom' => 'Amadou',
                'date_naissance' => '1988-03-22',
                'lieu_naissance' => 'Ségou',
                'sexe' => 'M',
                'telephone' => '+223 76 00 00 02',
                'email' => 'amadou.coulibaly@example.com',
                'fonction' => 'Entraîneur',
                'structure' => 'Stade Malien',
                'niveau_etude' => 'licence',
                'experience' => '5 ans comme entraîneur',
                'statut' => 'diplome',
                'note_finale' => 18.00,
                'appreciation' => 'Excellent, très impliqué et compétent',
                'certificat_delivre' => true,
            ],
            [
                'nom' => 'DIARRA',
                'prenom' => 'Fatoumata',
                'date_naissance' => '1992-08-10',
                'lieu_naissance' => 'Kayes',
                'sexe' => 'F',
                'telephone' => '+223 76 00 00 03',
                'email' => 'fatoumata.diarra@example.com',
                'fonction' => 'Entraîneur adjoint',
                'structure' => 'Djoliba AC',
                'niveau_etude' => 'bac+2',
                'experience' => '2 ans comme entraîneur adjoint',
                'statut' => 'diplome',
                'note_finale' => 15.25,
                'appreciation' => 'Bonne participation, progrès constants',
                'certificat_delivre' => true,
            ],
            [
                'nom' => 'KEITA',
                'prenom' => 'Oumar',
                'date_naissance' => '1985-11-30',
                'lieu_naissance' => 'Sikasso',
                'sexe' => 'M',
                'telephone' => '+223 76 00 00 04',
                'email' => 'oumar.keita@example.com',
                'fonction' => 'Entraîneur',
                'structure' => 'COB',
                'niveau_etude' => 'bac',
                'experience' => '7 ans comme entraîneur',
                'statut' => 'en_formation',
                'note_finale' => null,
                'appreciation' => null,
                'certificat_delivre' => false,
            ],
        ];

        $numero = 1;
        foreach ($participants as $data) {
            $inscription = new InscriptionStage($data);
            $inscription->stage_formation_id = $stage->id;
            
            if ($data['certificat_delivre']) {
                $inscription->numero_certificat = sprintf('DIP-FF-2025-001-%03d', $numero);
                $inscription->date_delivrance = now();
            }
            
            $inscription->save();
            $numero++;
        }

        // Créer un stage planifié
        StageFormation::create([
            'titre' => 'Formation Arbitrage Basketball',
            'code' => 'AB-2025-001',
            'description' => 'Formation pour devenir arbitre officiel de basketball.',
            'type' => 'initiation',
            'discipline_id' => 2, // Basketball si existe
            'date_debut' => now()->addDays(30),
            'date_fin' => now()->addDays(44),
            'lieu' => 'Palais des Sports de Bamako',
            'organisme' => 'Fédération Malienne de Basketball',
            'duree_heures' => 60,
            'places_disponibles' => 20,
            'frais_inscription' => 35000,
            'type_certification' => 'certificat',
            'intitule_certification' => 'Certificat d\'Arbitrage Basketball Niveau 1',
            'statut' => 'planifie',
            'encadreurs' => ['M. Seydou SANGARE'],
        ]);
    }
}

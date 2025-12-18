<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Discipline;
use Illuminate\Database\Seeder;

class AthleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disciplines = Discipline::all();

        $athletes = [
            [
                'nom' => 'Coulibaly',
                'prenom' => 'Amadou',
                'date_naissance' => '2008-05-15',
                'sexe' => 'M',
                'telephone' => '+223 70 11 22 33',
                'email' => 'amadou.coulibaly@email.com',
                'adresse' => 'Bamako, Magnambougou',
                'nom_tuteur' => 'Bakary Coulibaly',
                'telephone_tuteur' => '+223 76 44 55 66',
                'date_inscription' => '2023-09-01',
                'disciplines' => ['Football', 'Athlétisme'],
            ],
            [
                'nom' => 'Diarra',
                'prenom' => 'Fatoumata',
                'date_naissance' => '2010-03-22',
                'sexe' => 'F',
                'telephone' => '+223 65 77 88 99',
                'email' => 'fatoumata.diarra@email.com',
                'adresse' => 'Bamako, Lafiabougou',
                'nom_tuteur' => 'Mariam Diarra',
                'telephone_tuteur' => '+223 79 00 11 22',
                'date_inscription' => '2023-10-15',
                'disciplines' => ['Basketball', 'Volleyball'],
            ],
            [
                'nom' => 'Sangaré',
                'prenom' => 'Oumar',
                'date_naissance' => '2007-11-08',
                'sexe' => 'M',
                'telephone' => '+223 66 33 44 55',
                'email' => 'oumar.sangare@email.com',
                'adresse' => 'Bamako, Sotuba',
                'nom_tuteur' => 'Seydou Sangaré',
                'telephone_tuteur' => '+223 76 66 77 88',
                'date_inscription' => '2022-01-10',
                'disciplines' => ['Judo'],
            ],
            [
                'nom' => 'Touré',
                'prenom' => 'Aïssata',
                'date_naissance' => '2009-07-30',
                'sexe' => 'F',
                'telephone' => '+223 70 99 00 11',
                'email' => 'aissata.toure@email.com',
                'adresse' => 'Bamako, Djicoroni Para',
                'nom_tuteur' => 'Kadiatou Touré',
                'telephone_tuteur' => '+223 65 22 33 44',
                'date_inscription' => '2023-02-20',
                'disciplines' => ['Natation', 'Athlétisme'],
            ],
            [
                'nom' => 'Konaté',
                'prenom' => 'Mamadou',
                'date_naissance' => '2006-12-05',
                'sexe' => 'M',
                'telephone' => '+223 79 55 66 77',
                'email' => 'mamadou.konate@email.com',
                'adresse' => 'Bamako, Sebenikoro',
                'nom_tuteur' => 'Drissa Konaté',
                'telephone_tuteur' => '+223 66 88 99 00',
                'date_inscription' => '2021-09-01',
                'disciplines' => ['Taekwondo', 'Football'],
            ],
            [
                'nom' => 'Cissé',
                'prenom' => 'Rokia',
                'date_naissance' => '2011-02-14',
                'sexe' => 'F',
                'telephone' => '+223 76 11 22 33',
                'email' => 'rokia.cisse@email.com',
                'adresse' => 'Bamako, Niamakoro',
                'nom_tuteur' => 'Awa Cissé',
                'telephone_tuteur' => '+223 70 44 55 66',
                'date_inscription' => '2024-01-05',
                'disciplines' => ['Handball'],
            ],
        ];

        foreach ($athletes as $athleteData) {
            $disciplineNames = $athleteData['disciplines'];
            unset($athleteData['disciplines']);

            $athlete = Athlete::create(array_merge($athleteData, ['actif' => true]));

            // Attacher les disciplines
            $disciplineIds = $disciplines
                ->whereIn('nom', $disciplineNames)
                ->pluck('id')
                ->mapWithKeys(fn($id) => [$id => [
                    'date_inscription' => $athleteData['date_inscription'],
                    'actif' => true,
                ]]);

            $athlete->disciplines()->attach($disciplineIds);
        }
    }
}

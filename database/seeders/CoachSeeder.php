<?php

namespace Database\Seeders;

use App\Models\Coach;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coachUsers = User::where('role', User::ROLE_COACH)->get();
        $disciplines = Discipline::all();

        $coachData = [
            [
                'telephone' => '+223 76 12 34 56',
                'adresse' => 'Bamako, Hamdallaye ACI 2000',
                'specialite' => 'Football',
                'date_embauche' => '2022-01-15',
                'disciplines' => ['Football', 'Athlétisme'],
            ],
            [
                'telephone' => '+223 66 78 90 12',
                'adresse' => 'Bamako, Badalabougou',
                'specialite' => 'Basketball',
                'date_embauche' => '2021-09-01',
                'disciplines' => ['Basketball', 'Volleyball'],
            ],
            [
                'telephone' => '+223 79 45 67 89',
                'adresse' => 'Bamako, Kalaban Coura',
                'specialite' => 'Judo',
                'date_embauche' => '2023-03-10',
                'disciplines' => ['Judo', 'Taekwondo'],
            ],
        ];

        foreach ($coachUsers as $index => $user) {
            if (isset($coachData[$index])) {
                $data = $coachData[$index];
                
                $coach = Coach::create([
                    'user_id' => $user->id,
                    'telephone' => $data['telephone'],
                    'adresse' => $data['adresse'],
                    'specialite' => $data['specialite'],
                    'date_embauche' => $data['date_embauche'],
                    'actif' => true,
                ]);

                // Attacher les disciplines
                $disciplineIds = $disciplines
                    ->whereIn('nom', $data['disciplines'])
                    ->pluck('id');
                
                $coach->disciplines()->attach($disciplineIds);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disciplines = [
            [
                'nom' => 'Football',
                'description' => 'Sport collectif avec ballon rond',
                'tarif_mensuel' => 15000,
                'actif' => true,
            ],
            [
                'nom' => 'Basketball',
                'description' => 'Sport collectif avec panier',
                'tarif_mensuel' => 15000,
                'actif' => true,
            ],
            [
                'nom' => 'Athlétisme',
                'description' => 'Course, saut et lancer',
                'tarif_mensuel' => 12000,
                'actif' => true,
            ],
            [
                'nom' => 'Natation',
                'description' => 'Sport aquatique',
                'tarif_mensuel' => 20000,
                'actif' => true,
            ],
            [
                'nom' => 'Judo',
                'description' => 'Art martial japonais',
                'tarif_mensuel' => 18000,
                'actif' => true,
            ],
            [
                'nom' => 'Taekwondo',
                'description' => 'Art martial coréen',
                'tarif_mensuel' => 18000,
                'actif' => true,
            ],
            [
                'nom' => 'Handball',
                'description' => 'Sport collectif avec ballon à main',
                'tarif_mensuel' => 15000,
                'actif' => true,
            ],
            [
                'nom' => 'Volleyball',
                'description' => 'Sport collectif avec filet',
                'tarif_mensuel' => 12000,
                'actif' => true,
            ],
        ];

        foreach ($disciplines as $discipline) {
            Discipline::create($discipline);
        }
    }
}

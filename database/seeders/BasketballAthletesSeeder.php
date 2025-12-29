<?php

namespace Database\Seeders;

use App\Models\Athlete;
use Illuminate\Database\Seeder;

class BasketballAthletesSeeder extends Seeder
{
    public function run(): void
    {
        // Ajouter les 5 premiers athlètes actifs à la discipline Basketball (id=2)
        $athletes = Athlete::where('actif', true)->take(5)->get();
        
        foreach ($athletes as $athlete) {
            $athlete->disciplines()->syncWithoutDetaching([2]);
        }
        
        $this->command->info('Athlètes ajoutés au Basketball: ' . $athletes->count());
    }
}

<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Licence;
use Illuminate\Database\Seeder;

class LicencesExemplesSeeder extends Seeder
{
    public function run(): void
    {
        $athletes = Athlete::where('actif', true)->get();
        
        foreach ($athletes as $index => $athlete) {
            // Vérifier si l'athlète a déjà une licence
            if (Licence::where('athlete_id', $athlete->id)->exists()) {
                continue;
            }

            // Récupérer la première discipline de l'athlète
            $discipline = $athlete->disciplines()->first();
            if (!$discipline) {
                continue;
            }

            $numero = sprintf('OBD-2025-%s-%05d-%03d', 
                strtoupper(substr($discipline->nom, 0, 3)),
                $athlete->id,
                rand(1, 999)
            );

            Licence::create([
                'athlete_id' => $athlete->id,
                'discipline_id' => $discipline->id,
                'numero_licence' => $numero,
                'federation' => 'FMJSEP',
                'type' => $index % 2 === 0 ? 'nationale' : 'regionale',
                'categorie' => $athlete->categorie ?? 'Senior',
                'date_emission' => now()->subMonths(rand(1, 6)),
                'date_expiration' => now()->addMonths(rand(3, 12)),
                'statut' => 'active',
                'saison' => '2024-2025',
                'frais_licence' => $index % 2 === 0 ? 15000 : 10000,
                'paye' => rand(0, 1),
                'notes' => 'Licence générée automatiquement',
            ]);
        }

        $this->command->info('Licences créées pour ' . $athletes->count() . ' athlètes');
    }
}

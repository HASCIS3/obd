<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\CertificatMedical;
use Illuminate\Database\Seeder;

class CertificatsMedicauxSeeder extends Seeder
{
    public function run(): void
    {
        $athletes = Athlete::where('actif', true)->get();
        $medecins = ['Dr. Mamadou KEITA', 'Dr. Fatoumata DIARRA', 'Dr. Oumar TRAORE', 'Dr. Aminata COULIBALY'];
        $types = ['aptitude', 'suivi', 'aptitude'];
        
        foreach ($athletes as $index => $athlete) {
            // Vérifier si l'athlète a déjà un certificat
            if (CertificatMedical::where('athlete_id', $athlete->id)->exists()) {
                continue;
            }

            CertificatMedical::create([
                'athlete_id' => $athlete->id,
                'type' => $types[$index % count($types)],
                'medecin' => $medecins[$index % count($medecins)],
                'etablissement' => 'Centre de Santé de Référence',
                'date_examen' => now()->subMonths(rand(1, 3)),
                'date_expiration' => now()->addMonths(rand(6, 12)),
                'apte_competition' => true,
                'apte_entrainement' => true,
                'restrictions' => null,
                'observations' => 'Athlète en bonne santé, apte à la pratique sportive.',
                'statut' => 'valide',
            ]);
        }

        $this->command->info('Certificats médicaux créés pour ' . $athletes->count() . ' athlètes');
    }
}

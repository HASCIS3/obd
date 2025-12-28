<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur parent de test
        $userParent = User::firstOrCreate(
            ['email' => 'parent@obd.ml'],
            [
                'name' => 'Mamadou Diallo',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        // Créer le profil parent
        $parent = ParentModel::firstOrCreate(
            ['user_id' => $userParent->id],
            [
                'telephone' => '+223 76 12 34 56',
                'telephone_secondaire' => '+223 66 78 90 12',
                'adresse' => 'Baco-Djicoroni, Bamako',
                'profession' => 'Commerçant',
                'lien_parente' => 'pere',
                'recevoir_notifications' => true,
                'recevoir_sms' => true,
                'actif' => true,
            ]
        );

        // Lier le parent à des athlètes existants (les 2 premiers)
        $athletes = Athlete::take(2)->get();
        
        foreach ($athletes as $index => $athlete) {
            // Vérifier si la relation n'existe pas déjà
            if (!$parent->athletes()->where('athlete_id', $athlete->id)->exists()) {
                $parent->athletes()->attach($athlete->id, [
                    'lien' => $index === 0 ? 'pere' : 'tuteur',
                    'contact_principal' => $index === 0,
                    'autorise_recuperation' => true,
                ]);
            }
        }

        $this->command->info('Parent de test créé:');
        $this->command->info('Email: parent@obd.ml');
        $this->command->info('Mot de passe: password');
        $this->command->info('Enfants liés: ' . $athletes->count());
    }
}

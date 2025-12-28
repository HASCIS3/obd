<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AthleteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prendre le premier athlète existant
        $athlete = Athlete::first();
        
        if (!$athlete) {
            $this->command->error('Aucun athlète trouvé. Veuillez d\'abord créer des athlètes.');
            return;
        }

        // Créer un utilisateur pour cet athlète
        $user = User::firstOrCreate(
            ['email' => 'athlete@obd.ml'],
            [
                'name' => $athlete->nom_complet,
                'password' => Hash::make('password'),
                'role' => 'athlete',
                'athlete_id' => $athlete->id,
                'email_verified_at' => now(),
            ]
        );

        // Mettre à jour athlete_id si l'utilisateur existait déjà
        if (!$user->wasRecentlyCreated) {
            $user->update(['athlete_id' => $athlete->id]);
        }

        $this->command->info('Compte athlète de test créé:');
        $this->command->info('Email: athlete@obd.ml');
        $this->command->info('Mot de passe: password');
        $this->command->info('Athlète: ' . $athlete->nom_complet);
    }
}

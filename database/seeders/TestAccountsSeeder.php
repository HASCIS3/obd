<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Création des comptes de test...');

        // ============================================
        // COMPTE COACH DE TEST
        // ============================================
        $coachUser = User::updateOrCreate(
            ['email' => 'coach@test.ml'],
            [
                'name' => 'Coach Test',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COACH,
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('✓ Coach créé: coach@test.ml / password');

        // ============================================
        // COMPTE ATHLETE DE TEST
        // ============================================
        // Créer l'athlète d'abord
        $athlete = Athlete::updateOrCreate(
            ['email' => 'athlete@test.ml'],
            [
                'nom' => 'Diallo',
                'prenom' => 'Mamadou',
                'date_naissance' => '2005-03-15',
                'sexe' => 'M',
                'telephone' => '+223 70 00 00 01',
                'adresse' => 'Bamako, Mali',
                'actif' => true,
            ]
        );

        // Créer le compte utilisateur lié à l'athlète
        $athleteUser = User::updateOrCreate(
            ['email' => 'athlete@test.ml'],
            [
                'name' => 'Mamadou Diallo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ATHLETE,
                'athlete_id' => $athlete->id,
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('✓ Athlète créé: athlete@test.ml / password');

        // Associer l'athlète à une discipline si disponible
        $discipline = \App\Models\Discipline::first();
        if ($discipline) {
            $athlete->disciplines()->syncWithoutDetaching([$discipline->id => ['actif' => true]]);
        }

        // ============================================
        // COMPTE PARENT DE TEST
        // ============================================
        $parentUser = User::updateOrCreate(
            ['email' => 'parent@test.ml'],
            [
                'name' => 'Fatou Traoré',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PARENT,
                'email_verified_at' => now(),
            ]
        );

        // Créer le profil parent
        $parent = ParentModel::updateOrCreate(
            ['user_id' => $parentUser->id],
            [
                'telephone' => '+223 70 00 00 02',
                'adresse' => 'Bamako, Mali',
                'actif' => true,
                'recevoir_notifications' => true,
                'recevoir_sms' => true,
            ]
        );

        // Lier le parent à l'athlète créé
        $parent->athletes()->syncWithoutDetaching([$athlete->id]);

        $this->command->info('✓ Parent créé: parent@test.ml / password');
        $this->command->info('  → Lié à l\'athlète: ' . $athlete->nom_complet);

        // ============================================
        // RÉSUMÉ
        // ============================================
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║         COMPTES DE TEST CRÉÉS                    ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║ ADMIN:   admin@centresport.ml / password         ║');
        $this->command->info('║ COACH:   coach@test.ml / password                ║');
        $this->command->info('║ ATHLETE: athlete@test.ml / password              ║');
        $this->command->info('║ PARENT:  parent@test.ml / password               ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
    }
}

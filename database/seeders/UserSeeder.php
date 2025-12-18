<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'administrateur principal
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@centresport.ml',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        // Créer quelques coachs de test
        $coachs = [
            [
                'name' => 'Moussa Traoré',
                'email' => 'moussa.traore@centresport.ml',
            ],
            [
                'name' => 'Aminata Diallo',
                'email' => 'aminata.diallo@centresport.ml',
            ],
            [
                'name' => 'Ibrahim Keita',
                'email' => 'ibrahim.keita@centresport.ml',
            ],
        ];

        foreach ($coachs as $coach) {
            User::create([
                'name' => $coach['name'],
                'email' => $coach['email'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_COACH,
                'email_verified_at' => now(),
            ]);
        }
    }
}

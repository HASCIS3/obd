<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PresenceTaekwondoSeeder extends Seeder
{
    public function run(): void
    {
        $disciplineId = 6; // Taekwondo
        
        $athletes = Athlete::whereHas('disciplines', function ($q) use ($disciplineId) {
            $q->where('disciplines.id', $disciplineId);
        })->where('actif', true)->get();

        if ($athletes->isEmpty()) {
            $this->command->error('Aucun athlete pour Taekwondo');
            return;
        }

        $this->command->info("Generation des presences pour {$athletes->count()} athletes en Taekwondo");

        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();
        $trainingDays = [2, 4, 6]; // Mardi, Jeudi, Samedi
        $currentDate = $startDate->copy();
        $count = 0;

        while ($currentDate->lte($endDate)) {
            if (in_array($currentDate->dayOfWeekIso, $trainingDays)) {
                foreach ($athletes as $athlete) {
                    $exists = Presence::where('athlete_id', $athlete->id)
                        ->where('discipline_id', $disciplineId)
                        ->whereDate('date', $currentDate)
                        ->exists();

                    if (!$exists) {
                        $tauxPresence = rand(65, 90);
                        Presence::create([
                            'athlete_id' => $athlete->id,
                            'discipline_id' => $disciplineId,
                            'coach_id' => 1,
                            'date' => $currentDate->format('Y-m-d'),
                            'present' => rand(1, 100) <= $tauxPresence,
                            'remarque' => null,
                        ]);
                        $count++;
                    }
                }
            }
            $currentDate->addDay();
        }

        $this->command->info("✓ {$count} presences creees pour Taekwondo");
    }
}

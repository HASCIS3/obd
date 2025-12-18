<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PresenceService
{
    /**
     * Enregistre les présences pour une séance
     */
    public function enregistrerPresences(array $presences, int $disciplineId, string $date, ?int $coachId = null): int
    {
        $count = 0;

        DB::transaction(function () use ($presences, $disciplineId, $date, $coachId, &$count) {
            foreach ($presences as $presenceData) {
                Presence::updateOrCreate(
                    [
                        'athlete_id' => $presenceData['athlete_id'],
                        'discipline_id' => $disciplineId,
                        'date' => $date,
                    ],
                    [
                        'coach_id' => $coachId,
                        'present' => $presenceData['present'],
                        'remarque' => $presenceData['remarque'] ?? null,
                    ]
                );
                $count++;
            }
        });

        return $count;
    }

    /**
     * Marque un athlète comme présent
     */
    public function marquerPresent(Athlete $athlete, Discipline $discipline, string $date, ?Coach $coach = null, ?string $remarque = null): Presence
    {
        return Presence::updateOrCreate(
            [
                'athlete_id' => $athlete->id,
                'discipline_id' => $discipline->id,
                'date' => $date,
            ],
            [
                'coach_id' => $coach?->id,
                'present' => true,
                'remarque' => $remarque,
            ]
        );
    }

    /**
     * Marque un athlète comme absent
     */
    public function marquerAbsent(Athlete $athlete, Discipline $discipline, string $date, ?Coach $coach = null, ?string $remarque = null): Presence
    {
        return Presence::updateOrCreate(
            [
                'athlete_id' => $athlete->id,
                'discipline_id' => $discipline->id,
                'date' => $date,
            ],
            [
                'coach_id' => $coach?->id,
                'present' => false,
                'remarque' => $remarque,
            ]
        );
    }

    /**
     * Récupère les statistiques de présence d'un athlète
     */
    public function getStatistiquesAthlete(Athlete $athlete, ?int $disciplineId = null): array
    {
        $query = $athlete->presences();

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        $total = $query->count();
        $presents = (clone $query)->where('present', true)->count();
        $absents = $total - $presents;

        return [
            'total' => $total,
            'presents' => $presents,
            'absents' => $absents,
            'taux' => $total > 0 ? round(($presents / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Récupère les statistiques de présence par discipline pour un mois
     */
    public function getStatistiquesMensuelles(int $mois, int $annee): array
    {
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        $stats = [];

        foreach ($disciplines as $discipline) {
            $total = Presence::where('discipline_id', $discipline->id)
                ->whereMonth('date', $mois)
                ->whereYear('date', $annee)
                ->count();

            $presents = Presence::where('discipline_id', $discipline->id)
                ->whereMonth('date', $mois)
                ->whereYear('date', $annee)
                ->where('present', true)
                ->count();

            $stats[$discipline->id] = [
                'discipline' => $discipline->nom,
                'discipline_id' => $discipline->id,
                'total' => $total,
                'presents' => $presents,
                'absents' => $total - $presents,
                'taux' => $total > 0 ? round(($presents / $total) * 100, 1) : 0,
            ];
        }

        return $stats;
    }

    /**
     * Récupère les athlètes absents fréquemment
     */
    public function getAthletesAbsentsFrequents(int $seuilAbsences = 3, int $joursAnalyse = 30): Collection
    {
        $dateDebut = now()->subDays($joursAnalyse);

        return Athlete::where('actif', true)
            ->whereHas('presences', function ($query) use ($dateDebut, $seuilAbsences) {
                $query->where('date', '>=', $dateDebut)
                    ->where('present', false);
            }, '>=', $seuilAbsences)
            ->withCount(['presences as absences_count' => function ($query) use ($dateDebut) {
                $query->where('date', '>=', $dateDebut)
                    ->where('present', false);
            }])
            ->orderByDesc('absences_count')
            ->get();
    }

    /**
     * Récupère les présences d'une journée
     */
    public function getPresencesJour(string $date, ?int $disciplineId = null): Collection
    {
        $query = Presence::with(['athlete', 'discipline', 'coach.user'])
            ->whereDate('date', $date);

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Récupère les athlètes d'une discipline pour la saisie des présences
     */
    public function getAthletesForPresence(int $disciplineId): Collection
    {
        return Athlete::whereHas('disciplines', function ($q) use ($disciplineId) {
            $q->where('disciplines.id', $disciplineId)
                ->where('athlete_discipline.actif', true);
        })->where('actif', true)->orderBy('nom')->get();
    }

    /**
     * Récupère les présences existantes pour une date et discipline
     */
    public function getPresencesExistantes(int $disciplineId, string $date): array
    {
        return Presence::where('discipline_id', $disciplineId)
            ->whereDate('date', $date)
            ->pluck('present', 'athlete_id')
            ->toArray();
    }

    /**
     * Calcule le taux de présence global du mois en cours
     */
    public function getTauxPresenceMoisCourant(): array
    {
        $total = Presence::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $presents = Presence::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('present', true)
            ->count();

        return [
            'total' => $total,
            'presents' => $presents,
            'absents' => $total - $presents,
            'taux' => $total > 0 ? round(($presents / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Génère un rapport de présence pour une période
     */
    public function genererRapport(Carbon $dateDebut, Carbon $dateFin, ?int $disciplineId = null): array
    {
        $query = Presence::whereBetween('date', [$dateDebut, $dateFin]);

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        $presences = $query->get();

        $parAthlete = $presences->groupBy('athlete_id')->map(function ($group) {
            return [
                'total' => $group->count(),
                'presents' => $group->where('present', true)->count(),
                'absents' => $group->where('present', false)->count(),
            ];
        });

        $parDiscipline = $presences->groupBy('discipline_id')->map(function ($group) {
            return [
                'total' => $group->count(),
                'presents' => $group->where('present', true)->count(),
                'absents' => $group->where('present', false)->count(),
            ];
        });

        $total = $presences->count();
        $presents = $presences->where('present', true)->count();

        return [
            'periode' => [
                'debut' => $dateDebut->format('d/m/Y'),
                'fin' => $dateFin->format('d/m/Y'),
            ],
            'global' => [
                'total' => $total,
                'presents' => $presents,
                'absents' => $total - $presents,
                'taux' => $total > 0 ? round(($presents / $total) * 100, 1) : 0,
            ],
            'par_athlete' => $parAthlete,
            'par_discipline' => $parDiscipline,
        ];
    }
}

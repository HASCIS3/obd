<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Performance;
use Illuminate\Database\Eloquent\Collection;

class PerformanceService
{
    /**
     * Enregistre une nouvelle performance
     */
    public function enregistrer(array $data): Performance
    {
        return Performance::create($data);
    }

    /**
     * Met à jour une performance
     */
    public function mettreAJour(Performance $performance, array $data): Performance
    {
        $performance->update($data);
        return $performance->fresh();
    }

    /**
     * Récupère l'évolution des performances d'un athlète
     */
    public function getEvolutionAthlete(Athlete $athlete, ?int $disciplineId = null): Collection
    {
        $query = $athlete->performances()->with('discipline');

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        return $query->orderBy('date_evaluation')->get();
    }

    /**
     * Récupère les meilleures performances d'un athlète
     */
    public function getMeilleuresPerformances(Athlete $athlete, int $limit = 5): Collection
    {
        return $athlete->performances()
            ->with('discipline')
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->take($limit)
            ->get();
    }

    /**
     * Récupère les performances en compétition
     */
    public function getPerformancesCompetition(Athlete $athlete): Collection
    {
        return $athlete->performances()
            ->with('discipline')
            ->whereNotNull('competition')
            ->orderByDesc('date_evaluation')
            ->get();
    }

    /**
     * Calcule les statistiques de performance d'un athlète
     */
    public function getStatistiquesAthlete(Athlete $athlete, ?int $disciplineId = null): array
    {
        $query = $athlete->performances();

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        $performances = $query->get();

        if ($performances->isEmpty()) {
            return [
                'total' => 0,
                'competitions' => 0,
                'meilleur_classement' => null,
                'score_moyen' => null,
                'score_max' => null,
                'progression' => null,
            ];
        }

        $scoresNonNuls = $performances->whereNotNull('score');

        return [
            'total' => $performances->count(),
            'competitions' => $performances->whereNotNull('competition')->count(),
            'meilleur_classement' => $performances->whereNotNull('classement')->min('classement'),
            'score_moyen' => $scoresNonNuls->isNotEmpty() ? round($scoresNonNuls->avg('score'), 2) : null,
            'score_max' => $scoresNonNuls->max('score'),
            'progression' => $this->calculerProgression($performances),
        ];
    }

    /**
     * Calcule la progression (différence entre première et dernière performance)
     */
    public function calculerProgression(Collection $performances): ?float
    {
        $performancesAvecScore = $performances->whereNotNull('score')->sortBy('date_evaluation');

        if ($performancesAvecScore->count() < 2) {
            return null;
        }

        $premiere = $performancesAvecScore->first()->score;
        $derniere = $performancesAvecScore->last()->score;

        if ($premiere == 0) {
            return null;
        }

        return round((($derniere - $premiere) / $premiere) * 100, 1);
    }

    /**
     * Récupère le classement des athlètes par discipline
     */
    public function getClassementDiscipline(Discipline $discipline, ?string $typeEvaluation = null): Collection
    {
        $query = Performance::with('athlete')
            ->where('discipline_id', $discipline->id)
            ->whereNotNull('score');

        if ($typeEvaluation) {
            $query->where('type_evaluation', $typeEvaluation);
        }

        // Récupère la meilleure performance de chaque athlète
        return $query->get()
            ->groupBy('athlete_id')
            ->map(function ($performances) {
                $meilleure = $performances->sortByDesc('score')->first();
                return [
                    'athlete' => $meilleure->athlete,
                    'meilleur_score' => $meilleure->score,
                    'unite' => $meilleure->unite,
                    'date' => $meilleure->date_evaluation,
                    'nb_performances' => $performances->count(),
                ];
            })
            ->sortByDesc('meilleur_score')
            ->values();
    }

    /**
     * Récupère les types d'évaluation disponibles
     */
    public function getTypesEvaluation(?int $disciplineId = null): Collection
    {
        $query = Performance::distinct();

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        return $query->pluck('type_evaluation')->filter()->sort()->values();
    }

    /**
     * Compare les performances de deux athlètes
     */
    public function comparerAthletes(Athlete $athlete1, Athlete $athlete2, int $disciplineId): array
    {
        $perf1 = $athlete1->performances()
            ->where('discipline_id', $disciplineId)
            ->whereNotNull('score')
            ->get();

        $perf2 = $athlete2->performances()
            ->where('discipline_id', $disciplineId)
            ->whereNotNull('score')
            ->get();

        return [
            'athlete1' => [
                'athlete' => $athlete1,
                'nb_performances' => $perf1->count(),
                'score_moyen' => $perf1->isNotEmpty() ? round($perf1->avg('score'), 2) : null,
                'score_max' => $perf1->max('score'),
                'progression' => $this->calculerProgression($perf1),
            ],
            'athlete2' => [
                'athlete' => $athlete2,
                'nb_performances' => $perf2->count(),
                'score_moyen' => $perf2->isNotEmpty() ? round($perf2->avg('score'), 2) : null,
                'score_max' => $perf2->max('score'),
                'progression' => $this->calculerProgression($perf2),
            ],
        ];
    }

    /**
     * Récupère les performances récentes
     */
    public function getPerformancesRecentes(int $limit = 10): Collection
    {
        return Performance::with(['athlete', 'discipline'])
            ->orderByDesc('date_evaluation')
            ->take($limit)
            ->get();
    }

    /**
     * Récupère les athlètes les plus performants
     */
    public function getTopPerformeurs(int $disciplineId, int $limit = 10): Collection
    {
        return Performance::with('athlete')
            ->where('discipline_id', $disciplineId)
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->take($limit)
            ->get()
            ->unique('athlete_id');
    }
}

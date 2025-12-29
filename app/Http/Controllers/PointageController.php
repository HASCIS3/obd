<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class PointageController extends Controller
{
    /**
     * Récupère les données de base pour le pointage
     */
    private function getBaseData(Request $request): array
    {
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        $disciplineId = $request->discipline;
        $selectedDiscipline = $disciplineId ? Discipline::find($disciplineId) : null;
        
        $athletes = collect();
        if ($disciplineId) {
            $athletes = Athlete::whereHas('disciplines', function ($q) use ($disciplineId) {
                $q->where('disciplines.id', $disciplineId)
                    ->where('athlete_discipline.actif', true);
            })->where('actif', true)->orderBy('nom')->get();
        }

        return compact('disciplines', 'disciplineId', 'selectedDiscipline', 'athletes');
    }

    /**
     * Pointage quotidien
     */
    public function quotidien(Request $request): View
    {
        $baseData = $this->getBaseData($request);
        extract($baseData);

        $date = $request->date ?? now()->format('Y-m-d');
        $existingPresences = collect();
        $existingRemarks = collect();
        $athleteStats = [];

        if ($disciplineId && $athletes->count() > 0) {
            $todayPresences = Presence::where('discipline_id', $disciplineId)
                ->whereDate('date', $date)
                ->get();
            
            $existingPresences = $todayPresences->pluck('present', 'athlete_id');
            $existingRemarks = $todayPresences->pluck('remarque', 'athlete_id');

            $startOfWeek = Carbon::parse($date)->startOfWeek();
            $endOfWeek = Carbon::parse($date)->endOfWeek();
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();

            foreach ($athletes as $athlete) {
                $weekPresences = Presence::where('athlete_id', $athlete->id)
                    ->where('discipline_id', $disciplineId)
                    ->whereBetween('date', [$startOfWeek, $endOfWeek])
                    ->get();
                
                $weekTotal = $weekPresences->count();
                $weekPresents = $weekPresences->where('present', true)->count();

                $monthPresences = Presence::where('athlete_id', $athlete->id)
                    ->where('discipline_id', $disciplineId)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->get();
                
                $monthTotal = $monthPresences->count();
                $monthPresents = $monthPresences->where('present', true)->count();

                $athleteStats[$athlete->id] = [
                    'week' => [
                        'total' => $weekTotal,
                        'presents' => $weekPresents,
                        'taux' => $weekTotal > 0 ? round(($weekPresents / $weekTotal) * 100) : 0,
                    ],
                    'month' => [
                        'total' => $monthTotal,
                        'presents' => $monthPresents,
                        'taux' => $monthTotal > 0 ? round(($monthPresents / $monthTotal) * 100) : 0,
                    ],
                ];
            }
        }

        $periode = 'quotidien';

        return view('presences.pointage.quotidien', compact(
            'disciplines', 'athletes', 'date', 'disciplineId', 
            'existingPresences', 'existingRemarks', 'athleteStats', 
            'selectedDiscipline', 'periode'
        ));
    }

    /**
     * Pointage hebdomadaire
     */
    public function hebdomadaire(Request $request): View
    {
        $baseData = $this->getBaseData($request);
        extract($baseData);

        $date = $request->date ?? now()->format('Y-m-d');
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        $joursSemaine = [];
        $joursNoms = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $joursComplets = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        for ($i = 0; $i < 7; $i++) {
            $jour = $startOfWeek->copy()->addDays($i);
            $joursSemaine[] = [
                'date' => $jour,
                'nom' => $joursComplets[$i],
                'nom_court' => $joursNoms[$i],
            ];
        }

        $athleteStats = [];
        $statsGlobales = ['total_seances' => 0, 'total_pointages' => 0, 'presents' => 0, 'absents' => 0, 'taux' => 0];
        $topAthletes = [];

        if ($disciplineId && $athletes->count() > 0) {
            $presencesSemaine = Presence::where('discipline_id', $disciplineId)
                ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->get();

            foreach ($athletes as $athlete) {
                $athletePresences = $presencesSemaine->where('athlete_id', $athlete->id);
                $presencesParJour = [];
                
                foreach ($joursSemaine as $jour) {
                    $dateKey = $jour['date']->format('Y-m-d');
                    $presence = $athletePresences->first(function ($p) use ($dateKey) {
                        return Carbon::parse($p->date)->format('Y-m-d') === $dateKey;
                    });
                    $presencesParJour[$dateKey] = $presence ? (bool)$presence->present : null;
                }

                $total = $athletePresences->count();
                $presents = $athletePresences->where('present', true)->count();

                $athleteStats[$athlete->id] = [
                    'presences' => $presencesParJour,
                    'total' => $total,
                    'presents' => $presents,
                    'taux' => $total > 0 ? round(($presents / $total) * 100) : 0,
                ];

                $statsGlobales['total_pointages'] += $total;
                $statsGlobales['presents'] += $presents;
                $statsGlobales['absents'] += ($total - $presents);
            }

            $statsGlobales['total_seances'] = $presencesSemaine->groupBy('date')->count();
            $statsGlobales['taux'] = $statsGlobales['total_pointages'] > 0 
                ? round(($statsGlobales['presents'] / $statsGlobales['total_pointages']) * 100) 
                : 0;

            $topAthletes = collect($athleteStats)
                ->map(fn($stats, $id) => [
                    'id' => $id,
                    'nom' => $athletes->firstWhere('id', $id)?->nom_complet ?? 'Inconnu',
                    'taux' => $stats['taux'],
                ])
                ->sortByDesc('taux')
                ->take(3)
                ->values()
                ->toArray();
        }

        $periode = 'hebdomadaire';

        return view('presences.pointage.hebdomadaire', compact(
            'disciplines', 'athletes', 'disciplineId', 'selectedDiscipline',
            'startOfWeek', 'endOfWeek', 'joursSemaine', 'athleteStats',
            'statsGlobales', 'topAthletes', 'periode'
        ));
    }

    /**
     * Pointage mensuel
     */
    public function mensuel(Request $request): View
    {
        $baseData = $this->getBaseData($request);
        extract($baseData);

        $mois = (int)($request->mois ?? now()->month);
        $annee = (int)($request->annee ?? now()->year);

        $startOfMonth = Carbon::create($annee, $mois, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $nomMois = $startOfMonth->locale('fr')->isoFormat('MMMM');

        $moisOptions = [];
        for ($m = 1; $m <= 12; $m++) {
            $moisOptions[] = ['id' => $m, 'name' => Carbon::create(null, $m, 1)->locale('fr')->isoFormat('MMMM')];
        }

        $anneeOptions = [];
        for ($a = now()->year - 2; $a <= now()->year + 1; $a++) {
            $anneeOptions[] = ['id' => $a, 'name' => (string)$a];
        }

        $moisPrecedent = $mois === 1 ? ['mois' => 12, 'annee' => $annee - 1] : ['mois' => $mois - 1, 'annee' => $annee];
        $moisSuivant = $mois === 12 ? ['mois' => 1, 'annee' => $annee + 1] : ['mois' => $mois + 1, 'annee' => $annee];

        $nbSemaines = (int)ceil($endOfMonth->day / 7);
        if ($nbSemaines < 4) $nbSemaines = 4;
        if ($nbSemaines > 5) $nbSemaines = 5;

        $athleteStats = [];
        $statsGlobales = ['jours_seance' => 0, 'total' => 0, 'presents' => 0, 'absents' => 0, 'taux' => 0];
        $statsParSemaine = [];
        $topAthletes = collect();
        $athletesEnDifficulte = collect();
        $chartLabels = [];
        $chartData = [];

        if ($disciplineId && $athletes->count() > 0) {
            $presencesMois = Presence::where('discipline_id', $disciplineId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();

            $statsGlobales['jours_seance'] = $presencesMois->groupBy('date')->count();

            foreach ($athletes as $athlete) {
                $athletePresences = $presencesMois->where('athlete_id', $athlete->id);
                $semaines = [];

                for ($s = 1; $s <= $nbSemaines; $s++) {
                    $debutSemaine = $startOfMonth->copy()->addDays(($s - 1) * 7);
                    $finSemaine = $debutSemaine->copy()->addDays(6);
                    if ($finSemaine->gt($endOfMonth)) $finSemaine = $endOfMonth->copy();

                    $presencesSemaine = $athletePresences->filter(function ($p) use ($debutSemaine, $finSemaine) {
                        $date = Carbon::parse($p->date);
                        return $date->gte($debutSemaine) && $date->lte($finSemaine);
                    });

                    $total = $presencesSemaine->count();
                    $presents = $presencesSemaine->where('present', true)->count();

                    $semaines[$s] = [
                        'total' => $total,
                        'presents' => $presents,
                        'taux' => $total > 0 ? round(($presents / $total) * 100) : 0,
                    ];

                    if (!isset($statsParSemaine[$s])) {
                        $statsParSemaine[$s] = ['total' => 0, 'presents' => 0, 'taux' => 0];
                    }
                    $statsParSemaine[$s]['total'] += $total;
                    $statsParSemaine[$s]['presents'] += $presents;
                }

                $totalAthlete = $athletePresences->count();
                $presentsAthlete = $athletePresences->where('present', true)->count();
                $absentsAthlete = $totalAthlete - $presentsAthlete;
                $tauxAthlete = $totalAthlete > 0 ? round(($presentsAthlete / $totalAthlete) * 100) : 0;

                $athleteStats[$athlete->id] = [
                    'semaines' => $semaines,
                    'presents' => $presentsAthlete,
                    'absents' => $absentsAthlete,
                    'total' => $totalAthlete,
                    'taux' => $tauxAthlete,
                ];

                $statsGlobales['total'] += $totalAthlete;
                $statsGlobales['presents'] += $presentsAthlete;
                $statsGlobales['absents'] += $absentsAthlete;
            }

            foreach ($statsParSemaine as $s => $stats) {
                $statsParSemaine[$s]['taux'] = $stats['total'] > 0 ? round(($stats['presents'] / $stats['total']) * 100) : 0;
            }

            $statsGlobales['taux'] = $statsGlobales['total'] > 0 
                ? round(($statsGlobales['presents'] / $statsGlobales['total']) * 100) 
                : 0;

            $topAthletes = collect($athleteStats)
                ->map(fn($stats, $id) => [
                    'id' => $id,
                    'nom' => $athletes->firstWhere('id', $id)?->nom_complet ?? 'Inconnu',
                    'taux' => $stats['taux'],
                ])
                ->sortByDesc('taux')
                ->values();

            $athletesEnDifficulte = $topAthletes->filter(fn($a) => $a['taux'] < 50)->sortBy('taux')->values();

            for ($s = 1; $s <= $nbSemaines; $s++) {
                $chartLabels[] = "Semaine $s";
                $chartData[] = $statsParSemaine[$s]['taux'] ?? 0;
            }
        }

        $periode = 'mensuel';

        return view('presences.pointage.mensuel', compact(
            'disciplines', 'athletes', 'disciplineId', 'selectedDiscipline',
            'mois', 'annee', 'nomMois', 'moisOptions', 'anneeOptions',
            'moisPrecedent', 'moisSuivant', 'nbSemaines',
            'athleteStats', 'statsGlobales', 'statsParSemaine',
            'topAthletes', 'athletesEnDifficulte',
            'chartLabels', 'chartData', 'periode'
        ));
    }

    /**
     * Pointage annuel
     */
    public function annuel(Request $request): View
    {
        $baseData = $this->getBaseData($request);
        extract($baseData);

        $annee = (int)($request->annee ?? now()->year);

        $anneeOptions = [];
        for ($a = now()->year - 3; $a <= now()->year + 1; $a++) {
            $anneeOptions[] = ['id' => $a, 'name' => (string)$a];
        }

        $moisLabels = [];
        for ($m = 1; $m <= 12; $m++) {
            $moisLabels[$m] = Carbon::create(null, $m, 1)->locale('fr')->isoFormat('MMM');
        }

        $athleteStats = [];
        $statsGlobales = ['mois_actifs' => 0, 'total' => 0, 'presents' => 0, 'absents' => 0, 'taux' => 0];
        $statsParMois = [];
        $topAthletes = collect();
        $chartData = [];
        $chartColors = [];
        $meilleurMois = ['nom' => null, 'taux' => 0];
        $pireMois = ['nom' => null, 'taux' => 100];

        if ($disciplineId && $athletes->count() > 0) {
            $presencesAnnee = Presence::where('discipline_id', $disciplineId)
                ->whereYear('date', $annee)
                ->get();

            $moisActifs = $presencesAnnee->groupBy(fn($p) => Carbon::parse($p->date)->month)->keys();
            $statsGlobales['mois_actifs'] = $moisActifs->count();

            for ($m = 1; $m <= 12; $m++) {
                $presencesMois = $presencesAnnee->filter(fn($p) => Carbon::parse($p->date)->month === $m);
                $total = $presencesMois->count();
                $presents = $presencesMois->where('present', true)->count();
                $taux = $total > 0 ? round(($presents / $total) * 100) : 0;

                $statsParMois[$m] = ['total' => $total, 'presents' => $presents, 'taux' => $taux];
                $chartData[] = $taux;
                $chartColors[] = $taux >= 80 ? 'rgba(34, 197, 94, 0.8)' : ($taux >= 50 ? 'rgba(234, 179, 8, 0.8)' : 'rgba(239, 68, 68, 0.8)');

                if ($total > 0) {
                    if ($taux > $meilleurMois['taux']) {
                        $meilleurMois = ['nom' => $moisLabels[$m], 'taux' => $taux];
                    }
                    if ($taux < $pireMois['taux']) {
                        $pireMois = ['nom' => $moisLabels[$m], 'taux' => $taux];
                    }
                }
            }

            foreach ($athletes as $athlete) {
                $athletePresences = $presencesAnnee->where('athlete_id', $athlete->id);
                $moisStats = [];

                for ($m = 1; $m <= 12; $m++) {
                    $presencesMois = $athletePresences->filter(fn($p) => Carbon::parse($p->date)->month === $m);
                    $total = $presencesMois->count();
                    $presents = $presencesMois->where('present', true)->count();

                    $moisStats[$m] = [
                        'total' => $total,
                        'presents' => $presents,
                        'taux' => $total > 0 ? round(($presents / $total) * 100) : 0,
                    ];
                }

                $totalAthlete = $athletePresences->count();
                $presentsAthlete = $athletePresences->where('present', true)->count();

                $athleteStats[$athlete->id] = [
                    'mois' => $moisStats,
                    'presents' => $presentsAthlete,
                    'total' => $totalAthlete,
                    'taux' => $totalAthlete > 0 ? round(($presentsAthlete / $totalAthlete) * 100) : 0,
                ];

                $statsGlobales['total'] += $totalAthlete;
                $statsGlobales['presents'] += $presentsAthlete;
            }

            $statsGlobales['absents'] = $statsGlobales['total'] - $statsGlobales['presents'];
            $statsGlobales['taux'] = $statsGlobales['total'] > 0 
                ? round(($statsGlobales['presents'] / $statsGlobales['total']) * 100) 
                : 0;

            $topAthletes = collect($athleteStats)
                ->map(fn($stats, $id) => [
                    'id' => $id,
                    'nom' => $athletes->firstWhere('id', $id)?->nom_complet ?? 'Inconnu',
                    'taux' => $stats['taux'],
                ])
                ->sortByDesc('taux')
                ->values();
        }

        $periode = 'annuel';

        return view('presences.pointage.annuel', compact(
            'disciplines', 'athletes', 'disciplineId', 'selectedDiscipline',
            'annee', 'anneeOptions', 'moisLabels',
            'athleteStats', 'statsGlobales', 'statsParMois',
            'topAthletes', 'chartData', 'chartColors',
            'meilleurMois', 'pireMois', 'periode'
        ));
    }
}

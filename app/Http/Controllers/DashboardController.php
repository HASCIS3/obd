<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Paiement;
use App\Models\Performance;
use App\Models\Presence;
use App\Models\Rencontre;
use App\Models\Activity;
use App\Models\CombatTaekwondo;
use App\Services\StatistiqueService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        protected StatistiqueService $statistiqueService
    ) {}

    /**
     * Affiche le tableau de bord principal (Web ou API)
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        // Rediriger les parents vers leur portail dédié
        if (auth()->user()->role === 'parent') {
            return redirect()->route('parent.dashboard');
        }

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->apiIndex($request);
        }

        // Sinon, retourner la vue web
        return $this->webIndex();
    }

    /**
     * Dashboard pour l'API mobile
     */
    protected function apiIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Stats générales
        $stats = [
            'athletes_actifs' => Athlete::actifs()->count(),
            'athletes_total' => Athlete::count(),
            'disciplines' => Discipline::actives()->count(),
            'coachs' => Coach::count(),
            'presences_jour' => Presence::whereDate('date', $now->toDateString())->where('present', true)->count(),
        ];

        // Stats paiements du mois
        $paiementsMois = Paiement::where('mois', $currentMonth)
            ->where('annee', $currentYear)
            ->get();

        $stats['paiements'] = [
            'total' => $paiementsMois->sum('montant'),
            'paye' => $paiementsMois->sum('montant_paye'),
            'arrieres' => $paiementsMois->where('statut', 'impaye')->count(),
        ];

        // Activités récentes (dernières présences) - Augmenter la limite et inclure toutes les disciplines
        $activitesRecentes = Presence::with(['athlete', 'discipline'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($presence) {
                return [
                    'id' => $presence->id,
                    'type' => 'presence',
                    'athlete' => $presence->athlete?->nom_complet,
                    'discipline' => $presence->discipline?->nom,
                    'date' => $presence->date ? $presence->date->toIso8601String() : null,
                    'present' => $presence->present,
                ];
            });

        // Stats rencontres
        $statsRencontres = [
            'total' => Rencontre::count(),
            'a_venir' => Rencontre::where('date_match', '>=', $now->toDateString())->count(),
            'victoires' => Rencontre::where('resultat', 'victoire')->count(),
            'defaites' => Rencontre::where('resultat', 'defaite')->count(),
            'nuls' => Rencontre::where('resultat', 'nul')->count(),
        ];

        // Stats activités
        $statsActivites = [
            'total' => Activity::where('publie', true)->count(),
            'a_venir' => Activity::where('publie', true)->where('debut', '>=', $now->startOfDay())->count(),
        ];

        // Stats performances
        $statsPerformances = [
            'matchs' => Performance::where('contexte', 'match')->count(),
            'medailles' => Performance::whereNotNull('medaille')->count(),
            'note_moyenne' => round(Performance::avg('note_globale') ?? 0, 1),
        ];

        // Prochaines rencontres
        $prochainesRencontres = Rencontre::with('discipline')
            ->where('date_match', '>=', $now->toDateString())
            ->orderBy('date_match')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'adversaire' => $r->adversaire,
                    'date_match' => $r->date_match,
                    'discipline' => $r->discipline?->nom,
                    'lieu' => $r->lieu,
                    'type_competition' => $r->type_competition,
                ];
            });

        // Prochaines activités
        $prochainesActivites = Activity::where('publie', true)
            ->where('debut', '>=', $now->startOfDay())
            ->orderBy('debut')
            ->limit(5)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'titre' => $a->titre,
                    'type' => $a->type,
                    'debut' => $a->debut,
                    'lieu' => $a->lieu,
                ];
            });

        // Si c'est un coach, filtrer par ses disciplines
        if ($user && $user->isCoach() && $user->coach) {
            $disciplineIds = $user->coach->disciplines->pluck('id');
            $stats['athletes_actifs'] = Athlete::actifs()
                ->whereHas('disciplines', fn($q) => $q->whereIn('disciplines.id', $disciplineIds))
                ->count();
        }

        return response()->json([
            'stats' => $stats,
            'activites_recentes' => $activitesRecentes,
            'stats_rencontres' => $statsRencontres,
            'stats_activites' => $statsActivites,
            'stats_performances' => $statsPerformances,
            'prochaines_rencontres' => $prochainesRencontres,
            'prochaines_activites' => $prochainesActivites,
            'user' => $user ? [
                'name' => $user->name,
                'role' => $user->role,
            ] : null,
        ]);
    }

    /**
     * Dashboard pour le web
     */
    protected function webIndex(): View
    {
        if (auth()->user()?->isAthlete()) {
            return view('dashboard-athlete');
        }

        // Statistiques principales
        $dashboardStats = $this->statistiqueService->getDashboardStats();

        $stats = [
            'total_athletes' => $dashboardStats['athletes']['actifs'],
            'total_coachs' => $dashboardStats['coachs']['actifs'],
            'total_disciplines' => $dashboardStats['disciplines']['actives'],
            'arrieres_total' => $dashboardStats['paiements']['arrieres_total'],
            'presences_mois' => $dashboardStats['presences']['presents'],
            'absences_mois' => $dashboardStats['presences']['absents'],
            'taux_presence' => $dashboardStats['presences']['taux'],
            'encaissements_mois' => $dashboardStats['paiements']['encaissements_mois'],
        ];

        // Tendances
        $tendances = $this->statistiqueService->getTendances();

        // Derniers athlètes inscrits
        $derniersAthletes = Athlete::with('disciplines')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Paiements récents
        $paiementsRecents = Paiement::with('athlete')
            ->payes()
            ->orderBy('date_paiement', 'desc')
            ->take(5)
            ->get();

        // Athlètes avec arriérés
        $athletesArrieres = Athlete::avecArrieres()
            ->with(['paiements' => fn($q) => $q->arrieres()])
            ->take(5)
            ->get();

        // Données pour les graphiques
        $graphiques = $this->statistiqueService->getDonneesGraphiques();

        // Statistiques de performance
        $statsPerformance = [
            'matchs' => [
                'total' => Performance::where('contexte', 'match')->count(),
                'victoires' => Performance::where('contexte', 'match')->where('resultat_match', 'victoire')->count(),
                'defaites' => Performance::where('contexte', 'match')->where('resultat_match', 'defaite')->count(),
                'nuls' => Performance::where('contexte', 'match')->where('resultat_match', 'nul')->count(),
            ],
            'competitions' => [
                'total' => Performance::where('contexte', 'competition')->count(),
                'medailles_or' => Performance::where('medaille', 'or')->count(),
                'medailles_argent' => Performance::where('medaille', 'argent')->count(),
                'medailles_bronze' => Performance::where('medaille', 'bronze')->count(),
            ],
            'note_moyenne' => round(Performance::avg('note_globale') ?? 0, 1),
        ];
        
        $statsPerformance['matchs']['taux_victoire'] = $statsPerformance['matchs']['total'] > 0 
            ? round(($statsPerformance['matchs']['victoires'] / $statsPerformance['matchs']['total']) * 100, 1) 
            : 0;

        // Dernières performances
        $dernieresPerformances = Performance::with(['athlete', 'discipline'])
            ->orderBy('date_evaluation', 'desc')
            ->take(5)
            ->get();

        // Statistiques des rencontres/matchs
        $statsRencontres = [
            'total' => Rencontre::count(),
            'a_venir' => Rencontre::where('date_match', '>=', now()->toDateString())->count(),
            'victoires' => Rencontre::where('resultat', 'victoire')->count(),
            'defaites' => Rencontre::where('resultat', 'defaite')->count(),
            'nuls' => Rencontre::where('resultat', 'nul')->count(),
        ];
        $statsRencontres['taux_victoire'] = $statsRencontres['total'] > 0 
            ? round(($statsRencontres['victoires'] / $statsRencontres['total']) * 100, 1) 
            : 0;

        // Prochaines rencontres
        $prochainesRencontres = Rencontre::with('discipline')
            ->where('date_match', '>=', now()->toDateString())
            ->orderBy('date_match')
            ->take(5)
            ->get();

        // Dernières rencontres jouées
        $dernieresRencontres = Rencontre::with('discipline')
            ->where('date_match', '<', now()->toDateString())
            ->orderBy('date_match', 'desc')
            ->take(5)
            ->get();

        // Statistiques des activités (publiées uniquement)
        $statsActivites = [
            'total' => Activity::where('publie', true)->count(),
            'a_venir' => Activity::where('publie', true)->where('debut', '>=', now()->startOfDay())->count(),
            'ce_mois' => Activity::where('publie', true)->whereMonth('debut', now()->month)->whereYear('debut', now()->year)->count(),
        ];

        // Prochaines activités (publiées uniquement)
        $prochainesActivites = Activity::where('publie', true)
            ->where('debut', '>=', now()->startOfDay())
            ->orderBy('debut')
            ->take(5)
            ->get();

        // Statistiques combats Taekwondo
        $statsCombats = [
            'total' => CombatTaekwondo::count(),
            'victoires_rouge' => CombatTaekwondo::where('vainqueur', 'rouge')->count(),
            'victoires_bleu' => CombatTaekwondo::where('vainqueur', 'bleu')->count(),
            'en_cours' => CombatTaekwondo::where('statut', 'en_cours')->count(),
        ];

        return view('dashboard', compact(
            'stats',
            'tendances',
            'derniersAthletes',
            'paiementsRecents',
            'athletesArrieres',
            'graphiques',
            'statsPerformance',
            'dernieresPerformances',
            'statsRencontres',
            'prochainesRencontres',
            'dernieresRencontres',
            'statsActivites',
            'prochainesActivites',
            'statsCombats'
        ));
    }

    /**
     * Affiche le rapport mensuel
     */
    public function rapportMensuel(Request $request): View
    {
        $mois = $request->mois ?? now()->month;
        $annee = $request->annee ?? now()->year;

        $rapport = $this->statistiqueService->genererRapportMensuel($mois, $annee);

        return view('rapports.mensuel', compact('rapport', 'mois', 'annee'));
    }
}

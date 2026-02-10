<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Performance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    /**
     * Affiche la liste des performances (Web ou API)
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Performance::with(['athlete', 'discipline']);

        if ($request->filled('search')) {
            $query->whereHas('athlete', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                    ->orWhere('prenom', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('discipline') || $request->filled('discipline_id')) {
            $query->where('discipline_id', $request->discipline ?? $request->discipline_id);
        }

        if ($request->filled('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        if ($request->filled('type') || $request->filled('contexte')) {
            $query->where('contexte', $request->type ?? $request->contexte);
        }

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $performances = $query->orderBy('date_evaluation', 'desc')->paginate($request->per_page ?? 20);
            return response()->json([
                'data' => $performances->items(),
                'meta' => [
                    'current_page' => $performances->currentPage(),
                    'last_page' => $performances->lastPage(),
                    'total' => $performances->total(),
                ],
            ]);
        }

        $performances = $query->orderBy('date_evaluation', 'desc')
            ->paginate(20)
            ->withQueryString();

        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        return view('performances.index', compact('performances', 'disciplines'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Request $request): View
    {
        $athletes = Athlete::where('actif', true)->orderBy('nom')->get();
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        $athleteId = $request->athlete;
        $disciplineId = $request->discipline;

        return view('performances.create', compact('athletes', 'disciplines', 'athleteId', 'disciplineId'));
    }

    /**
     * Enregistre une nouvelle performance
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'date_evaluation' => 'required|date',
            'type_evaluation' => 'nullable|string|max:255',
            'contexte' => 'required|in:entrainement,match,competition,test_physique',
            'resultat_match' => 'nullable|in:victoire,defaite,nul',
            'points_marques' => 'nullable|integer|min:0',
            'points_encaisses' => 'nullable|integer|min:0',
            'score' => 'nullable|numeric',
            'unite' => 'nullable|string|max:50',
            'observations' => 'nullable|string|max:1000',
            'competition' => 'nullable|string|max:255',
            'adversaire' => 'nullable|string|max:255',
            'lieu' => 'nullable|string|max:255',
            'classement' => 'nullable|integer|min:1',
            'medaille' => 'nullable|in:or,argent,bronze',
            'note_physique' => 'nullable|integer|min:1|max:20',
            'note_technique' => 'nullable|integer|min:1|max:20',
            'note_comportement' => 'nullable|integer|min:1|max:20',
        ]);

        // Calculer la note globale
        $notes = array_filter([
            $validated['note_physique'] ?? null,
            $validated['note_technique'] ?? null,
            $validated['note_comportement'] ?? null,
        ]);
        
        if (!empty($notes)) {
            $validated['note_globale'] = round(array_sum($notes) / count($notes), 1);
        }

        $performance = Performance::create($validated);

        // Réponse API JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Performance enregistrée avec succès',
                'data' => $performance->load(['athlete', 'discipline']),
            ], 201);
        }

        return redirect()->route('performances.show', $performance)
            ->with('success', 'Performance enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une performance (Web ou API)
     */
    public function show(Request $request, Performance $performance): View|JsonResponse
    {
        $performance->load(['athlete', 'discipline']);

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['data' => $performance]);
        }

        return view('performances.show', compact('performance'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Performance $performance): View
    {
        $athletes = Athlete::where('actif', true)->orderBy('nom')->get();
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        return view('performances.edit', compact('performance', 'athletes', 'disciplines'));
    }

    /**
     * Met à jour une performance
     */
    public function update(Request $request, Performance $performance): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'date_evaluation' => 'required|date',
            'type_evaluation' => 'nullable|string|max:255',
            'contexte' => 'required|in:entrainement,match,competition,test_physique',
            'resultat_match' => 'nullable|in:victoire,defaite,nul',
            'points_marques' => 'nullable|integer|min:0',
            'points_encaisses' => 'nullable|integer|min:0',
            'score' => 'nullable|numeric',
            'unite' => 'nullable|string|max:50',
            'observations' => 'nullable|string|max:1000',
            'competition' => 'nullable|string|max:255',
            'adversaire' => 'nullable|string|max:255',
            'lieu' => 'nullable|string|max:255',
            'classement' => 'nullable|integer|min:1',
            'medaille' => 'nullable|in:or,argent,bronze',
            'note_physique' => 'nullable|integer|min:1|max:10',
            'note_technique' => 'nullable|integer|min:1|max:10',
            'note_comportement' => 'nullable|integer|min:1|max:10',
        ]);

        // Calculer la note globale
        $notes = array_filter([
            $validated['note_physique'] ?? null,
            $validated['note_technique'] ?? null,
            $validated['note_comportement'] ?? null,
        ]);
        
        if (!empty($notes)) {
            $validated['note_globale'] = round(array_sum($notes) / count($notes), 1);
        }

        $performance->update($validated);

        return redirect()->route('performances.show', $performance)
            ->with('success', 'Performance mise à jour avec succès.');
    }

    /**
     * Supprime une performance (Web ou API)
     */
    public function destroy(Request $request, Performance $performance): RedirectResponse|JsonResponse
    {
        $performance->delete();

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['message' => 'Performance supprimée avec succès']);
        }

        return redirect()->route('performances.index')
            ->with('success', 'Performance supprimée avec succès.');
    }

    /**
     * Affiche l'évolution des performances d'un athlète (Web ou API)
     */
    public function evolutionAthlete(Athlete $athlete, Request $request): View|JsonResponse
    {
        $disciplineId = $request->discipline;

        $query = $athlete->performances()->with('discipline');

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        $performances = $query->orderBy('date_evaluation')->get();

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $evolution = $performances->groupBy(fn($p) => $p->date_evaluation->format('Y-m'))
                ->map(fn($group) => [
                    'periode' => $group->first()->date_evaluation->format('Y-m'),
                    'moyenne' => round($group->avg('score') ?? 0, 1),
                    'count' => $group->count(),
                ])
                ->values();

            return response()->json([
                'athlete' => [
                    'id' => $athlete->id,
                    'nom_complet' => $athlete->nom_complet,
                ],
                'evolution' => $evolution,
                'total_evaluations' => $performances->count(),
                'moyenne_globale' => round($performances->avg('score') ?? 0, 1),
            ]);
        }

        $disciplines = $athlete->disciplines;

        return view('performances.evolution', compact('athlete', 'performances', 'disciplines', 'disciplineId'));
    }

    /**
     * Tableau de bord des performances (Web ou API)
     */
    public function dashboard(Request $request): View|JsonResponse
    {
        // Si c'est une requête API, retourner JSON simplifié
        if ($request->is('api/*') || $request->expectsJson()) {
            $performances = Performance::with(['athlete', 'discipline'])
                ->orderBy('date_evaluation', 'desc')
                ->limit(10)
                ->get();

            $stats = [
                'total_evaluations' => Performance::count(),
                'moyenne_generale' => round(Performance::avg('score') ?? 0, 1),
                'medailles' => [
                    'or' => Performance::where('medaille', 'or')->count(),
                    'argent' => Performance::where('medaille', 'argent')->count(),
                    'bronze' => Performance::where('medaille', 'bronze')->count(),
                ],
            ];

            return response()->json([
                'stats' => $stats,
                'recentes' => $performances,
            ]);
        }

        $disciplineId = $request->discipline;
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        // Statistiques globales ou par discipline
        if ($disciplineId) {
            $discipline = Discipline::findOrFail($disciplineId);
            $statsEquipe = Performance::statistiquesDiscipline($disciplineId);
            
            // Dernières performances de la discipline
            $dernieresPerformances = Performance::with(['athlete', 'discipline'])
                ->where('discipline_id', $disciplineId)
                ->orderBy('date_evaluation', 'desc')
                ->take(10)
                ->get();
            
            // Top athlètes de la discipline
            $athletesDiscipline = Athlete::whereHas('disciplines', fn($q) => $q->where('discipline_id', $disciplineId))
                ->where('actif', true)
                ->get()
                ->map(function ($athlete) use ($disciplineId) {
                    $stats = Performance::statistiquesAthlete($athlete->id, $disciplineId);
                    return [
                        'athlete' => $athlete,
                        'stats' => $stats,
                    ];
                })
                ->sortByDesc(fn($item) => $item['stats']['matchs']['victoires'])
                ->take(10);
        } else {
            $discipline = null;
            $statsEquipe = [
                'total_performances' => Performance::count(),
                'nb_athletes' => Performance::distinct('athlete_id')->count('athlete_id'),
                'matchs' => [
                    'total' => Performance::where('contexte', 'match')->count(),
                    'victoires' => Performance::where('contexte', 'match')->where('resultat_match', 'victoire')->count(),
                    'defaites' => Performance::where('contexte', 'match')->where('resultat_match', 'defaite')->count(),
                    'nuls' => Performance::where('contexte', 'match')->where('resultat_match', 'nul')->count(),
                    'taux_victoire' => 0,
                    'points_marques' => Performance::where('contexte', 'match')->sum('points_marques'),
                    'points_encaisses' => Performance::where('contexte', 'match')->sum('points_encaisses'),
                ],
                'competitions' => [
                    'total' => Performance::where('contexte', 'competition')->count(),
                    'medailles_or' => Performance::where('medaille', 'or')->count(),
                    'medailles_argent' => Performance::where('medaille', 'argent')->count(),
                    'medailles_bronze' => Performance::where('medaille', 'bronze')->count(),
                    'total_medailles' => Performance::whereNotNull('medaille')->count(),
                ],
                'notes' => [
                    'moyenne_globale' => round(Performance::avg('note_globale') ?? 0, 1),
                ],
            ];
            
            if ($statsEquipe['matchs']['total'] > 0) {
                $statsEquipe['matchs']['taux_victoire'] = round(($statsEquipe['matchs']['victoires'] / $statsEquipe['matchs']['total']) * 100, 1);
            }
            
            $dernieresPerformances = Performance::with(['athlete', 'discipline'])
                ->orderBy('date_evaluation', 'desc')
                ->take(10)
                ->get();
            
            $athletesDiscipline = collect();
        }

        return view('performances.dashboard', compact(
            'disciplines',
            'disciplineId',
            'discipline',
            'statsEquipe',
            'dernieresPerformances',
            'athletesDiscipline'
        ));
    }
}

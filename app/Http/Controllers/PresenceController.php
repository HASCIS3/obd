<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Presence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class PresenceController extends Controller
{
    /**
     * Affiche la liste des présences (Web ou API)
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Presence::with(['athlete', 'discipline', 'coach.user']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } elseif (!$request->is('api/*') && !$request->expectsJson()) {
            $query->whereDate('date', now());
        }

        if ($request->filled('discipline') || $request->filled('discipline_id')) {
            $query->where('discipline_id', $request->discipline ?? $request->discipline_id);
        }

        if ($request->filled('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        if ($request->filled('present')) {
            $query->where('present', $request->present === '1' || $request->present === true);
        }

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $presences = $query->orderBy('date', 'desc')->paginate($request->per_page ?? 50);
            return response()->json([
                'data' => $presences->items(),
                'meta' => [
                    'current_page' => $presences->currentPage(),
                    'last_page' => $presences->lastPage(),
                    'total' => $presences->total(),
                ],
            ]);
        }

        $presences = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        return view('presences.index', compact('presences', 'disciplines'));
    }

    /**
     * Affiche le formulaire de saisie des présences
     */
    public function create(Request $request): View
    {
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        $date = $request->date ?? now()->format('Y-m-d');
        $disciplineId = $request->discipline;

        $athletes = collect();
        $existingPresences = collect();

        if ($disciplineId) {
            $athletes = Athlete::whereHas('disciplines', function ($q) use ($disciplineId) {
                $q->where('disciplines.id', $disciplineId)
                    ->where('athlete_discipline.actif', true);
            })->where('actif', true)->orderBy('nom')->get();

            $existingPresences = Presence::where('discipline_id', $disciplineId)
                ->whereDate('date', $date)
                ->pluck('present', 'athlete_id');
        }

        return view('presences.create', compact('disciplines', 'athletes', 'date', 'disciplineId', 'existingPresences'));
    }

    /**
     * Enregistre les présences (Web ou API)
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $isApi = $request->is('api/*') || $request->expectsJson();

        // Validation pour API (présence unique) ou Web (présences multiples)
        if ($isApi && !$request->has('presences')) {
            $validated = $request->validate([
                'athlete_id' => 'required|exists:athletes,id',
                'discipline_id' => 'required|exists:disciplines,id',
                'date' => 'required|date',
                'present' => 'required|boolean',
                'notes' => 'nullable|string',
            ]);

            $presence = Presence::updateOrCreate(
                [
                    'athlete_id' => $validated['athlete_id'],
                    'discipline_id' => $validated['discipline_id'],
                    'date' => $validated['date'],
                ],
                [
                    'present' => $validated['present'],
                    'remarque' => $validated['notes'] ?? null,
                ]
            );

            return response()->json([
                'message' => 'Présence enregistrée',
                'data' => $presence->load(['athlete', 'discipline']),
            ], 201);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'discipline_id' => 'required|exists:disciplines,id',
            'presences' => 'required|array',
            'presences.*.athlete_id' => 'required|exists:athletes,id',
            'presences.*.present' => 'required|boolean',
            'presences.*.remarque' => 'nullable|string|max:500',
        ]);

        $coachId = null;
        if (auth()->user()->isCoach() && auth()->user()->coach) {
            $coachId = auth()->user()->coach->id;
        }

        $results = [];
        foreach ($validated['presences'] as $presenceData) {
            $presence = Presence::updateOrCreate(
                [
                    'athlete_id' => $presenceData['athlete_id'],
                    'discipline_id' => $validated['discipline_id'],
                    'date' => $validated['date'],
                ],
                [
                    'coach_id' => $coachId,
                    'present' => $presenceData['present'],
                    'remarque' => $presenceData['remarque'] ?? null,
                ]
            );
            $results[] = $presence;
        }

        if ($isApi) {
            return response()->json([
                'message' => count($results) . ' présences enregistrées',
                'data' => $results,
            ]);
        }

        return redirect()->route('presences.index', [
            'date' => $validated['date'],
            'discipline' => $validated['discipline_id'],
        ])->with('success', 'Présences enregistrées avec succès.');
    }

    /**
     * Affiche une présence (API)
     */
    public function show(Request $request, Presence $presence): JsonResponse
    {
        return response()->json([
            'data' => $presence->load(['athlete', 'discipline']),
        ]);
    }

    /**
     * Met à jour une présence (API)
     */
    public function update(Request $request, Presence $presence): JsonResponse
    {
        $validated = $request->validate([
            'present' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $presence->update($validated);

        return response()->json([
            'message' => 'Présence mise à jour',
            'data' => $presence->fresh(['athlete', 'discipline']),
        ]);
    }

    /**
     * Supprime une présence (API)
     */
    public function destroy(Request $request, Presence $presence): JsonResponse
    {
        $presence->delete();

        return response()->json([
            'message' => 'Présence supprimée',
        ]);
    }

    /**
     * Affiche les statistiques de présence d'un athlète
     */
    public function athleteStats(Athlete $athlete): View
    {
        $presences = $athlete->presences()
            ->with('discipline')
            ->orderBy('date', 'desc')
            ->paginate(30);

        $stats = [
            'total' => $athlete->presences()->count(),
            'presents' => $athlete->presences()->where('present', true)->count(),
            'absents' => $athlete->presences()->where('present', false)->count(),
        ];

        $stats['taux'] = $stats['total'] > 0 
            ? round(($stats['presents'] / $stats['total']) * 100, 1) 
            : 0;

        return view('presences.athlete-stats', compact('athlete', 'presences', 'stats'));
    }

    /**
     * Rapport mensuel des présences (Web ou API)
     */
    public function rapportMensuel(Request $request): View|JsonResponse
    {
        $mois = $request->mois ?? now()->month;
        $annee = $request->annee ?? now()->year;

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $startDate = Carbon::create($annee, $mois, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $presences = Presence::with(['athlete', 'discipline'])
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $stats = [
                'total_seances' => $presences->groupBy('date')->count(),
                'total_presences' => $presences->where('present', true)->count(),
                'total_absences' => $presences->where('present', false)->count(),
                'taux_presence' => $presences->count() > 0 
                    ? round($presences->where('present', true)->count() / $presences->count() * 100, 1)
                    : 0,
            ];

            return response()->json([
                'mois' => $mois,
                'annee' => $annee,
                'stats' => $stats,
            ]);
        }

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
                'total' => $total,
                'presents' => $presents,
                'absents' => $total - $presents,
                'taux' => $total > 0 ? round(($presents / $total) * 100, 1) : 0,
            ];
        }

        return view('presences.rapport-mensuel', compact('stats', 'mois', 'annee'));
    }
}

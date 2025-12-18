<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisciplineController extends Controller
{
    /**
     * Affiche la liste des disciplines (Web ou API)
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Discipline::withCount(['athletes', 'coachs']);

        if ($request->filled('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        if ($request->filled('actif')) {
            $actif = $request->actif === '1' || $request->actif === true;
            $query->where('actif', $actif);
        }

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $disciplines = $query->orderBy('nom')->get();
            return response()->json([
                'data' => $disciplines->map(fn($d) => $this->formatDiscipline($d)),
            ]);
        }

        $disciplines = $query->orderBy('nom')->paginate(15)->withQueryString();

        return view('disciplines.index', compact('disciplines'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        return view('disciplines.create');
    }

    /**
     * Enregistre une nouvelle discipline
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:disciplines,nom',
            'description' => 'nullable|string|max:1000',
            'tarif_mensuel' => 'required|numeric|min:0',
        ]);

        $validated['actif'] = true;

        $discipline = Discipline::create($validated);

        return redirect()->route('disciplines.show', $discipline)
            ->with('success', 'Discipline créée avec succès.');
    }

    /**
     * Affiche les détails d'une discipline (Web ou API)
     */
    public function show(Request $request, Discipline $discipline): View|JsonResponse
    {
        $discipline->load(['athletes', 'coachs.user']);
        $discipline->loadCount('athletes');
        
        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'data' => $this->formatDiscipline($discipline),
            ]);
        }
        
        $stats = [
            'total_athletes' => $discipline->athletes()->where('athlete_discipline.actif', true)->count(),
            'total_coachs' => $discipline->coachs()->count(),
            'presences_mois' => $discipline->presences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', true)
                ->count(),
        ];

        return view('disciplines.show', compact('discipline', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Discipline $discipline): View
    {
        return view('disciplines.edit', compact('discipline'));
    }

    /**
     * Met à jour une discipline
     */
    public function update(Request $request, Discipline $discipline): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:disciplines,nom,' . $discipline->id,
            'description' => 'nullable|string|max:1000',
            'tarif_mensuel' => 'required|numeric|min:0',
            'actif' => 'boolean',
        ]);

        $validated['actif'] = $request->boolean('actif', true);

        $discipline->update($validated);

        return redirect()->route('disciplines.show', $discipline)
            ->with('success', 'Discipline mise à jour avec succès.');
    }

    /**
     * Supprime une discipline
     */
    public function destroy(Discipline $discipline): RedirectResponse
    {
        // Vérifier s'il y a des athlètes inscrits
        if ($discipline->athletes()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette discipline car des athlètes y sont inscrits.');
        }

        $discipline->delete();

        return redirect()->route('disciplines.index')
            ->with('success', 'Discipline supprimée avec succès.');
    }

    /**
     * Formater une discipline pour l'API
     */
    private function formatDiscipline(Discipline $discipline): array
    {
        return [
            'id' => $discipline->id,
            'nom' => $discipline->nom,
            'description' => $discipline->description,
            'tarif_mensuel' => $discipline->tarif_mensuel,
            'actif' => $discipline->actif,
            'athletes_count' => $discipline->athletes_count ?? 0,
            'coachs_count' => $discipline->coachs_count ?? 0,
            'created_at' => $discipline->created_at?->toISOString(),
            'updated_at' => $discipline->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AthleteController extends Controller
{
    /**
     * Affiche la liste des athlètes (Web ou API)
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Athlete::with('disciplines');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('discipline') || $request->filled('discipline_id')) {
            $disciplineId = $request->discipline ?? $request->discipline_id;
            $query->whereHas('disciplines', function ($q) use ($disciplineId) {
                $q->where('disciplines.id', $disciplineId);
            });
        }

        if ($request->filled('actif')) {
            $actif = $request->actif === '1' || $request->actif === true;
            $query->where('actif', $actif);
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $athletes = $query->orderBy('nom')->paginate($request->per_page ?? 20);
            return response()->json([
                'data' => $athletes->map(fn($a) => $this->formatAthlete($a)),
                'meta' => [
                    'current_page' => $athletes->currentPage(),
                    'last_page' => $athletes->lastPage(),
                    'per_page' => $athletes->perPage(),
                    'total' => $athletes->total(),
                ],
            ]);
        }

        $athletes = $query->orderBy('nom')->paginate(15)->withQueryString();
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        return view('athletes.index', compact('athletes', 'disciplines'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        return view('athletes.create', compact('disciplines'));
    }

    /**
     * Enregistre un nouvel athlète (Web ou API)
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => 'required|in:M,F',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
            'nom_tuteur' => 'nullable|string|max:255',
            'telephone_tuteur' => 'nullable|string|max:20',
            'date_inscription' => 'nullable|date',
            'disciplines' => 'nullable|array',
            'disciplines.*' => 'exists:disciplines,id',
            'discipline_ids' => 'nullable|array',
            'discipline_ids.*' => 'exists:disciplines,id',
        ]);

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('athletes', 'public');
        }

        $validated['date_inscription'] = $validated['date_inscription'] ?? now();
        $validated['actif'] = true;

        $athlete = Athlete::create($validated);

        // Attacher les disciplines (supporte les deux formats)
        $disciplineIds = $validated['disciplines'] ?? $validated['discipline_ids'] ?? [];
        if (!empty($disciplineIds)) {
            $disciplines = collect($disciplineIds)->mapWithKeys(fn($id) => [
                $id => ['date_inscription' => now(), 'actif' => true]
            ]);
            $athlete->disciplines()->attach($disciplines);
        }

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Athlète créé avec succès',
                'data' => $this->formatAthlete($athlete->fresh(['disciplines'])),
            ], 201);
        }

        return redirect()->route('athletes.show', $athlete)
            ->with('success', 'Athlète créé avec succès.');
    }

    /**
     * Affiche les détails d'un athlète (Web ou API)
     */
    public function show(Request $request, Athlete $athlete): View|JsonResponse
    {
        $athlete->load([
            'disciplines',
            'presences' => fn($q) => $q->orderBy('date', 'desc')->take(10),
            'paiements' => fn($q) => $q->orderBy('annee', 'desc')->orderBy('mois', 'desc'),
            'suiviScolaire',
            'performances' => fn($q) => $q->orderBy('date_evaluation', 'desc')->take(10),
        ]);

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'data' => $this->formatAthlete($athlete),
            ]);
        }

        return view('athletes.show', compact('athlete'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Athlete $athlete): View
    {
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        $athlete->load('disciplines');
        
        return view('athletes.edit', compact('athlete', 'disciplines'));
    }

    /**
     * Met à jour un athlète (Web ou API)
     */
    public function update(Request $request, Athlete $athlete): RedirectResponse|JsonResponse
    {
        $isApi = $request->is('api/*') || $request->expectsJson();
        
        $validated = $request->validate([
            'nom' => $isApi ? 'sometimes|string|max:255' : 'required|string|max:255',
            'prenom' => $isApi ? 'sometimes|string|max:255' : 'required|string|max:255',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => $isApi ? 'sometimes|in:M,F' : 'required|in:M,F',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
            'nom_tuteur' => 'nullable|string|max:255',
            'telephone_tuteur' => 'nullable|string|max:20',
            'actif' => 'boolean',
            'disciplines' => 'nullable|array',
            'disciplines.*' => 'exists:disciplines,id',
            'discipline_ids' => 'nullable|array',
            'discipline_ids.*' => 'exists:disciplines,id',
        ]);

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('athletes', 'public');
        }

        if ($request->has('actif')) {
            $validated['actif'] = $request->boolean('actif', true);
        }

        $athlete->update($validated);

        // Synchroniser les disciplines (supporte les deux formats)
        $disciplineIds = $validated['disciplines'] ?? $validated['discipline_ids'] ?? null;
        if ($disciplineIds !== null) {
            $disciplines = collect($disciplineIds)->mapWithKeys(fn($id) => [
                $id => ['date_inscription' => now(), 'actif' => true]
            ]);
            $athlete->disciplines()->sync($disciplines);
        }

        // Si c'est une requête API, retourner JSON
        if ($isApi) {
            return response()->json([
                'message' => 'Athlète mis à jour avec succès',
                'data' => $this->formatAthlete($athlete->fresh(['disciplines'])),
            ]);
        }

        return redirect()->route('athletes.show', $athlete)
            ->with('success', 'Athlète mis à jour avec succès.');
    }

    /**
     * Supprime un athlète (Web ou API)
     */
    public function destroy(Request $request, Athlete $athlete): RedirectResponse|JsonResponse
    {
        $athlete->delete();

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'message' => 'Athlète supprimé avec succès',
            ]);
        }

        return redirect()->route('athletes.index')
            ->with('success', 'Athlète supprimé avec succès.');
    }

    public function createAccount(Athlete $athlete): View
    {
        if ($athlete->user) {
            abort(403, 'Ce compte existe déjà.');
        }

        return view('athletes.create-account', compact('athlete'));
    }

    public function storeAccount(Request $request, Athlete $athlete): RedirectResponse
    {
        if ($athlete->user) {
            abort(403, 'Ce compte existe déjà.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'athlete_id' => $athlete->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_COACH,
            'email_verified_at' => now(),
        ]);

        if (empty($athlete->email)) {
            $athlete->update(['email' => $validated['email']]);
        }

        return redirect()->route('athletes.show', $athlete)
            ->with('success', 'Compte athlète créé avec succès.');
    }

    /**
     * Présences d'un athlète (API)
     */
    public function presences(Athlete $athlete, Request $request): JsonResponse
    {
        $presences = $athlete->presences()
            ->with('discipline')
            ->orderBy('date', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $presences->items(),
            'meta' => [
                'current_page' => $presences->currentPage(),
                'last_page' => $presences->lastPage(),
                'total' => $presences->total(),
            ],
        ]);
    }

    /**
     * Paiements d'un athlète (API)
     */
    public function paiements(Athlete $athlete, Request $request): JsonResponse
    {
        $paiements = $athlete->paiements()
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $paiements->items(),
            'meta' => [
                'current_page' => $paiements->currentPage(),
                'last_page' => $paiements->lastPage(),
                'total' => $paiements->total(),
            ],
        ]);
    }

    /**
     * Performances d'un athlète (API)
     */
    public function performances(Athlete $athlete, Request $request): JsonResponse
    {
        $performances = $athlete->performances()
            ->with('discipline')
            ->orderBy('date_evaluation', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $performances->items(),
            'meta' => [
                'current_page' => $performances->currentPage(),
                'last_page' => $performances->lastPage(),
                'total' => $performances->total(),
            ],
        ]);
    }

    /**
     * Formater un athlète pour l'API
     */
    private function formatAthlete(Athlete $athlete): array
    {
        return [
            'id' => $athlete->id,
            'nom' => $athlete->nom,
            'prenom' => $athlete->prenom,
            'nom_complet' => $athlete->nom_complet,
            'date_naissance' => $athlete->date_naissance?->toDateString(),
            'age' => $athlete->age,
            'sexe' => $athlete->sexe,
            'categorie' => $athlete->categorie_age,
            'telephone' => $athlete->telephone,
            'email' => $athlete->email,
            'adresse' => $athlete->adresse,
            'photo' => $athlete->photo_url,
            'actif' => $athlete->actif,
            'nom_tuteur' => $athlete->nom_tuteur,
            'telephone_tuteur' => $athlete->telephone_tuteur,
            'disciplines' => $athlete->disciplines->map(fn($d) => [
                'id' => $d->id,
                'nom' => $d->nom,
                'tarif_mensuel' => $d->tarif_mensuel,
            ]),
            'est_a_jour_paiements' => $athlete->estAJourPaiements(),
            'taux_presence' => $athlete->taux_presence,
            'created_at' => $athlete->created_at?->toISOString(),
            'updated_at' => $athlete->updated_at?->toISOString(),
        ];
    }
}

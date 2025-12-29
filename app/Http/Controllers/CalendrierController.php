<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use App\Models\Evenement;
use App\Models\Rencontre;
use Illuminate\Http\Request;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        $disciplines = Discipline::orderBy('nom')->get();
        
        return view('calendrier.index', compact('disciplines'));
    }

    public function events(Request $request)
    {
        $events = collect();

        // Événements classiques
        $queryEvenements = Evenement::with('discipline');

        if ($request->filled('start') && $request->filled('end')) {
            $queryEvenements->entreDates($request->start, $request->end);
        }

        if ($request->filled('type') && $request->type !== 'match') {
            $queryEvenements->deType($request->type);
        }

        if ($request->filled('discipline_id')) {
            $queryEvenements->deDiscipline($request->discipline_id);
        }

        if (!$request->filled('type') || $request->type !== 'match') {
            $evenements = $queryEvenements->orderBy('date_debut')->get();
            $events = $events->merge($evenements->map(fn($e) => $e->toFullCalendarEvent()));
        }

        // Rencontres sportives (matchs)
        if (!$request->filled('type') || $request->type === 'match' || $request->type === 'competition') {
            $queryRencontres = Rencontre::with('discipline');

            if ($request->filled('start') && $request->filled('end')) {
                $queryRencontres->whereBetween('date_match', [$request->start, $request->end]);
            }

            if ($request->filled('discipline_id')) {
                $queryRencontres->where('discipline_id', $request->discipline_id);
            }

            $rencontres = $queryRencontres->orderBy('date_match')->get();
            
            $events = $events->merge($rencontres->map(function ($r) {
                $couleur = match($r->resultat) {
                    'victoire' => '#16a34a', // vert
                    'defaite' => '#dc2626',  // rouge
                    'nul' => '#f59e0b',      // orange
                    default => '#3b82f6',    // bleu (à jouer)
                };
                
                return [
                    'id' => 'match_' . $r->id,
                    'title' => 'Match: OBD vs ' . $r->adversaire,
                    'start' => $r->date_match->format('Y-m-d') . ($r->heure_match ? 'T' . $r->heure_match : ''),
                    'end' => $r->date_match->format('Y-m-d') . ($r->heure_match ? 'T' . $r->heure_match : ''),
                    'allDay' => !$r->heure_match,
                    'backgroundColor' => $couleur,
                    'borderColor' => $couleur,
                    'url' => route('rencontres.show', $r),
                    'extendedProps' => [
                        'type' => 'match',
                        'discipline' => $r->discipline?->nom,
                        'lieu' => $r->lieu,
                        'resultat' => $r->resultat,
                        'score' => $r->score_formate,
                        'type_match' => $r->type_match,
                    ],
                ];
            }));
        }

        return response()->json($events->values());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:entrainement,competition,reunion,stage,autre',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
            'lieu' => 'nullable|string|max:150',
            'couleur' => 'nullable|string|max:7',
            'toute_journee' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['couleur'] = $validated['couleur'] ?? Evenement::COULEURS[$validated['type']] ?? '#14532d';

        $evenement = Evenement::create($validated);

        if ($request->wantsJson()) {
            return response()->json($evenement->toFullCalendarEvent(), 201);
        }

        return redirect()->route('calendrier.index')
            ->with('success', 'Événement créé avec succès.');
    }

    public function show(Evenement $evenement)
    {
        $evenement->load('discipline', 'createur');
        
        if (request()->wantsJson()) {
            return response()->json($evenement);
        }

        return view('calendrier.show', compact('evenement'));
    }

    public function update(Request $request, Evenement $evenement)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:entrainement,competition,reunion,stage,autre',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
            'lieu' => 'nullable|string|max:150',
            'couleur' => 'nullable|string|max:7',
            'toute_journee' => 'boolean',
        ]);

        $evenement->update($validated);

        if ($request->wantsJson()) {
            return response()->json($evenement->toFullCalendarEvent());
        }

        return redirect()->route('calendrier.index')
            ->with('success', 'Événement mis à jour.');
    }

    public function destroy(Evenement $evenement)
    {
        $evenement->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Événement supprimé']);
        }

        return redirect()->route('calendrier.index')
            ->with('success', 'Événement supprimé.');
    }

    public function aVenir()
    {
        // Événements à venir
        $evenements = Evenement::with('discipline')
            ->aVenir()
            ->orderBy('date_debut')
            ->take(10)
            ->get();

        // Matchs à venir
        $matchsAVenir = Rencontre::with('discipline')
            ->where('date_match', '>=', now()->startOfDay())
            ->orderBy('date_match')
            ->take(10)
            ->get();

        return view('calendrier.a-venir', compact('evenements', 'matchsAVenir'));
    }
}

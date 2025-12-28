<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use App\Models\Evenement;
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
        $query = Evenement::with('discipline');

        if ($request->filled('start') && $request->filled('end')) {
            $query->entreDates($request->start, $request->end);
        }

        if ($request->filled('type')) {
            $query->deType($request->type);
        }

        if ($request->filled('discipline_id')) {
            $query->deDiscipline($request->discipline_id);
        }

        $evenements = $query->orderBy('date_debut')->get();

        return response()->json(
            $evenements->map(fn($e) => $e->toFullCalendarEvent())
        );
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
        $evenements = Evenement::with('discipline')
            ->aVenir()
            ->orderBy('date_debut')
            ->take(10)
            ->get();

        return view('calendrier.a-venir', compact('evenements'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Saison;
use Illuminate\Http\Request;

class SaisonController extends Controller
{
    public function index()
    {
        $saisons = Saison::orderBy('date_debut', 'desc')->get();
        $saisonActive = Saison::actuelle();

        return view('saisons.index', compact('saisons', 'saisonActive'));
    }

    public function create()
    {
        $anneeActuelle = now()->year;
        $anneeSuggestion = now()->month >= 9 ? $anneeActuelle : $anneeActuelle - 1;

        return view('saisons.create', compact('anneeSuggestion'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:20|unique:saisons,nom',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string',
        ]);

        $saison = Saison::create($validated);

        return redirect()->route('saisons.index')
            ->with('success', "Saison {$saison->nom} créée avec succès.");
    }

    public function show(Saison $saison)
    {
        return view('saisons.show', compact('saison'));
    }

    public function edit(Saison $saison)
    {
        return view('saisons.edit', compact('saison'));
    }

    public function update(Request $request, Saison $saison)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:20|unique:saisons,nom,' . $saison->id,
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string',
        ]);

        $saison->update($validated);

        return redirect()->route('saisons.index')
            ->with('success', 'Saison mise à jour avec succès.');
    }

    public function destroy(Saison $saison)
    {
        if ($saison->active) {
            return redirect()->route('saisons.index')
                ->with('error', 'Impossible de supprimer la saison active.');
        }

        $saison->delete();

        return redirect()->route('saisons.index')
            ->with('success', 'Saison supprimée avec succès.');
    }

    public function activer(Saison $saison)
    {
        $saison->activer();

        return redirect()->route('saisons.index')
            ->with('success', "Saison {$saison->nom} activée.");
    }

    public function archiver(Saison $saison)
    {
        $saison->archiver();

        return redirect()->route('saisons.index')
            ->with('success', "Saison {$saison->nom} archivée.");
    }
}

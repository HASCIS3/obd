<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Licence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LicenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Licence::with(['athlete', 'discipline']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('discipline_id')) {
            $query->where('discipline_id', $request->discipline_id);
        }

        if ($request->filled('saison')) {
            $query->where('saison', $request->saison);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_licence', 'like', "%{$search}%")
                    ->orWhereHas('athlete', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%");
                    });
            });
        }

        // Statistiques
        $stats = [
            'total' => Licence::count(),
            'actives' => Licence::actives()->count(),
            'expirees' => Licence::expirees()->count(),
            'expirant_bientot' => Licence::expirantBientot(30)->count(),
            'non_payees' => Licence::nonPayees()->count(),
        ];

        $licences = $query->orderBy('date_expiration', 'asc')->paginate(15);
        $disciplines = Discipline::orderBy('nom')->get();
        $saisons = Licence::distinct()->pluck('saison')->filter()->sort()->reverse();

        return view('licences.index', compact('licences', 'disciplines', 'saisons', 'stats'));
    }

    public function create()
    {
        $athletes = Athlete::actifs()->orderBy('nom')->get();
        $disciplines = Discipline::orderBy('nom')->get();
        $categories = Licence::CATEGORIES;
        $saisonActuelle = Licence::getSaisonActuelle();

        return view('licences.create', compact('athletes', 'disciplines', 'categories', 'saisonActuelle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'federation' => 'required|string|max:255',
            'type' => 'required|in:nationale,regionale,locale',
            'categorie' => 'nullable|in:' . implode(',', Licence::CATEGORIES),
            'date_emission' => 'required|date',
            'date_expiration' => 'required|date|after:date_emission',
            'saison' => 'nullable|string|max:20',
            'frais_licence' => 'required|numeric|min:0',
            'paye' => 'boolean',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        $athlete = Athlete::findOrFail($validated['athlete_id']);
        $discipline = Discipline::findOrFail($validated['discipline_id']);

        // Générer le numéro de licence
        $validated['numero_licence'] = Licence::genererNumeroLicence($athlete, $discipline);

        // Déterminer la catégorie automatiquement si non fournie
        if (empty($validated['categorie']) && $athlete->age) {
            $validated['categorie'] = Licence::getCategorieParAge($athlete->age);
        }

        // Upload du document
        if ($request->hasFile('document')) {
            $validated['document'] = $request->file('document')->store('licences', 'public');
        }

        $validated['paye'] = $request->boolean('paye');
        $validated['statut'] = Licence::STATUT_ACTIVE;

        $licence = Licence::create($validated);

        return redirect()->route('licences.show', $licence)
            ->with('success', "Licence {$licence->numero_licence} créée avec succès.");
    }

    public function show(Licence $licence)
    {
        $licence->load(['athlete', 'discipline']);
        
        // Historique des licences de l'athlète
        $historique = Licence::where('athlete_id', $licence->athlete_id)
            ->where('discipline_id', $licence->discipline_id)
            ->where('id', '!=', $licence->id)
            ->orderBy('date_emission', 'desc')
            ->get();

        return view('licences.show', compact('licence', 'historique'));
    }

    public function edit(Licence $licence)
    {
        $athletes = Athlete::actifs()->orderBy('nom')->get();
        $disciplines = Discipline::orderBy('nom')->get();
        $categories = Licence::CATEGORIES;

        return view('licences.edit', compact('licence', 'athletes', 'disciplines', 'categories'));
    }

    public function update(Request $request, Licence $licence)
    {
        $validated = $request->validate([
            'federation' => 'required|string|max:255',
            'type' => 'required|in:nationale,regionale,locale',
            'categorie' => 'nullable|in:' . implode(',', Licence::CATEGORIES),
            'date_emission' => 'required|date',
            'date_expiration' => 'required|date|after:date_emission',
            'statut' => 'required|in:active,expiree,suspendue,annulee',
            'saison' => 'nullable|string|max:20',
            'frais_licence' => 'required|numeric|min:0',
            'paye' => 'boolean',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Upload du document
        if ($request->hasFile('document')) {
            // Supprimer l'ancien document
            if ($licence->document) {
                Storage::disk('public')->delete($licence->document);
            }
            $validated['document'] = $request->file('document')->store('licences', 'public');
        }

        $validated['paye'] = $request->boolean('paye');

        $licence->update($validated);

        return redirect()->route('licences.show', $licence)
            ->with('success', 'Licence mise à jour avec succès.');
    }

    public function destroy(Licence $licence)
    {
        // Supprimer le document
        if ($licence->document) {
            Storage::disk('public')->delete($licence->document);
        }

        $licence->delete();

        return redirect()->route('licences.index')
            ->with('success', 'Licence supprimée avec succès.');
    }

    public function renouveler(Licence $licence)
    {
        $nouvelleLicence = $licence->renouveler();

        return redirect()->route('licences.show', $nouvelleLicence)
            ->with('success', "Licence renouvelée. Nouveau numéro: {$nouvelleLicence->numero_licence}");
    }

    public function expirantBientot()
    {
        $licences = Licence::with(['athlete', 'discipline'])
            ->expirantBientot(30)
            ->orderBy('date_expiration')
            ->get();

        return view('licences.expirant-bientot', compact('licences'));
    }

    public function verifierExpirations()
    {
        $count = 0;
        Licence::actives()
            ->where('date_expiration', '<', now())
            ->each(function ($licence) use (&$count) {
                $licence->update(['statut' => Licence::STATUT_EXPIREE]);
                $count++;
            });

        return redirect()->route('licences.index')
            ->with('success', "{$count} licence(s) marquée(s) comme expirée(s).");
    }
}

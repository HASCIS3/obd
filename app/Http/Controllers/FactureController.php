<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $query = Facture::with('athlete');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('athlete', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%");
                    });
            });
        }

        $stats = [
            'total' => Facture::count(),
            'emises' => Facture::emises()->count(),
            'payees' => Facture::payees()->count(),
            'impayees' => Facture::impayees()->count(),
            'en_retard' => Facture::enRetard()->count(),
            'montant_total' => Facture::impayees()->sum('montant_ttc'),
            'montant_paye' => Facture::impayees()->sum('montant_paye'),
        ];

        $factures = $query->orderBy('date_emission', 'desc')->paginate(15);

        return view('factures.index', compact('factures', 'stats'));
    }

    public function create(Request $request)
    {
        $athletes = Athlete::actifs()->orderBy('nom')->get();
        $athleteId = $request->query('athlete_id');
        $numero = Facture::genererNumero();

        return view('factures.create', compact('athletes', 'athleteId', 'numero'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_emission',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0|max:100',
            'periode' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['numero'] = Facture::genererNumero();
        $validated['tva'] = $validated['tva'] ?? 0;
        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $validated['tva'] / 100);
        $validated['statut'] = Facture::STATUT_BROUILLON;

        $facture = Facture::create($validated);

        return redirect()->route('factures.show', $facture)
            ->with('success', "Facture {$facture->numero} créée.");
    }

    public function show(Facture $facture)
    {
        $facture->load('athlete');
        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture)
    {
        if ($facture->statut !== Facture::STATUT_BROUILLON) {
            return redirect()->route('factures.show', $facture)
                ->with('error', 'Seules les factures en brouillon peuvent être modifiées.');
        }

        $athletes = Athlete::actifs()->orderBy('nom')->get();
        return view('factures.edit', compact('facture', 'athletes'));
    }

    public function update(Request $request, Facture $facture)
    {
        if ($facture->statut !== Facture::STATUT_BROUILLON) {
            return redirect()->route('factures.show', $facture)
                ->with('error', 'Seules les factures en brouillon peuvent être modifiées.');
        }

        $validated = $request->validate([
            'date_emission' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_emission',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0|max:100',
            'periode' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['tva'] = $validated['tva'] ?? 0;
        $validated['montant_ttc'] = $validated['montant_ht'] * (1 + $validated['tva'] / 100);

        $facture->update($validated);

        return redirect()->route('factures.show', $facture)
            ->with('success', 'Facture mise à jour.');
    }

    public function destroy(Facture $facture)
    {
        if (!in_array($facture->statut, [Facture::STATUT_BROUILLON, Facture::STATUT_ANNULEE])) {
            return redirect()->route('factures.index')
                ->with('error', 'Seules les factures en brouillon ou annulées peuvent être supprimées.');
        }

        $facture->delete();

        return redirect()->route('factures.index')
            ->with('success', 'Facture supprimée.');
    }

    public function emettre(Facture $facture)
    {
        $facture->emettre();

        return redirect()->route('factures.show', $facture)
            ->with('success', 'Facture émise avec succès.');
    }

    public function enregistrerPaiement(Request $request, Facture $facture)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0|max:' . $facture->reste_a_payer,
        ]);

        $facture->enregistrerPaiement($validated['montant']);

        return redirect()->route('factures.show', $facture)
            ->with('success', 'Paiement enregistré.');
    }

    public function annuler(Facture $facture)
    {
        $facture->annuler();

        return redirect()->route('factures.show', $facture)
            ->with('success', 'Facture annulée.');
    }

    public function pdf(Facture $facture)
    {
        $facture->load('athlete');

        $pdf = Pdf::loadView('factures.pdf', compact('facture'));

        return $pdf->download("facture_{$facture->numero}.pdf");
    }
}

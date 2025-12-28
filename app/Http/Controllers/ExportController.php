<?php

namespace App\Http\Controllers;

use App\Exports\AthletesExport;
use App\Exports\LicencesExport;
use App\Exports\PaiementsExport;
use App\Models\Athlete;
use App\Models\Licence;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // ==================== ATHLETES ====================

    public function athletesExcel(Request $request)
    {
        $filters = $request->only(['discipline_id', 'actif']);
        $filename = 'athletes_' . now()->format('Y-m-d_H-i') . '.xlsx';

        return Excel::download(new AthletesExport($filters), $filename);
    }

    public function athletesPdf(Request $request)
    {
        $query = Athlete::with(['disciplines', 'licences', 'certificatsMedicaux']);

        if ($request->filled('discipline_id')) {
            $query->whereHas('disciplines', fn($q) => $q->where('disciplines.id', $request->discipline_id));
        }

        if ($request->filled('actif')) {
            $query->where('actif', $request->actif);
        }

        $athletes = $query->orderBy('nom')->get();

        $pdf = Pdf::loadView('exports.athletes-pdf', compact('athletes'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('athletes_' . now()->format('Y-m-d') . '.pdf');
    }

    // ==================== LICENCES ====================

    public function licencesExcel(Request $request)
    {
        $filters = $request->only(['statut', 'discipline_id', 'saison']);
        $filename = 'licences_' . now()->format('Y-m-d_H-i') . '.xlsx';

        return Excel::download(new LicencesExport($filters), $filename);
    }

    public function licencesPdf(Request $request)
    {
        $query = Licence::with(['athlete', 'discipline']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('discipline_id')) {
            $query->where('discipline_id', $request->discipline_id);
        }

        $licences = $query->orderBy('date_expiration')->get();

        $pdf = Pdf::loadView('exports.licences-pdf', compact('licences'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('licences_' . now()->format('Y-m-d') . '.pdf');
    }

    // ==================== PAIEMENTS ====================

    public function paiementsExcel(Request $request)
    {
        $filters = $request->only(['statut', 'mois', 'annee']);
        $filename = 'paiements_' . now()->format('Y-m-d_H-i') . '.xlsx';

        return Excel::download(new PaiementsExport($filters), $filename);
    }

    public function paiementsPdf(Request $request)
    {
        $query = Paiement::with(['athlete', 'discipline']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        $paiements = $query->orderBy('date_paiement', 'desc')->get();

        $totaux = [
            'montant' => $paiements->sum('montant'),
            'paye' => $paiements->sum('montant_paye'),
            'reste' => $paiements->sum(fn($p) => $p->montant - $p->montant_paye),
        ];

        $pdf = Pdf::loadView('exports.paiements-pdf', compact('paiements', 'totaux'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('paiements_' . now()->format('Y-m-d') . '.pdf');
    }

    // ==================== FICHE ATHLETE ====================

    public function ficheAthletePdf(Athlete $athlete)
    {
        $athlete->load(['disciplines', 'licences', 'certificatsMedicaux', 'paiements', 'presences', 'performances']);

        $pdf = Pdf::loadView('exports.fiche-athlete-pdf', compact('athlete'));

        return $pdf->download('fiche_' . $athlete->nom . '_' . $athlete->prenom . '.pdf');
    }
}

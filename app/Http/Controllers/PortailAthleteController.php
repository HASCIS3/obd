<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Presence;
use App\Models\Paiement;
use App\Models\SuiviScolaire;
use App\Models\Performance;
use App\Models\Evenement;
use Illuminate\Http\Request;

class PortailAthleteController extends Controller
{
    /**
     * Dashboard athlète - Vue d'ensemble
     */
    public function dashboard()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);
        
        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Profil athlète non trouvé.');
        }

        $athlete->load(['disciplines']);
        
        // Statistiques
        $stats = [
            'presences_mois' => $athlete->presences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', true)
                ->count(),
            'absences_mois' => $athlete->presences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', false)
                ->count(),
            'paiements_en_attente' => Paiement::where('athlete_id', $athlete->id)
                ->where('statut', 'en_attente')
                ->sum('montant'),
        ];

        // Dernières présences
        $dernieresPresences = $athlete->presences()
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Prochain événement
        $prochainEvenement = Evenement::where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->first();

        return view('portail-athlete.dashboard', compact(
            'athlete', 
            'stats', 
            'dernieresPresences',
            'prochainEvenement'
        ));
    }

    /**
     * Mes présences
     */
    public function presences()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);

        $presences = $athlete->presences()
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Stats mensuelles
        $statsMensuelles = $athlete->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN present = 1 THEN 1 ELSE 0 END) as presences,
                SUM(CASE WHEN present = 0 THEN 1 ELSE 0 END) as absences
            ')
            ->first();

        return view('portail-athlete.presences', compact('athlete', 'presences', 'statsMensuelles'));
    }

    /**
     * Mon suivi scolaire
     */
    public function suiviScolaire()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);

        $suivis = SuiviScolaire::where('athlete_id', $athlete->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('portail-athlete.suivi-scolaire', compact('athlete', 'suivis'));
    }

    /**
     * Mes paiements
     */
    public function paiements()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);

        $paiements = Paiement::where('athlete_id', $athlete->id)
            ->orderBy('date_paiement', 'desc')
            ->paginate(15);

        $totalDu = Paiement::where('athlete_id', $athlete->id)
            ->where('statut', 'en_attente')
            ->sum('montant');

        $totalPaye = Paiement::where('athlete_id', $athlete->id)
            ->where('statut', 'paye')
            ->sum('montant');

        return view('portail-athlete.paiements', compact('athlete', 'paiements', 'totalDu', 'totalPaye'));
    }

    /**
     * Mes performances
     */
    public function performances()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);

        $performances = Performance::where('athlete_id', $athlete->id)
            ->orderBy('date_evaluation', 'desc')
            ->paginate(10);

        return view('portail-athlete.performances', compact('athlete', 'performances'));
    }

    /**
     * Calendrier des événements
     */
    public function calendrier()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);
        
        $disciplineIds = $athlete->disciplines->pluck('id')->toArray();

        $evenements = Evenement::where('date_debut', '>=', now()->subMonth())
            ->where(function($q) use ($disciplineIds) {
                $q->whereIn('discipline_id', $disciplineIds)
                  ->orWhereNull('discipline_id');
            })
            ->orderBy('date_debut')
            ->get();

        return view('portail-athlete.calendrier', compact('athlete', 'evenements'));
    }

    /**
     * Mon profil
     */
    public function profil()
    {
        $user = auth()->user();
        $athlete = Athlete::find($user->athlete_id);
        $athlete->load(['disciplines', 'certificatsMedicaux']);
        
        return view('portail-athlete.profil', compact('athlete'));
    }
}

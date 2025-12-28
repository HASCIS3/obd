<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\ParentModel;
use App\Models\Presence;
use App\Models\Paiement;
use App\Models\SuiviScolaire;
use App\Models\Performance;
use App\Models\Evenement;
use Illuminate\Http\Request;

class PortailParentController extends Controller
{
    /**
     * Dashboard parent - Vue d'ensemble
     */
    public function dashboard()
    {
        $parent = auth()->user()->parentProfile;
        
        if (!$parent) {
            return redirect()->route('login')
                ->with('error', 'Profil parent non trouvé.');
        }

        $enfants = $parent->athletes()->with(['disciplines', 'certificatMedical'])->get();
        
        // Statistiques globales
        $stats = [
            'nombre_enfants' => $enfants->count(),
            'presences_mois' => $this->getPresencesMois($enfants),
            'absences_mois' => $this->getAbsencesMois($enfants),
            'paiements_en_attente' => $this->getPaiementsEnAttente($enfants),
        ];

        // Dernières activités
        $dernieresPresences = $this->getDernieresPresences($enfants, 5);
        $prochainEvenement = Evenement::where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->first();

        return view('portail-parent.dashboard', compact(
            'parent', 
            'enfants', 
            'stats', 
            'dernieresPresences',
            'prochainEvenement'
        ));
    }

    /**
     * Liste des enfants du parent
     */
    public function enfants()
    {
        $parent = auth()->user()->parentProfile;
        $enfants = $parent->athletes()
            ->with(['disciplines', 'certificatMedical', 'presences' => function($q) {
                $q->orderBy('date', 'desc')->limit(5);
            }])
            ->get();

        return view('portail-parent.enfants', compact('parent', 'enfants'));
    }

    /**
     * Détail d'un enfant
     */
    public function enfantShow(Athlete $athlete)
    {
        $parent = auth()->user()->parentProfile;
        
        // Vérifier que l'athlète appartient bien au parent
        if (!$parent->peutVoirAthlete($athlete)) {
            abort(403, 'Vous n\'avez pas accès à ce profil.');
        }

        $athlete->load(['disciplines', 'certificatMedical', 'coach']);

        // Statistiques de l'enfant
        $stats = [
            'presences_total' => $athlete->presences()->count(),
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
        ];

        return view('portail-parent.enfant-detail', compact('parent', 'athlete', 'stats'));
    }

    /**
     * Présences d'un enfant
     */
    public function presences(Athlete $athlete)
    {
        $parent = auth()->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            abort(403, 'Vous n\'avez pas accès à ce profil.');
        }

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

        return view('portail-parent.presences', compact('parent', 'athlete', 'presences', 'statsMensuelles'));
    }

    /**
     * Suivi scolaire d'un enfant
     */
    public function suiviScolaire(Athlete $athlete)
    {
        $parent = auth()->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            abort(403, 'Vous n\'avez pas accès à ce profil.');
        }

        $suivis = SuiviScolaire::where('athlete_id', $athlete->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('portail-parent.suivi-scolaire', compact('parent', 'athlete', 'suivis'));
    }

    /**
     * Paiements d'un enfant
     */
    public function paiements(Athlete $athlete)
    {
        $parent = auth()->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            abort(403, 'Vous n\'avez pas accès à ce profil.');
        }

        $paiements = Paiement::where('athlete_id', $athlete->id)
            ->orderBy('date_paiement', 'desc')
            ->paginate(15);

        // Calcul des arriérés
        $totalDu = Paiement::where('athlete_id', $athlete->id)
            ->where('statut', 'en_attente')
            ->sum('montant');

        $totalPaye = Paiement::where('athlete_id', $athlete->id)
            ->where('statut', 'paye')
            ->sum('montant');

        return view('portail-parent.paiements', compact('parent', 'athlete', 'paiements', 'totalDu', 'totalPaye'));
    }

    /**
     * Performances d'un enfant
     */
    public function performances(Athlete $athlete)
    {
        $parent = auth()->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            abort(403, 'Vous n\'avez pas accès à ce profil.');
        }

        $performances = Performance::where('athlete_id', $athlete->id)
            ->orderBy('date_evaluation', 'desc')
            ->paginate(10);

        return view('portail-parent.performances', compact('parent', 'athlete', 'performances'));
    }

    /**
     * Calendrier des événements
     */
    public function calendrier()
    {
        $parent = auth()->user()->parentProfile;
        $enfants = $parent->athletes()->pluck('discipline_id')->unique();

        $evenements = Evenement::where('date_debut', '>=', now()->subMonth())
            ->where(function($q) use ($enfants) {
                $q->whereIn('discipline_id', $enfants)
                  ->orWhereNull('discipline_id');
            })
            ->orderBy('date_debut')
            ->get();

        return view('portail-parent.calendrier', compact('parent', 'evenements'));
    }

    /**
     * Profil du parent
     */
    public function profil()
    {
        $parent = auth()->user()->parentProfile;
        
        return view('portail-parent.profil', compact('parent'));
    }

    /**
     * Mise à jour du profil
     */
    public function updateProfil(Request $request)
    {
        $parent = auth()->user()->parentProfile;

        $validated = $request->validate([
            'telephone' => 'nullable|string|max:20',
            'telephone_secondaire' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'recevoir_notifications' => 'boolean',
            'recevoir_sms' => 'boolean',
        ]);

        $parent->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    // Méthodes privées pour les statistiques
    private function getPresencesMois($enfants)
    {
        $athleteIds = $enfants->pluck('id');
        return Presence::whereIn('athlete_id', $athleteIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('present', true)
            ->count();
    }

    private function getAbsencesMois($enfants)
    {
        $athleteIds = $enfants->pluck('id');
        return Presence::whereIn('athlete_id', $athleteIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('present', false)
            ->count();
    }

    private function getPaiementsEnAttente($enfants)
    {
        $athleteIds = $enfants->pluck('id');
        return Paiement::whereIn('athlete_id', $athleteIds)
            ->where('statut', 'en_attente')
            ->sum('montant');
    }

    private function getDernieresPresences($enfants, $limit = 5)
    {
        $athleteIds = $enfants->pluck('id');
        return Presence::whereIn('athlete_id', $athleteIds)
            ->with('athlete')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
}

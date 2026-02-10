<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\ParentModel;
use App\Models\Presence;
use App\Models\Paiement;
use App\Models\SuiviScolaire;
use App\Models\Performance;
use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PortailParentController extends Controller
{
    /**
     * Dashboard parent - Vue d'ensemble
     */
    public function dashboard(Request $request): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        if (!$parent) {
            return response()->json(['message' => 'Profil parent non trouvé.'], 404);
        }

        $enfants = $parent->athletes()->with(['disciplines'])->get();
        
        // Statistiques globales
        $athleteIds = $enfants->pluck('id');
        $stats = [
            'nombre_enfants' => $enfants->count(),
            'presences_mois' => Presence::whereIn('athlete_id', $athleteIds)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', true)
                ->count(),
            'absences_mois' => Presence::whereIn('athlete_id', $athleteIds)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', false)
                ->count(),
            'paiements_en_attente' => Paiement::whereIn('athlete_id', $athleteIds)
                ->whereIn('statut', ['impaye', 'partiel'])
                ->sum('montant') - Paiement::whereIn('athlete_id', $athleteIds)
                ->whereIn('statut', ['impaye', 'partiel'])
                ->sum('montant_paye'),
        ];

        // Dernières présences
        $dernieresPresences = Presence::whereIn('athlete_id', $athleteIds)
            ->with(['athlete', 'discipline'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Prochain événement
        $prochainEvenement = Evenement::where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->first();

        return response()->json([
            'parent' => $this->formatParent($parent),
            'enfants' => $enfants->map(fn($e) => $this->formatEnfant($e)),
            'stats' => $stats,
            'dernieres_presences' => $dernieresPresences,
            'prochain_evenement' => $prochainEvenement,
        ]);
    }

    /**
     * Liste des enfants du parent
     */
    public function enfants(Request $request): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        $enfants = $parent->athletes()->with(['disciplines'])->get();

        return response()->json([
            'data' => $enfants->map(fn($e) => $this->formatEnfant($e)),
        ]);
    }

    /**
     * Détail d'un enfant
     */
    public function enfantShow(Request $request, Athlete $athlete): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $athlete->load(['disciplines', 'certificatsMedicaux']);

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

        return response()->json([
            'data' => $this->formatEnfant($athlete),
            'stats' => $stats,
        ]);
    }

    /**
     * Présences d'un enfant
     */
    public function presences(Request $request, Athlete $athlete): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $presences = $athlete->presences()
            ->with('discipline')
            ->orderBy('date', 'desc')
            ->paginate($request->per_page ?? 20);

        // Stats mensuelles
        $statsMensuelles = [
            'total' => $athlete->presences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'presences' => $athlete->presences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', true)
                ->count(),
            'absences' => $athlete->presences()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('present', false)
                ->count(),
        ];

        return response()->json([
            'data' => $presences->items(),
            'meta' => [
                'current_page' => $presences->currentPage(),
                'last_page' => $presences->lastPage(),
                'total' => $presences->total(),
            ],
            'stats_mensuelles' => $statsMensuelles,
        ]);
    }

    /**
     * Suivi scolaire d'un enfant
     */
    public function suiviScolaire(Request $request, Athlete $athlete): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $suivis = SuiviScolaire::where('athlete_id', $athlete->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'data' => $suivis->items(),
            'meta' => [
                'current_page' => $suivis->currentPage(),
                'last_page' => $suivis->lastPage(),
                'total' => $suivis->total(),
            ],
        ]);
    }

    /**
     * Paiements d'un enfant
     */
    public function paiements(Request $request, Athlete $athlete): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $paiements = Paiement::where('athlete_id', $athlete->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $totalDu = Paiement::where('athlete_id', $athlete->id)
            ->whereIn('statut', ['impaye', 'partiel'])
            ->sum('montant');

        $totalPaye = Paiement::where('athlete_id', $athlete->id)
            ->sum('montant_paye');

        return response()->json([
            'data' => $paiements->items(),
            'meta' => [
                'current_page' => $paiements->currentPage(),
                'last_page' => $paiements->lastPage(),
                'total' => $paiements->total(),
            ],
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
            'solde' => $totalDu - $totalPaye,
        ]);
    }

    /**
     * Performances d'un enfant
     */
    public function performances(Request $request, Athlete $athlete): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        if (!$parent->peutVoirAthlete($athlete)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $performances = Performance::where('athlete_id', $athlete->id)
            ->with('discipline')
            ->orderBy('date_evaluation', 'desc')
            ->paginate($request->per_page ?? 10);

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
     * Calendrier des événements
     */
    public function calendrier(Request $request): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        // Récupérer les IDs des disciplines des enfants
        $disciplineIds = [];
        foreach ($parent->athletes as $athlete) {
            foreach ($athlete->disciplines as $discipline) {
                $disciplineIds[] = $discipline->id;
            }
        }
        $disciplineIds = array_unique($disciplineIds);

        $evenements = Evenement::where('date_debut', '>=', now()->subMonth())
            ->where(function($q) use ($disciplineIds) {
                $q->whereIn('discipline_id', $disciplineIds)
                  ->orWhereNull('discipline_id');
            })
            ->orderBy('date_debut')
            ->get();

        return response()->json([
            'data' => $evenements,
        ]);
    }

    /**
     * Profil du parent
     */
    public function profil(Request $request): JsonResponse
    {
        $parent = $request->user()->parentProfile;
        
        return response()->json([
            'data' => $this->formatParent($parent),
        ]);
    }

    /**
     * Formater les données parent
     */
    private function formatParent(ParentModel $parent): array
    {
        return [
            'id' => $parent->id,
            'nom' => $parent->user->name ?? null,
            'email' => $parent->user->email ?? null,
            'telephone' => $parent->telephone,
            'telephone_secondaire' => $parent->telephone_secondaire,
            'adresse' => $parent->adresse,
            'recevoir_notifications' => $parent->recevoir_notifications,
            'recevoir_sms' => $parent->recevoir_sms,
            'actif' => $parent->actif,
        ];
    }

    /**
     * Formater les données enfant
     */
    private function formatEnfant(Athlete $athlete): array
    {
        return [
            'id' => $athlete->id,
            'nom' => $athlete->nom,
            'prenom' => $athlete->prenom,
            'nom_complet' => $athlete->nom_complet,
            'date_naissance' => $athlete->date_naissance?->format('Y-m-d'),
            'age' => $athlete->age,
            'sexe' => $athlete->sexe,
            'categorie' => $athlete->categorie,
            'photo' => $athlete->photo ? asset('storage/' . $athlete->photo) : null,
            'actif' => $athlete->actif,
            'disciplines' => $athlete->disciplines->map(fn($d) => [
                'id' => $d->id,
                'nom' => $d->nom,
            ]),
        ];
    }
}

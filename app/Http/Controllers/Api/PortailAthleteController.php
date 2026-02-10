<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Presence;
use App\Models\Paiement;
use App\Models\SuiviScolaire;
use App\Models\Performance;
use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PortailAthleteController extends Controller
{
    /**
     * Dashboard athlète - Vue d'ensemble
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $athlete = Athlete::with('disciplines')->find($user->athlete_id);
        
        if (!$athlete) {
            return response()->json(['message' => 'Profil athlète non trouvé.'], 404);
        }

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
                ->whereIn('statut', ['impaye', 'partiel'])
                ->sum('montant') - Paiement::where('athlete_id', $athlete->id)
                ->whereIn('statut', ['impaye', 'partiel'])
                ->sum('montant_paye'),
        ];

        // Dernières présences
        $dernieresPresences = $athlete->presences()
            ->with('discipline')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Prochain événement
        $prochainEvenement = Evenement::where('date_debut', '>=', now())
            ->orderBy('date_debut')
            ->first();

        return response()->json([
            'athlete' => $this->formatAthlete($athlete),
            'stats' => $stats,
            'dernieres_presences' => $dernieresPresences,
            'prochain_evenement' => $prochainEvenement,
        ]);
    }

    /**
     * Mes présences
     */
    public function presences(Request $request): JsonResponse
    {
        $user = $request->user();
        $athlete = Athlete::find($user->athlete_id);

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
     * Mon suivi scolaire
     */
    public function suiviScolaire(Request $request): JsonResponse
    {
        $user = $request->user();
        $athlete = Athlete::find($user->athlete_id);

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
     * Mes paiements
     */
    public function paiements(Request $request): JsonResponse
    {
        $user = $request->user();
        $athlete = Athlete::find($user->athlete_id);

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
     * Mes performances
     */
    public function performances(Request $request): JsonResponse
    {
        $user = $request->user();
        $athlete = Athlete::find($user->athlete_id);

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
        $user = $request->user();
        $athlete = Athlete::find($user->athlete_id);
        
        $disciplineIds = $athlete->disciplines->pluck('id')->toArray();

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
     * Mon profil
     */
    public function profil(Request $request): JsonResponse
    {
        $user = $request->user();
        $athlete = Athlete::with(['disciplines', 'certificatsMedicaux'])->find($user->athlete_id);
        
        return response()->json([
            'data' => $this->formatAthlete($athlete),
        ]);
    }

    /**
     * Formater les données athlète
     */
    private function formatAthlete(Athlete $athlete): array
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
            'telephone' => $athlete->telephone,
            'email' => $athlete->email,
            'adresse' => $athlete->adresse,
            'photo' => $athlete->photo ? asset('storage/' . $athlete->photo) : null,
            'actif' => $athlete->actif,
            'disciplines' => $athlete->disciplines->map(fn($d) => [
                'id' => $d->id,
                'nom' => $d->nom,
            ]),
            'certificats_medicaux' => $athlete->certificatsMedicaux ?? [],
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PaiementController extends Controller
{
    /**
     * Affiche la liste des paiements (Web ou API)
     */
    public function index(Request $request): View|JsonResponse
    {
        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $paiementsApi = Paiement::with('athlete')
                ->when($request->filled('athlete_id'), fn($q) => $q->where('athlete_id', $request->athlete_id))
                ->when($request->filled('statut'), fn($q) => $q->where('statut', $request->statut))
                ->when($request->filled('mois'), fn($q) => $q->where('mois', $request->mois))
                ->when($request->filled('annee'), fn($q) => $q->where('annee', $request->annee))
                ->orderBy('annee', 'desc')
                ->orderBy('mois', 'desc')
                ->paginate($request->per_page ?? 20);

            return response()->json([
                'data' => $paiementsApi->items(),
                'meta' => [
                    'current_page' => $paiementsApi->currentPage(),
                    'last_page' => $paiementsApi->lastPage(),
                    'total' => $paiementsApi->total(),
                ],
            ]);
        }

        $query = Paiement::with('athlete');

        if ($request->filled('search')) {
            $query->whereHas('athlete', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                    ->orWhere('prenom', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        $paiements = $query->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Statistiques
        $cotisationMensuelle = 2000;
        $anneeEnCours = now()->year;
        $moisEnCours = now()->month;
        
        // Calculer les vrais arriérés basés sur le suivi annuel
        $athletesActifs = Athlete::where('actif', true)->with(['paiements' => function ($q) use ($anneeEnCours) {
            $q->where('annee', $anneeEnCours)->whereIn('type_paiement', ['cotisation', 'inscription']);
        }])->get();
        
        $totalArrieres = 0;
        $nbAthletesEnRetard = 0;
        
        foreach ($athletesActifs as $athlete) {
            $totalPayeCotisation = 0;
            
            foreach ($athlete->paiements as $paiement) {
                if ($paiement->type_paiement === 'cotisation') {
                    $totalPayeCotisation += $paiement->montant_paye;
                } elseif ($paiement->type_paiement === 'inscription' && $paiement->frais_inscription > 0) {
                    $totalPayeCotisation += min($paiement->frais_inscription, $cotisationMensuelle);
                }
            }
            
            // Total attendu jusqu'au mois en cours
            $totalAttendu = $moisEnCours * $cotisationMensuelle;
            $arriereAthlete = max(0, $totalAttendu - $totalPayeCotisation);
            
            if ($arriereAthlete > 0) {
                $totalArrieres += $arriereAthlete;
                $nbAthletesEnRetard++;
            }
        }
        
        $stats = [
            'total_mois' => Paiement::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('statut', 'paye')
                ->sum('montant_paye'),
            'arrieres' => $totalArrieres,
            'nb_impayes' => $nbAthletesEnRetard,
        ];

        return view('paiements.index', compact('paiements', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Request $request): View
    {
        $athletes = Athlete::where('actif', true)->orderBy('nom')->get();
        $athleteId = $request->athlete;

        return view('paiements.create', compact('athletes', 'athleteId'));
    }

    /**
     * Enregistre un nouveau paiement (Web ou API)
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'type_paiement' => 'required|in:cotisation,inscription,equipement',
            'frais_inscription' => 'nullable|numeric|min:0',
            'type_equipement' => 'nullable|in:maillot,dobok,dobok_enfant,dobok_junior,dobok_senior',
            'frais_equipement' => 'nullable|numeric|min:0',
            'montant' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020|max:2100',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'required|in:especes,virement,mobile_money',
            'reference' => 'nullable|string|max:255',
            'remarque' => 'nullable|string|max:500',
        ]);

        // Calculer le montant total si inscription ou équipement
        $montantTotal = $validated['montant'];
        
        if ($validated['type_paiement'] === 'inscription' && !empty($validated['frais_inscription'])) {
            $montantTotal = $validated['frais_inscription'];
            if (!empty($validated['type_equipement']) && !empty($validated['frais_equipement'])) {
                $montantTotal += $validated['frais_equipement'];
            }
            $validated['montant'] = $montantTotal;
        } elseif ($validated['type_paiement'] === 'equipement' && !empty($validated['frais_equipement'])) {
            $validated['montant'] = $validated['frais_equipement'];
        }

        // Déterminer le statut
        if ($validated['montant_paye'] >= $validated['montant']) {
            $validated['statut'] = Paiement::STATUT_PAYE;
            $validated['montant_paye'] = $validated['montant'];
        } elseif ($validated['montant_paye'] > 0) {
            $validated['statut'] = Paiement::STATUT_PARTIEL;
        } else {
            $validated['statut'] = Paiement::STATUT_IMPAYE;
        }

        if ($validated['montant_paye'] > 0 && empty($validated['date_paiement'])) {
            $validated['date_paiement'] = now();
        }

        $paiement = Paiement::create($validated);
        $paiement->load('athlete');

        // Si c'est une requête API, retourner JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['data' => $paiement], 201);
        }

        return redirect()->route('paiements.show', $paiement)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un paiement (Web ou API)
     */
    public function show(Request $request, Paiement $paiement): View|JsonResponse
    {
        $paiement->load('athlete');

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['data' => $paiement]);
        }

        return view('paiements.show', compact('paiement'));
    }

    /**
     * Génère et télécharge le reçu PDF d'un paiement
     */
    public function recu(Paiement $paiement): Response
    {
        $paiement->load('athlete.disciplines');
        
        $pdf = Pdf::loadView('paiements.recu', compact('paiement'));
        
        $filename = 'recu_paiement_' . $paiement->id . '_' . $paiement->athlete->nom . '_' . $paiement->periode . '.pdf';
        $filename = str_replace(' ', '_', $filename);
        
        return $pdf->download($filename);
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Paiement $paiement): View
    {
        $athletes = Athlete::where('actif', true)->orderBy('nom')->get();
        return view('paiements.edit', compact('paiement', 'athletes'));
    }

    /**
     * Met à jour un paiement
     */
    public function update(Request $request, Paiement $paiement): RedirectResponse
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020|max:2100',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'required|in:especes,virement,mobile_money',
            'reference' => 'nullable|string|max:255',
            'remarque' => 'nullable|string|max:500',
        ]);

        // Déterminer le statut
        if ($validated['montant_paye'] >= $validated['montant']) {
            $validated['statut'] = Paiement::STATUT_PAYE;
            $validated['montant_paye'] = $validated['montant'];
        } elseif ($validated['montant_paye'] > 0) {
            $validated['statut'] = Paiement::STATUT_PARTIEL;
        } else {
            $validated['statut'] = Paiement::STATUT_IMPAYE;
        }

        $paiement->update($validated);

        return redirect()->route('paiements.show', $paiement)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Supprime un paiement (Web ou API)
     */
    public function destroy(Request $request, Paiement $paiement): RedirectResponse|JsonResponse
    {
        $paiement->delete();

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['message' => 'Paiement supprimé avec succès']);
        }

        return redirect()->route('paiements.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }

    /**
     * Affiche le suivi annuel des paiements par athlète
     */
    public function suiviAnnuel(Request $request): View
    {
        $annee = $request->input('annee', now()->year);
        $cotisationMensuelle = 2000; // Cotisation mensuelle fixe
        $totalAnnuel = $cotisationMensuelle * 12; // 24 000 FCFA par an

        // Récupérer tous les athlètes actifs avec leurs paiements (cotisation ET inscription) pour l'année
        $athletes = Athlete::where('actif', true)
            ->with(['paiements' => function ($query) use ($annee) {
                $query->where('annee', $annee)
                    ->whereIn('type_paiement', ['cotisation', 'inscription'])
                    ->orderBy('mois');
            }])
            ->orderBy('nom')
            ->get();

        // Calculer les statistiques pour chaque athlète
        $suiviAthletes = $athletes->map(function ($athlete) use ($totalAnnuel, $cotisationMensuelle) {
            $totalPaye = 0;
            $moisPayes = [];
            $moisPartiels = [];
            
            foreach ($athlete->paiements as $paiement) {
                if ($paiement->type_paiement === 'cotisation') {
                    // Paiement de cotisation mensuelle
                    $totalPaye += $paiement->montant_paye;
                    if ($paiement->statut === 'paye') {
                        $moisPayes[] = $paiement->mois;
                    } elseif ($paiement->statut === 'partiel') {
                        $moisPartiels[] = $paiement->mois;
                    }
                } elseif ($paiement->type_paiement === 'inscription' && $paiement->frais_inscription > 0) {
                    // Les frais d'inscription incluent la cotisation du premier mois (2000 FCFA)
                    $cotisationIncluse = min($paiement->frais_inscription, $cotisationMensuelle);
                    $totalPaye += $cotisationIncluse;
                    
                    // Marquer le mois comme payé si les frais d'inscription sont payés
                    if ($paiement->statut === 'paye') {
                        $moisPayes[] = $paiement->mois;
                    } elseif ($paiement->statut === 'partiel') {
                        $moisPartiels[] = $paiement->mois;
                    }
                }
            }
            
            $resteAPayer = max(0, $totalAnnuel - $totalPaye);
            $pourcentage = $totalAnnuel > 0 ? round(($totalPaye / $totalAnnuel) * 100, 1) : 0;
            
            return [
                'athlete' => $athlete,
                'total_annuel' => $totalAnnuel,
                'total_paye' => $totalPaye,
                'reste_a_payer' => $resteAPayer,
                'pourcentage' => $pourcentage,
                'mois_payes' => array_unique($moisPayes),
                'mois_partiels' => array_unique($moisPartiels),
                'nb_mois_payes' => count(array_unique($moisPayes)),
                'statut' => $resteAPayer == 0 ? 'complet' : ($totalPaye > 0 ? 'en_cours' : 'aucun'),
            ];
        });

        // Statistiques globales
        $stats = [
            'total_attendu' => $athletes->count() * $totalAnnuel,
            'total_recu' => $suiviAthletes->sum('total_paye'),
            'total_arrieres' => $suiviAthletes->sum('reste_a_payer'),
            'athletes_a_jour' => $suiviAthletes->where('statut', 'complet')->count(),
            'athletes_en_cours' => $suiviAthletes->where('statut', 'en_cours')->count(),
            'athletes_aucun' => $suiviAthletes->where('statut', 'aucun')->count(),
        ];

        return view('paiements.suivi-annuel', compact('suiviAthletes', 'annee', 'stats', 'cotisationMensuelle', 'totalAnnuel'));
    }

    /**
     * Affiche les arriérés (Web ou API)
     */
    public function arrieres(Request $request): View|JsonResponse
    {
        $arrieres = Paiement::with('athlete')
            ->whereIn('statut', ['impaye', 'partiel'])
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'data' => $arrieres,
                'total_arrieres' => $arrieres->sum(fn($p) => $p->montant - $p->montant_paye),
            ]);
        }

        $arrieresGrouped = $arrieres->groupBy('athlete_id');
        $athletes = Athlete::whereIn('id', $arrieresGrouped->keys())->get()->keyBy('id');

        return view('paiements.arrieres', compact('arrieresGrouped', 'athletes'));
    }

    /**
     * Génère les paiements mensuels pour tous les athlètes actifs
     */
    public function genererMensuel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020|max:2100',
        ]);

        $athletes = Athlete::where('actif', true)
            ->with('disciplines')
            ->get();

        $count = 0;
        foreach ($athletes as $athlete) {
            // Calculer le montant total basé sur les disciplines
            $montant = $athlete->disciplines->sum('tarif_mensuel');

            if ($montant > 0) {
                // Vérifier si le paiement existe déjà
                $exists = Paiement::where('athlete_id', $athlete->id)
                    ->where('mois', $validated['mois'])
                    ->where('annee', $validated['annee'])
                    ->exists();

                if (!$exists) {
                    Paiement::create([
                        'athlete_id' => $athlete->id,
                        'montant' => $montant,
                        'montant_paye' => 0,
                        'mois' => $validated['mois'],
                        'annee' => $validated['annee'],
                        'statut' => Paiement::STATUT_IMPAYE,
                        'mode_paiement' => Paiement::MODE_ESPECES,
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->route('paiements.index')
            ->with('success', "{$count} paiements générés avec succès.");
    }
}

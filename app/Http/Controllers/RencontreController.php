<?php

namespace App\Http\Controllers;

use App\Models\Rencontre;
use App\Models\MatchParticipation;
use App\Models\Discipline;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RencontreController extends Controller
{
    /**
     * Afficher la liste des matchs
     */
    public function index(Request $request)
    {
        $query = Rencontre::with(['discipline', 'participations.athlete'])
            ->orderByDesc('date_match');

        // Filtres
        if ($request->filled('discipline')) {
            $query->where('discipline_id', $request->discipline);
        }

        if ($request->filled('resultat')) {
            $query->where('resultat', $request->resultat);
        }

        if ($request->filled('type_competition')) {
            $query->where('type_competition', $request->type_competition);
        }

        if ($request->filled('saison')) {
            $query->where('saison', $request->saison);
        }

        // Réponse API JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $rencontres = $query->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'discipline_id' => $r->discipline_id,
                    'discipline_nom' => $r->discipline?->nom,
                    'date_match' => $r->date_match,
                    'heure_match' => $r->heure_match,
                    'type_match' => $r->type_match,
                    'adversaire' => $r->adversaire,
                    'lieu' => $r->lieu,
                    'score_obd' => $r->score_obd,
                    'score_adversaire' => $r->score_adversaire,
                    'resultat' => $r->resultat,
                    'type_competition' => $r->type_competition,
                    'saison' => $r->saison,
                    'phase' => $r->phase,
                ];
            });
            return response()->json(['data' => $rencontres]);
        }

        $rencontres = $query->paginate(15)->withQueryString();

        // Toutes les disciplines actives
        $disciplines = Discipline::where('actif', true)
            ->orderBy('nom')
            ->get();

        // Statistiques globales
        $stats = [
            'total' => Rencontre::count(),
            'victoires' => Rencontre::where('resultat', 'victoire')->count(),
            'defaites' => Rencontre::where('resultat', 'defaite')->count(),
            'nuls' => Rencontre::where('resultat', 'nul')->count(),
            'a_venir' => Rencontre::where('resultat', 'a_jouer')->count(),
        ];

        // Saisons disponibles
        $saisons = Rencontre::distinct()->pluck('saison')->filter()->sort()->reverse();

        return view('rencontres.index', compact('rencontres', 'disciplines', 'stats', 'saisons'));
    }

    /**
     * Matchs à venir (API)
     */
    public function aVenir()
    {
        $rencontres = Rencontre::with('discipline')
            ->where('resultat', 'a_jouer')
            ->where('date_match', '>=', now()->startOfDay())
            ->orderBy('date_match')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'discipline_id' => $r->discipline_id,
                    'discipline_nom' => $r->discipline?->nom,
                    'date_match' => $r->date_match,
                    'heure_match' => $r->heure_match,
                    'type_match' => $r->type_match,
                    'adversaire' => $r->adversaire,
                    'lieu' => $r->lieu,
                    'type_competition' => $r->type_competition,
                    'resultat' => 'a_venir',
                ];
            });

        return response()->json(['data' => $rencontres]);
    }

    /**
     * Derniers résultats (API)
     */
    public function resultats()
    {
        $rencontres = Rencontre::with('discipline')
            ->whereIn('resultat', ['victoire', 'defaite', 'nul'])
            ->orderByDesc('date_match')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'discipline_id' => $r->discipline_id,
                    'discipline_nom' => $r->discipline?->nom,
                    'date_match' => $r->date_match,
                    'heure_match' => $r->heure_match,
                    'type_match' => $r->type_match,
                    'adversaire' => $r->adversaire,
                    'lieu' => $r->lieu,
                    'score_obd' => $r->score_obd,
                    'score_adversaire' => $r->score_adversaire,
                    'resultat' => $r->resultat,
                    'type_competition' => $r->type_competition,
                ];
            });

        return response()->json(['data' => $rencontres]);
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $disciplines = Discipline::where('actif', true)
            ->orderBy('nom')
            ->get();

        // Saison actuelle
        $moisActuel = now()->month;
        $anneeActuelle = now()->year;
        $saison = $moisActuel >= 9 
            ? $anneeActuelle . '-' . ($anneeActuelle + 1)
            : ($anneeActuelle - 1) . '-' . $anneeActuelle;

        return view('rencontres.create', compact('disciplines', 'saison'));
    }

    /**
     * Enregistrer un nouveau match
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
            'date_match' => 'required|date',
            'heure_match' => 'nullable|date_format:H:i',
            'type_match' => 'required|in:domicile,exterieur',
            'adversaire' => 'required|string|max:255',
            'lieu' => 'nullable|string|max:255',
            'score_obd' => 'nullable|integer|min:0',
            'score_adversaire' => 'nullable|integer|min:0',
            'resultat' => 'required|in:a_jouer,victoire,defaite,nul',
            'type_competition' => 'required|in:amical,championnat,coupe,tournoi',
            'nom_competition' => 'nullable|string|max:255',
            'saison' => 'nullable|string|max:20',
            'phase' => 'nullable|in:aller,retour,poule,quart_finale,demi_finale,finale,autre',
            'remarques' => 'nullable|string',
        ]);

        $rencontre = Rencontre::create($validated);

        return redirect()
            ->route('rencontres.show', $rencontre)
            ->with('success', 'Match enregistré avec succès.');
    }

    /**
     * Afficher un match
     */
    public function show(Rencontre $rencontre)
    {
        $rencontre->load(['discipline', 'participations.athlete']);

        // Statistiques du match
        $statsMatch = [
            'nb_participants' => $rencontre->participations->count(),
            'nb_titulaires' => $rencontre->participations->where('titulaire', true)->count(),
            'total_points' => $rencontre->participations->sum('points_marques'),
            'total_passes' => $rencontre->participations->sum('passes_decisives'),
            'total_rebonds' => $rencontre->participations->sum('rebonds'),
            'moyenne_note' => $rencontre->participations->avg('note_performance'),
        ];

        // Meilleur marqueur
        $meilleurMarqueur = $rencontre->participations
            ->sortByDesc('points_marques')
            ->first();

        // Historique contre cet adversaire
        $historique = Rencontre::where('adversaire', $rencontre->adversaire)
            ->where('id', '!=', $rencontre->id)
            ->where('resultat', '!=', 'a_jouer')
            ->orderByDesc('date_match')
            ->limit(5)
            ->get();

        return view('rencontres.show', compact('rencontre', 'statsMatch', 'meilleurMarqueur', 'historique'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Rencontre $rencontre)
    {
        $disciplines = Discipline::where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('rencontres.edit', compact('rencontre', 'disciplines'));
    }

    /**
     * Mettre à jour un match
     */
    public function update(Request $request, Rencontre $rencontre)
    {
        $validated = $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
            'date_match' => 'required|date',
            'heure_match' => 'nullable|date_format:H:i',
            'type_match' => 'required|in:domicile,exterieur',
            'adversaire' => 'required|string|max:255',
            'lieu' => 'nullable|string|max:255',
            'score_obd' => 'nullable|integer|min:0',
            'score_adversaire' => 'nullable|integer|min:0',
            'resultat' => 'required|in:a_jouer,victoire,defaite,nul',
            'type_competition' => 'required|in:amical,championnat,coupe,tournoi',
            'nom_competition' => 'nullable|string|max:255',
            'saison' => 'nullable|string|max:20',
            'phase' => 'nullable|in:aller,retour,poule,quart_finale,demi_finale,finale,autre',
            'remarques' => 'nullable|string',
        ]);

        $rencontre->update($validated);

        return redirect()
            ->route('rencontres.show', $rencontre)
            ->with('success', 'Match mis à jour avec succès.');
    }

    /**
     * Supprimer un match
     */
    public function destroy(Rencontre $rencontre)
    {
        $rencontre->delete();

        return redirect()
            ->route('rencontres.index')
            ->with('success', 'Match supprimé avec succès.');
    }

    /**
     * Formulaire pour gérer les participations
     */
    public function participations(Rencontre $rencontre)
    {
        $rencontre->load(['discipline', 'participations.athlete']);

        // Athlètes de la discipline du match
        $athletes = Athlete::whereHas('disciplines', function ($q) use ($rencontre) {
            $q->where('disciplines.id', $rencontre->discipline_id);
        })
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        // IDs des athlètes déjà participants
        $participantsIds = $rencontre->participations->pluck('athlete_id')->toArray();

        return view('rencontres.participations', compact('rencontre', 'athletes', 'participantsIds'));
    }

    /**
     * Enregistrer les participations
     */
    public function storeParticipations(Request $request, Rencontre $rencontre)
    {
        $validated = $request->validate([
            'participations' => 'array',
            'participations.*.athlete_id' => 'required|exists:athletes,id',
            'participations.*.titulaire' => 'boolean',
            'participations.*.minutes_jouees' => 'nullable|integer|min:0',
            'participations.*.points_marques' => 'nullable|integer|min:0',
            'participations.*.passes_decisives' => 'nullable|integer|min:0',
            'participations.*.rebonds' => 'nullable|integer|min:0',
            'participations.*.interceptions' => 'nullable|integer|min:0',
            'participations.*.fautes' => 'nullable|integer|min:0',
            'participations.*.cartons_jaunes' => 'nullable|integer|min:0',
            'participations.*.cartons_rouges' => 'nullable|integer|min:0',
            'participations.*.note_performance' => 'nullable|numeric|min:0|max:10',
            'participations.*.remarques' => 'nullable|string',
        ]);

        DB::transaction(function () use ($rencontre, $validated) {
            // Supprimer les anciennes participations
            $rencontre->participations()->delete();

            // Créer les nouvelles participations
            if (!empty($validated['participations'])) {
                foreach ($validated['participations'] as $data) {
                    $rencontre->participations()->create([
                        'athlete_id' => $data['athlete_id'],
                        'titulaire' => $data['titulaire'] ?? false,
                        'minutes_jouees' => $data['minutes_jouees'] ?? null,
                        'points_marques' => $data['points_marques'] ?? null,
                        'passes_decisives' => $data['passes_decisives'] ?? null,
                        'rebonds' => $data['rebonds'] ?? null,
                        'interceptions' => $data['interceptions'] ?? null,
                        'fautes' => $data['fautes'] ?? null,
                        'cartons_jaunes' => $data['cartons_jaunes'] ?? null,
                        'cartons_rouges' => $data['cartons_rouges'] ?? null,
                        'note_performance' => $data['note_performance'] ?? null,
                        'remarques' => $data['remarques'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('rencontres.show', $rencontre)
            ->with('success', 'Participations enregistrées avec succès.');
    }

    /**
     * Statistiques par équipe/discipline
     */
    public function statistiques(Request $request)
    {
        $disciplineId = $request->get('discipline');
        $saison = $request->get('saison');

        $query = Rencontre::query();

        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        if ($saison) {
            $query->where('saison', $saison);
        }

        // Statistiques globales
        $stats = [
            'total_matchs' => (clone $query)->count(),
            'victoires' => (clone $query)->where('resultat', 'victoire')->count(),
            'defaites' => (clone $query)->where('resultat', 'defaite')->count(),
            'nuls' => (clone $query)->where('resultat', 'nul')->count(),
            'buts_marques' => (clone $query)->sum('score_obd'),
            'buts_encaisses' => (clone $query)->sum('score_adversaire'),
        ];

        // Pourcentage de victoires
        $matchsJoues = $stats['victoires'] + $stats['defaites'] + $stats['nuls'];
        $stats['pourcentage_victoires'] = $matchsJoues > 0 
            ? round(($stats['victoires'] / $matchsJoues) * 100, 1) 
            : 0;

        // Différence de buts
        $stats['difference_buts'] = $stats['buts_marques'] - $stats['buts_encaisses'];

        // Meilleurs joueurs (par points marqués)
        $meilleursJoueurs = MatchParticipation::select('athlete_id')
            ->selectRaw('SUM(points_marques) as total_points')
            ->selectRaw('COUNT(*) as nb_matchs')
            ->selectRaw('AVG(note_performance) as moyenne_note')
            ->when($disciplineId, function ($q) use ($disciplineId) {
                $q->whereHas('rencontre', fn($r) => $r->where('discipline_id', $disciplineId));
            })
            ->when($saison, function ($q) use ($saison) {
                $q->whereHas('rencontre', fn($r) => $r->where('saison', $saison));
            })
            ->groupBy('athlete_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->with('athlete')
            ->get();

        $disciplines = Discipline::where('actif', true)
            ->orderBy('nom')
            ->get();

        $saisons = Rencontre::distinct()->pluck('saison')->filter()->sort()->reverse();

        return view('rencontres.statistiques', compact('stats', 'meilleursJoueurs', 'disciplines', 'saisons', 'disciplineId', 'saison'));
    }

    /**
     * Calendrier des matchs
     */
    public function calendrier(Request $request)
    {
        $mois = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $rencontres = Rencontre::with('discipline')
            ->whereMonth('date_match', $mois)
            ->whereYear('date_match', $annee)
            ->orderBy('date_match')
            ->get();

        $disciplines = Discipline::where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('rencontres.calendrier', compact('rencontres', 'disciplines', 'mois', 'annee'));
    }
}

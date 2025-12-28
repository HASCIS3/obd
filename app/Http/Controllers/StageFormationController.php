<?php

namespace App\Http\Controllers;

use App\Models\StageFormation;
use App\Models\InscriptionStage;
use App\Models\Discipline;
use App\Models\Coach;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StageFormationController extends Controller
{
    public function index(Request $request)
    {
        $query = StageFormation::with(['discipline', 'inscriptions']);

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('discipline_id')) {
            $query->where('discipline_id', $request->discipline_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }

        $stages = $query->orderBy('date_debut', 'desc')->paginate(15);
        $disciplines = Discipline::orderBy('nom')->get();

        // Statistiques
        $stats = [
            'total' => StageFormation::count(),
            'en_cours' => StageFormation::where('statut', 'en_cours')->count(),
            'planifies' => StageFormation::where('statut', 'planifie')->count(),
            'termines' => StageFormation::where('statut', 'termine')->count(),
            'total_diplomes' => InscriptionStage::where('statut', 'diplome')->count(),
        ];

        return view('stages-formation.index', compact('stages', 'disciplines', 'stats'));
    }

    public function create()
    {
        $disciplines = Discipline::orderBy('nom')->get();
        $coachs = Coach::with('user')->get()->sortBy(fn($c) => $c->user->name ?? '');
        
        return view('stages-formation.create', compact('disciplines', 'coachs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:formation_formateurs,recyclage,specialisation,initiation,perfectionnement',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'lieu' => 'required|string|max:255',
            'organisme' => 'required|string|max:255',
            'programme' => 'nullable|string',
            'duree_heures' => 'nullable|integer|min:1',
            'places_disponibles' => 'required|integer|min:1',
            'frais_inscription' => 'nullable|numeric|min:0',
            'type_certification' => 'required|in:diplome,certificat,attestation',
            'intitule_certification' => 'nullable|string|max:255',
            'conditions_admission' => 'nullable|string',
            'objectifs' => 'nullable|string',
            'encadreurs' => 'nullable|array',
            'encadreurs.*' => 'string',
        ]);

        $validated['code'] = StageFormation::genererCode($validated['type']);
        $validated['created_by'] = auth()->id();
        $validated['statut'] = 'planifie';

        $stage = StageFormation::create($validated);

        return redirect()->route('stages-formation.show', $stage)
            ->with('success', 'Stage de formation créé avec succès.');
    }

    public function show(StageFormation $stages_formation)
    {
        $stages_formation->load(['discipline', 'inscriptions.coach', 'createur']);
        
        $statsInscrits = [
            'total' => $stages_formation->inscriptions->count(),
            'confirmes' => $stages_formation->inscriptions->where('statut', 'confirme')->count(),
            'en_formation' => $stages_formation->inscriptions->where('statut', 'en_formation')->count(),
            'diplomes' => $stages_formation->inscriptions->where('statut', 'diplome')->count(),
            'echecs' => $stages_formation->inscriptions->where('statut', 'echec')->count(),
            'abandons' => $stages_formation->inscriptions->where('statut', 'abandon')->count(),
        ];

        return view('stages-formation.show', ['stageFormation' => $stages_formation, 'statsInscrits' => $statsInscrits]);
    }

    public function edit(StageFormation $stages_formation)
    {
        $disciplines = Discipline::orderBy('nom')->get();
        $coachs = Coach::with('user')->get()->sortBy(fn($c) => $c->user->name ?? '');
        
        return view('stages-formation.edit', ['stageFormation' => $stages_formation, 'disciplines' => $disciplines, 'coachs' => $coachs]);
    }

    public function update(Request $request, StageFormation $stages_formation)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:formation_formateurs,recyclage,specialisation,initiation,perfectionnement',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'lieu' => 'required|string|max:255',
            'organisme' => 'required|string|max:255',
            'programme' => 'nullable|string',
            'duree_heures' => 'nullable|integer|min:1',
            'places_disponibles' => 'required|integer|min:1',
            'frais_inscription' => 'nullable|numeric|min:0',
            'type_certification' => 'required|in:diplome,certificat,attestation',
            'intitule_certification' => 'nullable|string|max:255',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'conditions_admission' => 'nullable|string',
            'objectifs' => 'nullable|string',
            'encadreurs' => 'nullable|array',
            'encadreurs.*' => 'string',
        ]);

        $stages_formation->update($validated);

        return redirect()->route('stages-formation.show', $stages_formation)
            ->with('success', 'Stage de formation mis à jour avec succès.');
    }

    public function destroy(StageFormation $stages_formation)
    {
        $stages_formation->delete();

        return redirect()->route('stages-formation.index')
            ->with('success', 'Stage de formation supprimé avec succès.');
    }

    // Gestion des inscriptions
    public function inscriptions(StageFormation $stages_formation)
    {
        $stages_formation->load(['inscriptions.coach', 'discipline']);
        $coachs = Coach::with('user')->get()->sortBy(fn($c) => $c->user->name ?? '');
        
        return view('stages-formation.inscriptions', ['stageFormation' => $stages_formation, 'coachs' => $coachs]);
    }

    public function storeInscription(Request $request, StageFormation $stages_formation)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'sexe' => 'required|in:M,F',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'structure' => 'nullable|string|max:255',
            'niveau_etude' => 'nullable|string|max:50',
            'experience' => 'nullable|string',
            'coach_id' => 'nullable|exists:coachs,id',
        ]);

        $validated['stage_formation_id'] = $stages_formation->id;
        $validated['statut'] = 'inscrit';

        InscriptionStage::create($validated);

        return redirect()->route('stages-formation.inscriptions', $stages_formation)
            ->with('success', 'Participant inscrit avec succès.');
    }

    public function updateInscription(Request $request, InscriptionStage $inscription)
    {
        $validated = $request->validate([
            'statut' => 'required|in:inscrit,confirme,en_formation,diplome,echec,abandon',
            'note_finale' => 'nullable|numeric|min:0|max:20',
            'appreciation' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $inscription->update($validated);

        return back()->with('success', 'Inscription mise à jour avec succès.');
    }

    public function destroyInscription(InscriptionStage $inscription)
    {
        $stageFormation = $inscription->stageFormation;
        $inscription->delete();

        return redirect()->route('stages-formation.inscriptions', $stageFormation)
            ->with('success', 'Inscription supprimée avec succès.');
    }

    // Délivrer certificat
    public function delivrerCertificat(InscriptionStage $inscription)
    {
        $inscription->delivrerCertificat();

        return back()->with('success', 'Certificat délivré avec succès.');
    }

    // Générer certificat PDF
    public function certificatPdf(InscriptionStage $inscription)
    {
        $inscription->load(['stageFormation.discipline']);
        
        $pdf = Pdf::loadView('stages-formation.certificat-pdf', compact('inscription'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'certificat_' . $inscription->numero_certificat . '.pdf';
        
        return $pdf->download($filename);
    }

    // Liste des diplômés
    public function diplomes(StageFormation $stages_formation)
    {
        $diplomes = $stages_formation->inscriptions()
            ->where('statut', 'diplome')
            ->orderBy('nom')
            ->get();
        
        return view('stages-formation.diplomes', ['stageFormation' => $stages_formation, 'diplomes' => $diplomes]);
    }

    // Export PDF liste des participants
    public function listeParticipantsPdf(StageFormation $stages_formation)
    {
        $stages_formation->load(['inscriptions', 'discipline']);
        
        $pdf = Pdf::loadView('stages-formation.liste-participants-pdf', ['stageFormation' => $stages_formation]);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'participants_' . $stages_formation->code . '.pdf';
        
        return $pdf->download($filename);
    }
}

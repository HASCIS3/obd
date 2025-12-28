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
        $coachs = Coach::orderBy('nom')->get();
        
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

    public function show(StageFormation $stageFormation)
    {
        $stageFormation->load(['discipline', 'inscriptions.coach', 'createur']);
        
        $statsInscrits = [
            'total' => $stageFormation->inscriptions->count(),
            'confirmes' => $stageFormation->inscriptions->where('statut', 'confirme')->count(),
            'en_formation' => $stageFormation->inscriptions->where('statut', 'en_formation')->count(),
            'diplomes' => $stageFormation->inscriptions->where('statut', 'diplome')->count(),
            'echecs' => $stageFormation->inscriptions->where('statut', 'echec')->count(),
            'abandons' => $stageFormation->inscriptions->where('statut', 'abandon')->count(),
        ];

        return view('stages-formation.show', compact('stageFormation', 'statsInscrits'));
    }

    public function edit(StageFormation $stageFormation)
    {
        $disciplines = Discipline::orderBy('nom')->get();
        $coachs = Coach::orderBy('nom')->get();
        
        return view('stages-formation.edit', compact('stageFormation', 'disciplines', 'coachs'));
    }

    public function update(Request $request, StageFormation $stageFormation)
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

        $stageFormation->update($validated);

        return redirect()->route('stages-formation.show', $stageFormation)
            ->with('success', 'Stage de formation mis à jour avec succès.');
    }

    public function destroy(StageFormation $stageFormation)
    {
        $stageFormation->delete();

        return redirect()->route('stages-formation.index')
            ->with('success', 'Stage de formation supprimé avec succès.');
    }

    // Gestion des inscriptions
    public function inscriptions(StageFormation $stageFormation)
    {
        $stageFormation->load(['inscriptions.coach', 'discipline']);
        $coachs = Coach::orderBy('nom')->get();
        
        return view('stages-formation.inscriptions', compact('stageFormation', 'coachs'));
    }

    public function storeInscription(Request $request, StageFormation $stageFormation)
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

        $validated['stage_formation_id'] = $stageFormation->id;
        $validated['statut'] = 'inscrit';

        InscriptionStage::create($validated);

        return redirect()->route('stages-formation.inscriptions', $stageFormation)
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
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'certificat_' . $inscription->numero_certificat . '.pdf';
        
        return $pdf->download($filename);
    }

    // Liste des diplômés
    public function diplomes(StageFormation $stageFormation)
    {
        $diplomes = $stageFormation->inscriptions()
            ->where('statut', 'diplome')
            ->orderBy('nom')
            ->get();
        
        return view('stages-formation.diplomes', compact('stageFormation', 'diplomes'));
    }

    // Export PDF liste des participants
    public function listeParticipantsPdf(StageFormation $stageFormation)
    {
        $stageFormation->load(['inscriptions', 'discipline']);
        
        $pdf = Pdf::loadView('stages-formation.liste-participants-pdf', compact('stageFormation'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'participants_' . $stageFormation->code . '.pdf';
        
        return $pdf->download($filename);
    }
}

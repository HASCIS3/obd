<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\CertificatMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificatMedicalController extends Controller
{
    public function index(Request $request)
    {
        $query = CertificatMedical::with('athlete');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('athlete', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => CertificatMedical::count(),
            'valides' => CertificatMedical::valides()->count(),
            'expires' => CertificatMedical::expires()->count(),
            'expirant_bientot' => CertificatMedical::expirantBientot(30)->count(),
        ];

        $certificats = $query->orderBy('date_expiration', 'asc')->paginate(15);

        return view('certificats-medicaux.index', compact('certificats', 'stats'));
    }

    public function create(Request $request)
    {
        $athletes = Athlete::actifs()->orderBy('nom')->get();
        $athleteId = $request->query('athlete_id');

        return view('certificats-medicaux.create', compact('athletes', 'athleteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'type' => 'required|in:aptitude,inaptitude_temporaire,inaptitude_definitive,suivi',
            'date_examen' => 'required|date',
            'date_expiration' => 'required|date|after:date_examen',
            'medecin' => 'required|string|max:100',
            'etablissement' => 'nullable|string|max:150',
            'apte_competition' => 'boolean',
            'apte_entrainement' => 'boolean',
            'restrictions' => 'nullable|string',
            'observations' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['apte_competition'] = $request->boolean('apte_competition');
        $validated['apte_entrainement'] = $request->boolean('apte_entrainement');
        $validated['statut'] = CertificatMedical::STATUT_VALIDE;

        if ($request->hasFile('document')) {
            $validated['document'] = $request->file('document')->store('certificats-medicaux', 'public');
        }

        $certificat = CertificatMedical::create($validated);

        return redirect()->route('certificats-medicaux.show', $certificat)
            ->with('success', 'Certificat médical créé avec succès.');
    }

    public function show(CertificatMedical $certificats_medicaux)
    {
        $certificat = $certificats_medicaux;
        $certificat->load('athlete');

        $historique = CertificatMedical::where('athlete_id', $certificat->athlete_id)
            ->where('id', '!=', $certificat->id)
            ->orderBy('date_examen', 'desc')
            ->get();

        return view('certificats-medicaux.show', compact('certificat', 'historique'));
    }

    public function edit(CertificatMedical $certificats_medicaux)
    {
        $certificat = $certificats_medicaux;
        $athletes = Athlete::actifs()->orderBy('nom')->get();

        return view('certificats-medicaux.edit', compact('certificat', 'athletes'));
    }

    public function update(Request $request, CertificatMedical $certificats_medicaux)
    {
        $certificat = $certificats_medicaux;

        $validated = $request->validate([
            'type' => 'required|in:aptitude,inaptitude_temporaire,inaptitude_definitive,suivi',
            'date_examen' => 'required|date',
            'date_expiration' => 'required|date|after:date_examen',
            'medecin' => 'required|string|max:100',
            'etablissement' => 'nullable|string|max:150',
            'statut' => 'required|in:valide,expire,en_attente',
            'apte_competition' => 'boolean',
            'apte_entrainement' => 'boolean',
            'restrictions' => 'nullable|string',
            'observations' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['apte_competition'] = $request->boolean('apte_competition');
        $validated['apte_entrainement'] = $request->boolean('apte_entrainement');

        if ($request->hasFile('document')) {
            if ($certificat->document) {
                Storage::disk('public')->delete($certificat->document);
            }
            $validated['document'] = $request->file('document')->store('certificats-medicaux', 'public');
        }

        $certificat->update($validated);

        return redirect()->route('certificats-medicaux.show', $certificat)
            ->with('success', 'Certificat médical mis à jour avec succès.');
    }

    public function destroy(CertificatMedical $certificats_medicaux)
    {
        $certificat = $certificats_medicaux;

        if ($certificat->document) {
            Storage::disk('public')->delete($certificat->document);
        }

        $certificat->delete();

        return redirect()->route('certificats-medicaux.index')
            ->with('success', 'Certificat médical supprimé avec succès.');
    }

    public function expirantBientot()
    {
        $certificats = CertificatMedical::with('athlete')
            ->expirantBientot(30)
            ->orderBy('date_expiration')
            ->get();

        return view('certificats-medicaux.expirant-bientot', compact('certificats'));
    }

    public function verifierExpirations()
    {
        $count = 0;
        CertificatMedical::valides()
            ->where('date_expiration', '<', now())
            ->each(function ($certificat) use (&$count) {
                $certificat->update(['statut' => CertificatMedical::STATUT_EXPIRE]);
                $count++;
            });

        return redirect()->route('certificats-medicaux.index')
            ->with('success', "{$count} certificat(s) marqué(s) comme expiré(s).");
    }
}

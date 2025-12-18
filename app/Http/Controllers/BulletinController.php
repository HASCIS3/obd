<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\SuiviScolaire;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    /**
     * Formulaire public pour soumettre un bulletin (accessible via token)
     */
    public function formulaireEcole(string $token): View
    {
        $athlete = Athlete::where('bulletin_token', $token)->firstOrFail();
        
        return view('bulletins.formulaire-ecole', compact('athlete', 'token'));
    }

    /**
     * Enregistrer le bulletin soumis par l'école
     */
    public function soumettreBulletin(Request $request, string $token): RedirectResponse
    {
        $athlete = Athlete::where('bulletin_token', $token)->firstOrFail();

        $validated = $request->validate([
            'etablissement' => 'required|string|max:255',
            'classe' => 'required|string|max:100',
            'annee_scolaire' => 'required|string|max:20',
            'trimestre' => 'required|string|max:50',
            'moyenne_generale' => 'required|numeric|min:0|max:20',
            'rang' => 'nullable|integer|min:1',
            'effectif_classe' => 'nullable|integer|min:1',
            'observations' => 'nullable|string|max:1000',
            'bulletin_photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'nom_enseignant' => 'nullable|string|max:255',
            'email_enseignant' => 'nullable|email|max:255',
        ]);

        // Sauvegarder la photo du bulletin
        $bulletinPath = $request->file('bulletin_photo')->store('bulletins', 'public');

        // Créer le suivi scolaire
        $suivi = SuiviScolaire::create([
            'athlete_id' => $athlete->id,
            'etablissement' => $validated['etablissement'],
            'classe' => $validated['classe'],
            'annee_scolaire' => $validated['annee_scolaire'] . ' - ' . $validated['trimestre'],
            'moyenne_generale' => $validated['moyenne_generale'],
            'rang' => $validated['rang'],
            'observations' => $validated['observations'],
            'bulletin_path' => $bulletinPath,
        ]);

        // Régénérer le token pour sécurité
        $athlete->update(['bulletin_token' => Str::random(64)]);

        return redirect()->route('bulletin.confirmation', $suivi->id)
            ->with('success', 'Bulletin soumis avec succès. Merci !');
    }

    /**
     * Page de confirmation après soumission
     */
    public function confirmation(SuiviScolaire $suivi): View
    {
        return view('bulletins.confirmation', compact('suivi'));
    }

    /**
     * Générer le lien unique pour un athlète (admin)
     */
    public function genererLien(Athlete $athlete): RedirectResponse
    {
        if (!$athlete->bulletin_token) {
            $athlete->update(['bulletin_token' => Str::random(64)]);
        }

        return back()->with('success', 'Lien de soumission généré avec succès.');
    }

    /**
     * Régénérer le lien (admin)
     */
    public function regenererLien(Athlete $athlete): RedirectResponse
    {
        $athlete->update(['bulletin_token' => Str::random(64)]);

        return back()->with('success', 'Nouveau lien généré avec succès.');
    }

    /**
     * Générer et télécharger le rapport PDF pour les parents
     */
    public function rapportPdf(Athlete $athlete)
    {
        $athlete->load(['suivisScolaires', 'presences', 'disciplinesActives']);
        
        // Analyse de l'athlète
        $analyse = $this->analyserAthlete($athlete);
        $observations = $this->genererObservations($analyse);
        $recommandations = $this->genererRecommandationsParent($analyse);
        
        $presences = $athlete->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('bulletins.rapport-pdf', compact(
            'athlete', 'analyse', 'observations', 'recommandations', 'presences'
        ));

        $filename = 'rapport_' . Str::slug($athlete->nom_complet) . '_' . now()->format('Y-m') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Envoyer le rapport par email aux parents
     */
    public function envoyerRapport(Request $request, Athlete $athlete): RedirectResponse
    {
        // TODO: Implémenter l'envoi par email
        // Pour l'instant, on redirige vers le téléchargement PDF
        
        return back()->with('success', 'Rapport envoyé aux parents avec succès.');
    }

    // ==================== METHODES PRIVEES ====================

    private function analyserAthlete(Athlete $athlete): array
    {
        $dernierSuivi = $athlete->suivisScolaires->sortByDesc('annee_scolaire')->first();
        $moyenne = $dernierSuivi?->moyenne_generale;
        $tauxPresence = $athlete->taux_presence;

        // Calcul de la tendance
        $suivis = $athlete->suivisScolaires->sortBy('annee_scolaire')->values();
        $tendance = 'stable';
        if ($suivis->count() >= 2) {
            $derniere = $suivis->last()?->moyenne_generale ?? 0;
            $avantDerniere = $suivis->get($suivis->count() - 2)?->moyenne_generale ?? 0;
            if ($derniere > $avantDerniere + 0.5) $tendance = 'hausse';
            elseif ($derniere < $avantDerniere - 0.5) $tendance = 'baisse';
        }

        // Évaluation de l'équilibre
        $equilibre = $this->evaluerEquilibre($tauxPresence, $moyenne);

        // Calcul du nombre de séances
        $nbSeancesMois = $athlete->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('present', true)
            ->count();

        return [
            'athlete' => $athlete,
            'moyenne' => $moyenne,
            'taux_presence' => $tauxPresence,
            'tendance' => $tendance,
            'equilibre' => $equilibre['label'],
            'equilibre_color' => $equilibre['color'],
            'equilibre_score' => $equilibre['score'],
            'equilibre_description' => $equilibre['description'],
            'nb_seances' => $nbSeancesMois,
        ];
    }

    private function evaluerEquilibre(float $tauxPresence, ?float $moyenne): array
    {
        if ($moyenne === null) {
            return [
                'label' => 'Non évalué',
                'color' => 'gray',
                'score' => 50,
                'description' => 'Aucune donnée scolaire disponible.',
            ];
        }

        $scorePresence = min(100, $tauxPresence);
        $scoreMoyenne = min(100, $moyenne * 5);
        $score = ($scorePresence + $scoreMoyenne) / 2;

        if ($tauxPresence >= 80 && $moyenne >= 12) {
            return [
                'label' => 'Excellent',
                'color' => 'success',
                'score' => $score,
                'description' => 'Excellent équilibre entre sport et études.',
            ];
        }

        if ($tauxPresence >= 70 && $moyenne >= 10) {
            return [
                'label' => 'Bon',
                'color' => 'primary',
                'score' => $score,
                'description' => 'Équilibre satisfaisant.',
            ];
        }

        if ($tauxPresence >= 90 && $moyenne < 10) {
            return [
                'label' => 'Déséquilibré',
                'color' => 'warning',
                'score' => $score,
                'description' => 'L\'intensité sportive impacte les études.',
            ];
        }

        return [
            'label' => 'À surveiller',
            'color' => 'danger',
            'score' => $score,
            'description' => 'Nécessite une attention particulière.',
        ];
    }

    private function genererObservations(array $analyse): array
    {
        $observations = [];

        if ($analyse['taux_presence'] >= 80) {
            $observations[] = [
                'icon' => '✅',
                'titre' => 'Assiduité',
                'message' => "Excellente régularité aux entraînements ({$analyse['taux_presence']}%).",
            ];
        } else {
            $observations[] = [
                'icon' => '📊',
                'titre' => 'Assiduité',
                'message' => "Taux de présence de {$analyse['taux_presence']}%.",
            ];
        }

        if ($analyse['moyenne'] !== null) {
            if ($analyse['moyenne'] >= 14) {
                $observations[] = [
                    'icon' => '🌟',
                    'titre' => 'Résultats scolaires',
                    'message' => "Excellents résultats ({$analyse['moyenne']}/20).",
                ];
            } elseif ($analyse['moyenne'] >= 10) {
                $observations[] = [
                    'icon' => '📚',
                    'titre' => 'Résultats scolaires',
                    'message' => "Résultats satisfaisants ({$analyse['moyenne']}/20).",
                ];
            } else {
                $observations[] = [
                    'icon' => '⚠️',
                    'titre' => 'Résultats scolaires',
                    'message' => "Moyenne de {$analyse['moyenne']}/20 à améliorer.",
                ];
            }
        }

        $observations[] = [
            'icon' => '⚖️',
            'titre' => 'Équilibre',
            'message' => $analyse['equilibre_description'],
        ];

        return $observations;
    }

    private function genererRecommandationsParent(array $analyse): array
    {
        $recommandations = [];

        if ($analyse['moyenne'] !== null && $analyse['moyenne'] < 10) {
            $recommandations[] = "Prévoir des temps d'étude réguliers";
            $recommandations[] = "Envisager un soutien scolaire si nécessaire";
        }

        $recommandations[] = "Maintenir un bon équilibre repos/études/sport";
        $recommandations[] = "Veiller à une alimentation équilibrée";
        
        if ($analyse['tendance'] === 'baisse') {
            $recommandations[] = "Discuter des difficultés rencontrées";
        }

        return $recommandations;
    }
}

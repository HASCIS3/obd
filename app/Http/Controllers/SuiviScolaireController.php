<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Presence;
use App\Models\SuiviScolaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SuiviScolaireController extends Controller
{
    /**
     * Affiche la liste des suivis scolaires
     */
    public function index(Request $request): View
    {
        $query = SuiviScolaire::with('athlete');

        if ($request->filled('search')) {
            $query->whereHas('athlete', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                    ->orWhere('prenom', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('annee_scolaire')) {
            $query->where('annee_scolaire', $request->annee_scolaire);
        }

        if ($request->filled('satisfaisant')) {
            if ($request->satisfaisant === '1') {
                $query->where('moyenne_generale', '>=', 10);
            } else {
                $query->where('moyenne_generale', '<', 10);
            }
        }

        $suivis = $query->orderBy('annee_scolaire', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Années scolaires disponibles
        // 
        $anneesScolaires = SuiviScolaire::distinct()
            ->pluck('annee_scolaire')
            ->filter()
            ->sort()
            ->reverse();

        return view('suivis-scolaires.index', compact('suivis', 'anneesScolaires'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Request $request): View
    {
        $athletes = Athlete::where('actif', true)->orderBy('nom')->get();
        $athleteId = $request->athlete;

        return view('suivis-scolaires.create', compact('athletes', 'athleteId'));
    }

    /**
     * Enregistre un nouveau suivi scolaire
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'etablissement' => 'nullable|string|max:255',
            'classe' => 'nullable|string|max:100',
            'annee_scolaire' => 'nullable|string|max:20',
            'moyenne_generale' => 'nullable|numeric|min:0|max:20',
            'rang' => 'nullable|integer|min:1',
            'observations' => 'nullable|string|max:1000',
            'bulletin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('bulletin')) {
            $validated['bulletin_path'] = $request->file('bulletin')->store('bulletins', 'public');
        }

        unset($validated['bulletin']);

        $suivi = SuiviScolaire::create($validated);

        return redirect()->route('suivis-scolaires.show', $suivi)
            ->with('success', 'Suivi scolaire enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un suivi scolaire
     */
    public function show(SuiviScolaire $suivis_scolaire): View
    {
        $suivis_scolaire->load('athlete');
        $suiviScolaire = $suivis_scolaire;
        return view('suivis-scolaires.show', compact('suiviScolaire'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(SuiviScolaire $suivis_scolaire): View
    {
        $athletes = Athlete::where('actif', true)->orderBy('nom')->get();
        $suiviScolaire = $suivis_scolaire;
        return view('suivis-scolaires.edit', compact('suiviScolaire', 'athletes'));
    }

    /**
     * Met à jour un suivi scolaire
     */
    public function update(Request $request, SuiviScolaire $suivis_scolaire): RedirectResponse
    {
        $validated = $request->validate([
            'etablissement' => 'nullable|string|max:255',
            'classe' => 'nullable|string|max:100',
            'annee_scolaire' => 'nullable|string|max:20',
            'moyenne_generale' => 'nullable|numeric|min:0|max:20',
            'rang' => 'nullable|integer|min:1',
            'observations' => 'nullable|string|max:1000',
            'bulletin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('bulletin')) {
            $validated['bulletin_path'] = $request->file('bulletin')->store('bulletins', 'public');
        }

        unset($validated['bulletin']);

        $suivis_scolaire->update($validated);

        return redirect()->route('suivis-scolaires.show', ['suivis_scolaire' => $suivis_scolaire->id])
            ->with('success', 'Suivi scolaire mis à jour avec succès.');
    }

    /**
     * Supprime un suivi scolaire
     */
    public function destroy(SuiviScolaire $suivis_scolaire): RedirectResponse
    {
        $suivis_scolaire->delete();

        return redirect()->route('suivis-scolaires.index')
            ->with('success', 'Suivi scolaire supprimé avec succès.');
    }

    /**
     * Gestion des bulletins et liens
     */
    public function gestionBulletins(): View
    {
        $athletes = Athlete::with(['suivisScolaires'])
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('suivis-scolaires.gestion-bulletins', compact('athletes'));
    }

    /**
     * Dashboard de suivi Sport/Etudes avec corrélation
     */
    public function dashboard(): View
    {
        // Récupérer les athlètes avec leurs suivis et présences
        $athletes = Athlete::with(['suivisScolaires', 'presences', 'disciplinesActives'])
            ->whereHas('suivisScolaires')
            ->orWhereHas('presences')
            ->get();

        // Statistiques globales
        $stats = $this->calculerStatistiquesGlobales($athletes);

        // Analyse par athlète
        $athletesAnalyse = $athletes->map(function ($athlete) {
            return $this->analyserAthlete($athlete);
        })->sortByDesc('alerte');

        // Alertes
        $alertes = $this->genererAlertes($athletesAnalyse);

        // Données pour les graphiques
        $correlationData = $this->prepareCorrelationData($athletesAnalyse);
        $niveauData = $this->prepareNiveauData($athletesAnalyse);
        $evolutionData = $this->prepareEvolutionData();

        return view('suivis-scolaires.dashboard', compact(
            'stats', 'athletesAnalyse', 'alertes', 
            'correlationData', 'niveauData', 'evolutionData'
        ));
    }

    /**
     * Rapport détaillé d'un athlète
     */
    public function rapportAthlete(Athlete $athlete): View
    {
        $athlete->load(['suivisScolaires', 'presences', 'disciplinesActives']);
        
        $analyse = $this->analyserAthlete($athlete);
        $recommandations = $this->genererRecommandations($analyse);
        $suivis = $athlete->suivisScolaires()->orderBy('annee_scolaire', 'desc')->get();
        $presences = $athlete->presences()->orderBy('date', 'desc')->take(30)->get()->reverse();
        $evolutionData = $this->prepareEvolutionDataAthlete($athlete);

        return view('suivis-scolaires.rapport-athlete', compact(
            'athlete', 'analyse', 'recommandations', 'suivis', 'presences', 'evolutionData'
        ));
    }

    /**
     * Rapport pour les parents (imprimable)
     */
    public function rapportParent(Athlete $athlete): View
    {
        $athlete->load(['suivisScolaires', 'presences', 'disciplinesActives']);
        
        $analyse = $this->analyserAthlete($athlete);
        $observations = $this->genererObservations($analyse);
        $recommandationsParent = $this->genererRecommandationsParent($analyse);
        $presences = $athlete->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date')
            ->get();

        return view('suivis-scolaires.rapport-parent', compact(
            'athlete', 'analyse', 'observations', 'recommandationsParent', 'presences'
        ));
    }

    // ==================== METHODES PRIVEES ====================

    private function calculerStatistiquesGlobales($athletes): array
    {
        $totalAthletes = $athletes->count();
        
        $moyenneGlobale = $athletes->flatMap->suivisScolaires
            ->whereNotNull('moyenne_generale')
            ->avg('moyenne_generale') ?? 0;

        $tauxPresenceMoyen = $athletes->avg(function ($athlete) {
            return $athlete->taux_presence;
        });

        // Calcul de la corrélation
        $correlation = $this->calculerCorrelation($athletes);

        return [
            'total_athletes' => $totalAthletes,
            'moyenne_globale' => $moyenneGlobale,
            'taux_presence_moyen' => $tauxPresenceMoyen,
            'correlation' => $correlation,
        ];
    }

    private function calculerCorrelation($athletes): string
    {
        $data = $athletes->filter(function ($athlete) {
            $dernierSuivi = $athlete->suivisScolaires->sortByDesc('annee_scolaire')->first();
            return $dernierSuivi && $dernierSuivi->moyenne_generale && $athlete->presences->count() > 0;
        });

        if ($data->count() < 3) {
            return 'Données insuffisantes';
        }

        $presences = $data->pluck('taux_presence')->toArray();
        $moyennes = $data->map(function ($a) {
            return $a->suivisScolaires->sortByDesc('annee_scolaire')->first()->moyenne_generale;
        })->toArray();

        $n = count($presences);
        $sumX = array_sum($presences);
        $sumY = array_sum($moyennes);
        $sumXY = 0;
        $sumX2 = 0;
        $sumY2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $presences[$i] * $moyennes[$i];
            $sumX2 += $presences[$i] ** 2;
            $sumY2 += $moyennes[$i] ** 2;
        }

        $denominator = sqrt(($n * $sumX2 - $sumX ** 2) * ($n * $sumY2 - $sumY ** 2));
        
        if ($denominator == 0) {
            return 'Non calculable';
        }

        $r = ($n * $sumXY - $sumX * $sumY) / $denominator;

        if ($r >= 0.7) return 'Forte positive';
        if ($r >= 0.4) return 'Modérée positive';
        if ($r >= 0.1) return 'Faible positive';
        if ($r >= -0.1) return 'Neutre';
        if ($r >= -0.4) return 'Faible négative';
        if ($r >= -0.7) return 'Modérée négative';
        return 'Forte négative';
    }

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

        // Calcul de l'intensité d'entraînement
        $nbSeancesMois = $athlete->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('present', true)
            ->count();

        $intensite = min(5, ceil($nbSeancesMois / 4));
        $intensiteLabels = ['Très faible', 'Faible', 'Modérée', 'Élevée', 'Très élevée'];

        // Impact sur les études
        $impact = 'neutre';
        if ($tauxPresence >= 80 && $moyenne >= 12) $impact = 'positif';
        elseif ($tauxPresence >= 90 && $moyenne < 10) $impact = 'negatif';

        // Charge recommandée
        $chargeRecommandee = 3;
        if ($moyenne < 10) $chargeRecommandee = 2;
        if ($moyenne >= 14 && $tauxPresence >= 80) $chargeRecommandee = 4;

        // Conclusion
        $conclusion = $this->genererConclusion($tauxPresence, $moyenne, $tendance);

        return [
            'athlete' => $athlete,
            'moyenne' => $moyenne,
            'taux_presence' => $tauxPresence,
            'tendance' => $tendance,
            'equilibre' => $equilibre['label'],
            'equilibre_color' => $equilibre['color'],
            'equilibre_score' => $equilibre['score'],
            'equilibre_description' => $equilibre['description'],
            'alerte' => $equilibre['alerte'],
            'nb_seances' => $nbSeancesMois,
            'intensite' => $intensite,
            'intensite_label' => $intensiteLabels[$intensite - 1] ?? 'Non défini',
            'impact' => $impact,
            'charge_recommandee' => $chargeRecommandee,
            'conclusion' => $conclusion,
        ];
    }

    private function evaluerEquilibre(float $tauxPresence, ?float $moyenne): array
    {
        if ($moyenne === null) {
            return [
                'label' => 'Non évalué',
                'color' => 'gray',
                'score' => 50,
                'description' => 'Aucune donnée scolaire disponible pour évaluer l\'équilibre.',
                'alerte' => false,
            ];
        }

        // Score basé sur présence et moyenne
        $scorePresence = min(100, $tauxPresence);
        $scoreMoyenne = min(100, $moyenne * 5); // 20/20 = 100%
        $score = ($scorePresence + $scoreMoyenne) / 2;

        if ($tauxPresence >= 80 && $moyenne >= 12) {
            return [
                'label' => 'Excellent',
                'color' => 'success',
                'score' => $score,
                'description' => 'Votre enfant maintient un excellent équilibre entre sport et études. Continuez ainsi !',
                'alerte' => false,
            ];
        }

        if ($tauxPresence >= 70 && $moyenne >= 10) {
            return [
                'label' => 'Bon',
                'color' => 'primary',
                'score' => $score,
                'description' => 'L\'équilibre est satisfaisant. Quelques ajustements peuvent optimiser les performances.',
                'alerte' => false,
            ];
        }

        if ($tauxPresence >= 90 && $moyenne < 10) {
            return [
                'label' => 'Déséquilibré',
                'color' => 'warning',
                'score' => $score,
                'description' => 'L\'intensité sportive semble impacter les résultats scolaires. Une réduction des entraînements est conseillée.',
                'alerte' => true,
            ];
        }

        if ($tauxPresence < 60 && $moyenne >= 12) {
            return [
                'label' => 'Sport insuffisant',
                'color' => 'warning',
                'score' => $score,
                'description' => 'Les résultats scolaires sont bons mais la pratique sportive est insuffisante.',
                'alerte' => false,
            ];
        }

        return [
            'label' => 'À surveiller',
            'color' => 'danger',
            'score' => $score,
            'description' => 'L\'équilibre nécessite une attention particulière. Un suivi rapproché est recommandé.',
            'alerte' => true,
        ];
    }

    private function genererConclusion(float $tauxPresence, ?float $moyenne, string $tendance): string
    {
        if ($moyenne === null) {
            return "Nous n'avons pas encore de données scolaires pour cet athlète. Merci de nous fournir les bulletins.";
        }

        if ($tauxPresence >= 80 && $moyenne >= 12) {
            return "Cet athlète démontre une excellente capacité à concilier sport et études. La pratique sportive semble avoir un effet positif sur sa discipline et ses résultats.";
        }

        if ($tauxPresence >= 90 && $moyenne < 10) {
            return "L'intensité de la pratique sportive pourrait impacter les études. Nous recommandons de réduire temporairement les entraînements pour permettre un meilleur équilibre.";
        }

        if ($tendance === 'baisse') {
            return "Une baisse des résultats scolaires est observée. Il serait judicieux d'adapter la charge d'entraînement et de renforcer le suivi scolaire.";
        }

        if ($tendance === 'hausse') {
            return "Les résultats scolaires sont en progression, ce qui est encourageant. La charge d'entraînement actuelle semble adaptée.";
        }

        return "L'équilibre sport/études est globalement maintenu. Un suivi régulier permettra d'optimiser les performances dans les deux domaines.";
    }

    private function genererAlertes($athletesAnalyse): array
    {
        $alertes = [];

        foreach ($athletesAnalyse as $analyse) {
            if (!$analyse['alerte']) continue;

            $icon = '⚠️';
            $type = 'warning';
            $message = '';
            $recommandation = '';

            if ($analyse['moyenne'] !== null && $analyse['moyenne'] < 8) {
                $icon = '🚨';
                $type = 'danger';
                $message = "Moyenne scolaire critique ({$analyse['moyenne']}/20)";
                $recommandation = "Réduire les entraînements et renforcer le soutien scolaire";
            } elseif ($analyse['taux_presence'] >= 90 && $analyse['moyenne'] < 10) {
                $message = "Déséquilibre sport/études détecté";
                $recommandation = "Envisager une réduction de la charge d'entraînement";
            } elseif ($analyse['tendance'] === 'baisse') {
                $message = "Baisse des résultats scolaires";
                $recommandation = "Surveiller l'impact de l'entraînement sur les études";
            }

            if ($message) {
                $alertes[] = [
                    'athlete_id' => $analyse['athlete']->id,
                    'athlete' => $analyse['athlete']->nom_complet,
                    'icon' => $icon,
                    'type' => $type,
                    'message' => $message,
                    'recommandation' => $recommandation,
                ];
            }
        }

        return $alertes;
    }

    private function genererRecommandations(array $analyse): array
    {
        $recommandations = [];

        if ($analyse['moyenne'] !== null && $analyse['moyenne'] >= 14) {
            $recommandations[] = [
                'type' => 'success',
                'icon' => '🌟',
                'titre' => 'Excellents résultats scolaires',
                'message' => 'Continuez à maintenir cet excellent niveau. La pratique sportive ne semble pas impacter négativement les études.',
            ];
        }

        if ($analyse['taux_presence'] >= 80) {
            $recommandations[] = [
                'type' => 'success',
                'icon' => '✅',
                'titre' => 'Assiduité exemplaire',
                'message' => 'La régularité aux entraînements est un facteur clé de progression.',
            ];
        }

        if ($analyse['moyenne'] !== null && $analyse['moyenne'] < 10 && $analyse['taux_presence'] >= 80) {
            $recommandations[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'titre' => 'Attention à l\'équilibre',
                'message' => 'Les résultats scolaires nécessitent une attention. Envisagez de réduire temporairement les entraînements.',
            ];
        }

        if ($analyse['tendance'] === 'baisse') {
            $recommandations[] = [
                'type' => 'danger',
                'icon' => '📉',
                'titre' => 'Tendance à la baisse',
                'message' => 'Une baisse des résultats est observée. Un suivi rapproché est recommandé.',
            ];
        }

        if ($analyse['taux_presence'] < 60) {
            $recommandations[] = [
                'type' => 'warning',
                'icon' => '🏃',
                'titre' => 'Assiduité à améliorer',
                'message' => 'Une présence plus régulière aux entraînements favoriserait la progression sportive.',
            ];
        }

        if (empty($recommandations)) {
            $recommandations[] = [
                'type' => 'success',
                'icon' => '👍',
                'titre' => 'Situation équilibrée',
                'message' => 'L\'équilibre entre sport et études est globalement satisfaisant.',
            ];
        }

        return $recommandations;
    }

    private function genererObservations(array $analyse): array
    {
        $observations = [];

        // Observation sur l'assiduité
        if ($analyse['taux_presence'] >= 80) {
            $observations[] = [
                'icon' => '✅',
                'titre' => 'Assiduité',
                'message' => "Votre enfant fait preuve d'une excellente régularité aux entraînements ({$analyse['taux_presence']}% de présence).",
            ];
        } else {
            $observations[] = [
                'icon' => '📊',
                'titre' => 'Assiduité',
                'message' => "Le taux de présence est de {$analyse['taux_presence']}%. Une meilleure régularité favoriserait la progression.",
            ];
        }

        // Observation sur les résultats scolaires
        if ($analyse['moyenne'] !== null) {
            if ($analyse['moyenne'] >= 14) {
                $observations[] = [
                    'icon' => '🌟',
                    'titre' => 'Résultats scolaires',
                    'message' => "Excellents résultats avec une moyenne de {$analyse['moyenne']}/20. Félicitations !",
                ];
            } elseif ($analyse['moyenne'] >= 10) {
                $observations[] = [
                    'icon' => '📚',
                    'titre' => 'Résultats scolaires',
                    'message' => "Résultats satisfaisants avec une moyenne de {$analyse['moyenne']}/20.",
                ];
            } else {
                $observations[] = [
                    'icon' => '⚠️',
                    'titre' => 'Résultats scolaires',
                    'message' => "La moyenne de {$analyse['moyenne']}/20 nécessite une attention particulière.",
                ];
            }
        }

        // Observation sur l'équilibre
        $observations[] = [
            'icon' => '⚖️',
            'titre' => 'Équilibre Sport/Études',
            'message' => $analyse['equilibre_description'],
        ];

        return $observations;
    }

    private function genererRecommandationsParent(array $analyse): array
    {
        $recommandations = [];

        if ($analyse['moyenne'] !== null && $analyse['moyenne'] < 10) {
            $recommandations[] = "Prévoir des temps d'étude réguliers après les entraînements";
            $recommandations[] = "Envisager un soutien scolaire si nécessaire";
        }

        if ($analyse['taux_presence'] >= 90 && ($analyse['moyenne'] === null || $analyse['moyenne'] < 12)) {
            $recommandations[] = "Vérifier que votre enfant dispose de suffisamment de temps pour ses devoirs";
        }

        $recommandations[] = "Encourager votre enfant à maintenir un bon équilibre entre repos, études et sport";
        $recommandations[] = "Veiller à une alimentation équilibrée et un sommeil suffisant";
        
        if ($analyse['tendance'] === 'baisse') {
            $recommandations[] = "Discuter avec votre enfant des éventuelles difficultés rencontrées";
        }

        return $recommandations;
    }

    private function prepareCorrelationData($athletesAnalyse): array
    {
        return $athletesAnalyse->filter(function ($a) {
            return $a['moyenne'] !== null;
        })->map(function ($a) {
            return [
                'x' => round($a['taux_presence'], 1),
                'y' => round($a['moyenne'], 2),
                'name' => $a['athlete']->nom_complet,
            ];
        })->values()->toArray();
    }

    private function prepareNiveauData($athletesAnalyse): array
    {
        $excellent = $athletesAnalyse->filter(fn($a) => $a['moyenne'] !== null && $a['moyenne'] >= 17)->count();
        $tresBien = $athletesAnalyse->filter(fn($a) => $a['moyenne'] !== null && $a['moyenne'] >= 14 && $a['moyenne'] < 17)->count();
        $satisfaisant = $athletesAnalyse->filter(fn($a) => $a['moyenne'] !== null && $a['moyenne'] >= 12 && $a['moyenne'] < 14)->count();
        $passable = $athletesAnalyse->filter(fn($a) => $a['moyenne'] !== null && $a['moyenne'] >= 10 && $a['moyenne'] < 12)->count();
        $insuffisant = $athletesAnalyse->filter(fn($a) => $a['moyenne'] !== null && $a['moyenne'] < 10)->count();

        return compact('excellent', 'tresBien', 'satisfaisant', 'passable', 'insuffisant');
    }

    private function prepareEvolutionData(): array
    {
        $labels = [];
        $presenceData = [];
        $moyenneData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $tauxPresence = Presence::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->avg(DB::raw('CASE WHEN present = 1 THEN 100 ELSE 0 END')) ?? 0;

            $presenceData[] = round($tauxPresence, 1);
            
            // Pour la moyenne, on prend la moyenne des derniers suivis
            $moyenneData[] = SuiviScolaire::whereNotNull('moyenne_generale')
                ->avg('moyenne_generale') ?? 0;
        }

        return [
            'labels' => $labels,
            'presence' => $presenceData,
            'moyenne' => $moyenneData,
        ];
    }

    private function prepareEvolutionDataAthlete(Athlete $athlete): array
    {
        $labels = [];
        $presenceData = [];
        $moyenneData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $presences = $athlete->presences()
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->get();

            $total = $presences->count();
            $presents = $presences->where('present', true)->count();
            $presenceData[] = $total > 0 ? round(($presents / $total) * 100, 1) : 0;

            $suivi = $athlete->suivisScolaires->sortByDesc('annee_scolaire')->first();
            $moyenneData[] = $suivi?->moyenne_generale ?? 0;
        }

        return [
            'labels' => $labels,
            'presence' => $presenceData,
            'moyenne' => $moyenneData,
        ];
    }
}

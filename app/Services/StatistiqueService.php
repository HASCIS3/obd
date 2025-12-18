<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Paiement;
use App\Models\Performance;
use App\Models\Presence;
use App\Models\SuiviScolaire;
use Carbon\Carbon;

class StatistiqueService
{
    /**
     * Récupère les statistiques globales pour le dashboard
     */
    public function getDashboardStats(): array
    {
        return [
            'athletes' => $this->getStatsAthletes(),
            'coachs' => $this->getStatsCoachs(),
            'disciplines' => $this->getStatsDisciplines(),
            'paiements' => $this->getStatsPaiements(),
            'presences' => $this->getStatsPresences(),
        ];
    }

    /**
     * Statistiques des athlètes
     */
    public function getStatsAthletes(): array
    {
        return [
            'total' => Athlete::count(),
            'actifs' => Athlete::actifs()->count(),
            'inactifs' => Athlete::inactifs()->count(),
            'avec_arrieres' => Athlete::avecArrieres()->count(),
            'hommes' => Athlete::actifs()->parSexe('M')->count(),
            'femmes' => Athlete::actifs()->parSexe('F')->count(),
            'mineurs' => Athlete::actifs()->mineurs()->count(),
            'majeurs' => Athlete::actifs()->majeurs()->count(),
            'nouveaux_mois' => Athlete::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Statistiques des coachs
     */
    public function getStatsCoachs(): array
    {
        return [
            'total' => Coach::count(),
            'actifs' => Coach::actifs()->count(),
            'inactifs' => Coach::inactifs()->count(),
        ];
    }

    /**
     * Statistiques des disciplines
     */
    public function getStatsDisciplines(): array
    {
        $disciplines = Discipline::actives()->withCount(['athletes' => function ($q) {
            $q->where('athlete_discipline.actif', true);
        }])->get();

        return [
            'total' => Discipline::count(),
            'actives' => Discipline::actives()->count(),
            'plus_populaire' => $disciplines->sortByDesc('athletes_count')->first()?->nom,
            'revenus_potentiels' => $disciplines->sum(fn($d) => $d->athletes_count * $d->tarif_mensuel),
        ];
    }

    /**
     * Statistiques des paiements
     */
    public function getStatsPaiements(): array
    {
        $moisCourant = Paiement::moisCourant();

        return [
            'encaissements_mois' => Paiement::whereMonth('date_paiement', now()->month)
                ->whereYear('date_paiement', now()->year)
                ->sum('montant_paye'),
            'arrieres_total' => Paiement::arrieres()->get()->sum(fn($p) => $p->reste_a_payer),
            'nb_impayes' => Paiement::impayes()->count(),
            'nb_partiels' => Paiement::partiels()->count(),
            'taux_recouvrement_mois' => $this->calculerTauxRecouvrement(now()->month, now()->year),
        ];
    }

    /**
     * Statistiques des présences
     */
    public function getStatsPresences(): array
    {
        $presencesMois = Presence::moisCourant()->get();
        $stats = Presence::calculerStatistiques($presencesMois);

        return array_merge($stats, [
            'presences_aujourd_hui' => Presence::pourDate(now()->format('Y-m-d'))->presents()->count(),
            'absences_aujourd_hui' => Presence::pourDate(now()->format('Y-m-d'))->absents()->count(),
        ]);
    }

    /**
     * Calcule le taux de recouvrement pour un mois
     */
    public function calculerTauxRecouvrement(int $mois, int $annee): float
    {
        $paiements = Paiement::pourPeriode($mois, $annee)->get();
        $totalDu = $paiements->sum('montant');
        $totalPaye = $paiements->sum('montant_paye');

        return $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0;
    }

    /**
     * Génère un rapport mensuel complet
     */
    public function genererRapportMensuel(int $mois, int $annee): array
    {
        $dateDebut = Carbon::create($annee, $mois, 1)->startOfMonth();
        $dateFin = Carbon::create($annee, $mois, 1)->endOfMonth();

        return [
            'periode' => [
                'mois' => $mois,
                'annee' => $annee,
                'libelle' => Paiement::mois()[$mois] . ' ' . $annee,
            ],
            'athletes' => [
                'total_actifs' => Athlete::actifs()->count(),
                'nouveaux' => Athlete::whereBetween('created_at', [$dateDebut, $dateFin])->count(),
            ],
            'presences' => $this->getStatsPresencesPeriode($dateDebut, $dateFin),
            'paiements' => $this->getStatsPaiementsPeriode($mois, $annee),
            'performances' => $this->getStatsPerformancesPeriode($dateDebut, $dateFin),
        ];
    }

    /**
     * Statistiques de présences pour une période
     */
    public function getStatsPresencesPeriode(Carbon $dateDebut, Carbon $dateFin): array
    {
        $presences = Presence::pourPeriode($dateDebut, $dateFin)->get();
        $stats = Presence::calculerStatistiques($presences);

        // Par discipline
        $parDiscipline = $presences->groupBy('discipline_id')->map(function ($group) {
            return Presence::calculerStatistiques($group);
        });

        return array_merge($stats, [
            'par_discipline' => $parDiscipline,
        ]);
    }

    /**
     * Statistiques de paiements pour une période
     */
    public function getStatsPaiementsPeriode(int $mois, int $annee): array
    {
        $paiements = Paiement::pourPeriode($mois, $annee)->get();

        return [
            'total_du' => $paiements->sum('montant'),
            'total_paye' => $paiements->sum('montant_paye'),
            'total_arrieres' => $paiements->sum(fn($p) => $p->reste_a_payer),
            'nb_payes' => $paiements->where('statut', Paiement::STATUT_PAYE)->count(),
            'nb_partiels' => $paiements->where('statut', Paiement::STATUT_PARTIEL)->count(),
            'nb_impayes' => $paiements->where('statut', Paiement::STATUT_IMPAYE)->count(),
            'taux_recouvrement' => $this->calculerTauxRecouvrement($mois, $annee),
        ];
    }

    /**
     * Statistiques de performances pour une période
     */
    public function getStatsPerformancesPeriode(Carbon $dateDebut, Carbon $dateFin): array
    {
        $performances = Performance::pourPeriode($dateDebut, $dateFin)->get();

        return [
            'total' => $performances->count(),
            'en_competition' => $performances->whereNotNull('competition')->count(),
            'podiums' => $performances->filter(fn($p) => $p->est_podium)->count(),
        ];
    }

    /**
     * Récupère les tendances (comparaison avec le mois précédent)
     */
    public function getTendances(): array
    {
        $moisCourant = now()->month;
        $anneeCourante = now()->year;
        $moisPrecedent = now()->subMonth()->month;
        $anneePrecedente = now()->subMonth()->year;

        // Athlètes
        $athletesCourant = Athlete::whereMonth('created_at', $moisCourant)
            ->whereYear('created_at', $anneeCourante)->count();
        $athletesPrecedent = Athlete::whereMonth('created_at', $moisPrecedent)
            ->whereYear('created_at', $anneePrecedente)->count();

        // Paiements
        $paiementsCourant = Paiement::whereMonth('date_paiement', $moisCourant)
            ->whereYear('date_paiement', $anneeCourante)->sum('montant_paye');
        $paiementsPrecedent = Paiement::whereMonth('date_paiement', $moisPrecedent)
            ->whereYear('date_paiement', $anneePrecedente)->sum('montant_paye');

        // Présences
        $presencesCourant = Presence::moisCourant()->presents()->count();
        $presencesPrecedent = Presence::pourMois($moisPrecedent, $anneePrecedente)->presents()->count();

        return [
            'athletes' => $this->calculerTendance($athletesCourant, $athletesPrecedent),
            'paiements' => $this->calculerTendance($paiementsCourant, $paiementsPrecedent),
            'presences' => $this->calculerTendance($presencesCourant, $presencesPrecedent),
        ];
    }

    /**
     * Calcule la tendance en pourcentage
     */
    private function calculerTendance($valeurCourante, $valeurPrecedente): array
    {
        if ($valeurPrecedente == 0) {
            $pourcentage = $valeurCourante > 0 ? 100 : 0;
        } else {
            $pourcentage = round((($valeurCourante - $valeurPrecedente) / $valeurPrecedente) * 100, 1);
        }

        return [
            'valeur_courante' => $valeurCourante,
            'valeur_precedente' => $valeurPrecedente,
            'pourcentage' => $pourcentage,
            'tendance' => $pourcentage > 0 ? 'hausse' : ($pourcentage < 0 ? 'baisse' : 'stable'),
        ];
    }

    /**
     * Récupère les données pour les graphiques du dashboard
     */
    public function getDonneesGraphiques(): array
    {
        return [
            'presences_semaine' => $this->getPresencesSemaine(),
            'paiements_6_mois' => $this->getPaiements6Mois(),
            'repartition_disciplines' => $this->getRepartitionDisciplines(),
        ];
    }

    /**
     * Présences des 7 derniers jours
     */
    private function getPresencesSemaine(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $presences = Presence::pourDate($date->format('Y-m-d'))->get();
            $data[] = [
                'date' => $date->format('d/m'),
                'presents' => $presences->where('present', true)->count(),
                'absents' => $presences->where('present', false)->count(),
            ];
        }
        return $data;
    }

    /**
     * Paiements des 6 derniers mois
     */
    private function getPaiements6Mois(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $paiements = Paiement::whereMonth('date_paiement', $date->month)
                ->whereYear('date_paiement', $date->year)
                ->sum('montant_paye');
            $data[] = [
                'mois' => $date->format('M Y'),
                'montant' => $paiements,
            ];
        }
        return $data;
    }

    /**
     * Répartition des athlètes par discipline
     */
    private function getRepartitionDisciplines(): array
    {
        return Discipline::actives()
            ->withCount(['athletes' => fn($q) => $q->where('athlete_discipline.actif', true)])
            ->get()
            ->map(fn($d) => [
                'discipline' => $d->nom,
                'count' => $d->athletes_count,
            ])
            ->toArray();
    }
}

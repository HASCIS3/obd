<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

/**
 * Service d'orchestration des workflows métier
 * Assure la cohérence entre les différents modules
 */
class WorkflowService
{
    public function __construct(
        protected AthleteService $athleteService,
        protected PaiementService $paiementService,
        protected PresenceService $presenceService,
        protected PerformanceService $performanceService,
        protected SuiviScolaireService $suiviScolaireService
    ) {}

    /**
     * Workflow d'inscription d'un nouvel athlète
     * - Crée l'athlète
     * - L'inscrit aux disciplines
     * - Génère le premier paiement
     */
    public function inscrireNouvelAthlete(array $athleteData, array $disciplineIds = []): Athlete
    {
        return DB::transaction(function () use ($athleteData, $disciplineIds) {
            // Créer l'athlète
            $athlete = $this->athleteService->creer($athleteData);

            // Inscrire aux disciplines
            if (!empty($disciplineIds)) {
                $this->athleteService->attacherDisciplines($athlete, $disciplineIds);
            }

            // Générer le paiement du mois en cours si des disciplines sont sélectionnées
            if (!empty($disciplineIds)) {
                $this->paiementService->genererPaiementAthlete($athlete, now()->month, now()->year);
            }

            return $athlete->fresh(['disciplines', 'paiements']);
        });
    }

    /**
     * Workflow de changement de discipline pour un athlète
     * - Met à jour les disciplines
     * - Recalcule le paiement du mois si nécessaire
     */
    public function changerDisciplinesAthlete(Athlete $athlete, array $nouvellesDisciplineIds): Athlete
    {
        return DB::transaction(function () use ($athlete, $nouvellesDisciplineIds) {
            // Synchroniser les disciplines
            $this->athleteService->synchroniserDisciplines($athlete, $nouvellesDisciplineIds);

            // Vérifier si un paiement du mois existe et le mettre à jour
            $paiementMois = $athlete->paiements()
                ->pourPeriode(now()->month, now()->year)
                ->first();

            if ($paiementMois && $paiementMois->statut !== Paiement::STATUT_PAYE) {
                // Recalculer le montant
                $nouveauMontant = $athlete->fresh()->getTarifMensuelTotal();
                $paiementMois->update(['montant' => $nouveauMontant]);
            }

            return $athlete->fresh(['disciplines']);
        });
    }

    /**
     * Workflow de désactivation d'un athlète
     * - Désactive l'athlète
     * - Désactive ses inscriptions aux disciplines
     */
    public function desactiverAthlete(Athlete $athlete): bool
    {
        return DB::transaction(function () use ($athlete) {
            // Désactiver toutes les inscriptions aux disciplines
            $athlete->disciplines()->updateExistingPivot(
                $athlete->disciplines->pluck('id')->toArray(),
                ['actif' => false]
            );

            // Désactiver l'athlète
            return $this->athleteService->desactiver($athlete);
        });
    }

    /**
     * Workflow de réactivation d'un athlète
     */
    public function reactiverAthlete(Athlete $athlete, array $disciplineIds = []): Athlete
    {
        return DB::transaction(function () use ($athlete, $disciplineIds) {
            // Réactiver l'athlète
            $this->athleteService->reactiver($athlete);

            // Réactiver les disciplines spécifiées ou toutes
            if (!empty($disciplineIds)) {
                $athlete->disciplines()->updateExistingPivot($disciplineIds, ['actif' => true]);
            }

            // Générer le paiement du mois si nécessaire
            $this->paiementService->genererPaiementAthlete($athlete, now()->month, now()->year);

            return $athlete->fresh(['disciplines']);
        });
    }

    /**
     * Workflow de génération des paiements mensuels
     * - Génère les paiements pour tous les athlètes actifs
     * - Retourne le nombre de paiements créés
     */
    public function genererPaiementsMensuels(int $mois, int $annee): array
    {
        $count = $this->paiementService->genererPaiementsMensuels($mois, $annee);

        return [
            'count' => $count,
            'mois' => $mois,
            'annee' => $annee,
        ];
    }

    /**
     * Workflow de vérification d'éligibilité d'un athlète
     * Vérifie les critères : paiements, présences, résultats scolaires
     */
    public function verifierEligibilite(Athlete $athlete): array
    {
        $resultats = [
            'eligible' => true,
            'raisons' => [],
        ];

        // Vérification des arriérés
        $arrieres = $this->athleteService->calculerArrieres($athlete);
        if ($arrieres >= 50000) {
            $resultats['eligible'] = false;
            $resultats['raisons'][] = "Arriérés de paiement importants ({$arrieres} FCFA)";
        }

        // Vérification du taux de présence (minimum 50%)
        $statsPresence = $this->presenceService->getStatistiquesAthlete($athlete);
        if ($statsPresence['total'] > 10 && $statsPresence['taux'] < 50) {
            $resultats['eligible'] = false;
            $resultats['raisons'][] = "Taux de présence insuffisant ({$statsPresence['taux']}%)";
        }

        // Vérification des résultats scolaires
        $suiviScolaire = $athlete->suiviScolaire;
        if ($suiviScolaire && !$this->suiviScolaireService->estEligibleScolairement($athlete)) {
            $resultats['eligible'] = false;
            $resultats['raisons'][] = "Résultats scolaires insuffisants";
        }

        return $resultats;
    }

    /**
     * Workflow de saisie des présences pour une séance
     */
    public function enregistrerSeance(
        int $disciplineId,
        string $date,
        array $presences,
        ?Coach $coach = null
    ): array {
        $coachId = $coach?->id;

        // Vérifier que le coach peut gérer cette discipline
        if ($coach && !$coach->peutGererDiscipline(Discipline::find($disciplineId))) {
            throw new \Exception("Ce coach n'est pas autorisé à gérer cette discipline.");
        }

        $count = $this->presenceService->enregistrerPresences(
            $presences,
            $disciplineId,
            $date,
            $coachId
        );

        return [
            'count' => $count,
            'discipline_id' => $disciplineId,
            'date' => $date,
        ];
    }

    /**
     * Workflow de régularisation d'un paiement
     */
    public function regulariserPaiement(
        Paiement $paiement,
        float $montant,
        string $modePaiement = 'especes',
        ?string $reference = null
    ): Paiement {
        return $this->paiementService->enregistrerPaiement(
            $paiement,
            $montant,
            $modePaiement,
            $reference
        );
    }

    /**
     * Récupère un résumé complet d'un athlète
     */
    public function getResumeAthlete(Athlete $athlete): array
    {
        return [
            'athlete' => $athlete,
            'statistiques' => $this->athleteService->calculerStatistiques($athlete),
            'eligibilite' => $this->verifierEligibilite($athlete),
            'presences' => $this->presenceService->getStatistiquesAthlete($athlete),
            'performances' => $this->performanceService->getStatistiquesAthlete($athlete),
            'paiements' => [
                'arrieres' => $this->paiementService->calculerArrieresAthlete($athlete),
                'historique' => $this->paiementService->getHistoriqueAthlete($athlete)->take(12),
            ],
        ];
    }

    /**
     * Récupère les alertes pour le dashboard admin
     */
    public function getAlertes(): array
    {
        $alertes = [];

        // Athlètes avec arriérés importants
        $athletesArrieres = Athlete::avecArrieres()
            ->actifs()
            ->get()
            ->filter(fn($a) => $a->arrieres >= 30000);

        if ($athletesArrieres->count() > 0) {
            $alertes[] = [
                'type' => 'danger',
                'message' => "{$athletesArrieres->count()} athlète(s) avec arriérés importants",
                'link' => route('paiements.arrieres'),
            ];
        }

        // Disciplines sans coach
        $disciplinesSansCoach = Discipline::actives()->sansCoach()->count();
        if ($disciplinesSansCoach > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$disciplinesSansCoach} discipline(s) sans coach assigné",
                'link' => route('disciplines.index'),
            ];
        }

        // Athlètes en difficulté scolaire
        $enDifficulte = $this->suiviScolaireService->getAthletesEnDifficulte()->count();
        if ($enDifficulte > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$enDifficulte} athlète(s) en difficulté scolaire",
                'link' => route('suivis-scolaires.index'),
            ];
        }

        return $alertes;
    }
}

<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PaiementService
{
    /**
     * Crée un nouveau paiement
     */
    public function creer(array $data): Paiement
    {
        // Déterminer le statut automatiquement
        $data['statut'] = $this->determinerStatut($data['montant'], $data['montant_paye']);

        // Si paiement effectué, mettre la date si non fournie
        if ($data['montant_paye'] > 0 && empty($data['date_paiement'])) {
            $data['date_paiement'] = now();
        }

        return Paiement::create($data);
    }

    /**
     * Met à jour un paiement
     */
    public function mettreAJour(Paiement $paiement, array $data): Paiement
    {
        // Recalculer le statut
        $data['statut'] = $this->determinerStatut($data['montant'], $data['montant_paye']);

        $paiement->update($data);

        return $paiement->fresh();
    }

    /**
     * Enregistre un paiement partiel ou complet
     */
    public function enregistrerPaiement(Paiement $paiement, float $montant, string $modePaiement = 'especes', ?string $reference = null): Paiement
    {
        $nouveauMontantPaye = $paiement->montant_paye + $montant;

        // Ne pas dépasser le montant dû
        if ($nouveauMontantPaye > $paiement->montant) {
            $nouveauMontantPaye = $paiement->montant;
        }

        $paiement->update([
            'montant_paye' => $nouveauMontantPaye,
            'statut' => $this->determinerStatut($paiement->montant, $nouveauMontantPaye),
            'mode_paiement' => $modePaiement,
            'date_paiement' => now(),
            'reference' => $reference ?? $paiement->reference,
        ]);

        return $paiement->fresh();
    }

    /**
     * Détermine le statut en fonction des montants
     */
    public function determinerStatut(float $montant, float $montantPaye): string
    {
        if ($montantPaye >= $montant) {
            return Paiement::STATUT_PAYE;
        } elseif ($montantPaye > 0) {
            return Paiement::STATUT_PARTIEL;
        }
        return Paiement::STATUT_IMPAYE;
    }

    /**
     * Génère les paiements mensuels pour tous les athlètes actifs
     */
    public function genererPaiementsMensuels(int $mois, int $annee): int
    {
        $athletes = Athlete::where('actif', true)
            ->with('disciplines')
            ->get();

        $count = 0;

        foreach ($athletes as $athlete) {
            // Calculer le montant total basé sur les disciplines actives
            $montant = $athlete->disciplines()
                ->wherePivot('actif', true)
                ->sum('tarif_mensuel');

            if ($montant > 0) {
                // Vérifier si le paiement existe déjà
                $exists = Paiement::where('athlete_id', $athlete->id)
                    ->where('mois', $mois)
                    ->where('annee', $annee)
                    ->exists();

                if (!$exists) {
                    Paiement::create([
                        'athlete_id' => $athlete->id,
                        'montant' => $montant,
                        'montant_paye' => 0,
                        'mois' => $mois,
                        'annee' => $annee,
                        'statut' => Paiement::STATUT_IMPAYE,
                        'mode_paiement' => Paiement::MODE_ESPECES,
                    ]);
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Génère un paiement pour un athlète spécifique
     */
    public function genererPaiementAthlete(Athlete $athlete, int $mois, int $annee): ?Paiement
    {
        // Vérifier si le paiement existe déjà
        $existant = Paiement::where('athlete_id', $athlete->id)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->first();

        if ($existant) {
            return null;
        }

        // Calculer le montant
        $montant = $athlete->disciplines()
            ->wherePivot('actif', true)
            ->sum('tarif_mensuel');

        if ($montant <= 0) {
            return null;
        }

        return Paiement::create([
            'athlete_id' => $athlete->id,
            'montant' => $montant,
            'montant_paye' => 0,
            'mois' => $mois,
            'annee' => $annee,
            'statut' => Paiement::STATUT_IMPAYE,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);
    }

    /**
     * Récupère tous les arriérés groupés par athlète
     */
    public function getArrieres(): array
    {
        $arrieres = Paiement::with('athlete')
            ->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL])
            ->orderBy('annee')
            ->orderBy('mois')
            ->get()
            ->groupBy('athlete_id');

        $athletes = Athlete::whereIn('id', $arrieres->keys())->get()->keyBy('id');

        return [
            'arrieres' => $arrieres,
            'athletes' => $athletes,
        ];
    }

    /**
     * Calcule le total des arriérés
     */
    public function calculerTotalArrieres(): float
    {
        return Paiement::whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL])
            ->get()
            ->sum(fn($p) => $p->montant - $p->montant_paye);
    }

    /**
     * Calcule les arriérés d'un athlète
     */
    public function calculerArrieresAthlete(Athlete $athlete): float
    {
        return $athlete->paiements()
            ->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL])
            ->get()
            ->sum(fn($p) => $p->montant - $p->montant_paye);
    }

    /**
     * Récupère les statistiques de paiement
     */
    public function getStatistiques(?int $mois = null, ?int $annee = null): array
    {
        $mois = $mois ?? now()->month;
        $annee = $annee ?? now()->year;

        $query = Paiement::query();

        // Encaissements du mois
        $encaissementsMois = (clone $query)
            ->whereMonth('date_paiement', $mois)
            ->whereYear('date_paiement', $annee)
            ->sum('montant_paye');

        // Total des arriérés
        $arrieres = $this->calculerTotalArrieres();

        // Nombre d'impayés
        $nbImpayes = Paiement::where('statut', Paiement::STATUT_IMPAYE)->count();

        // Paiements du mois
        $paiementsMois = Paiement::where('mois', $mois)
            ->where('annee', $annee)
            ->get();

        $totalDu = $paiementsMois->sum('montant');
        $totalPaye = $paiementsMois->sum('montant_paye');

        return [
            'encaissements_mois' => $encaissementsMois,
            'arrieres' => $arrieres,
            'nb_impayes' => $nbImpayes,
            'total_du_mois' => $totalDu,
            'total_paye_mois' => $totalPaye,
            'taux_recouvrement' => $totalDu > 0 ? round(($totalPaye / $totalDu) * 100, 1) : 0,
        ];
    }

    /**
     * Récupère l'historique des paiements d'un athlète
     */
    public function getHistoriqueAthlete(Athlete $athlete): Collection
    {
        return $athlete->paiements()
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
    }

    /**
     * Envoie un rappel pour les arriérés (placeholder pour notification)
     */
    public function envoyerRappelArrieres(Athlete $athlete): bool
    {
        // TODO: Implémenter l'envoi de notification (email, SMS)
        // Pour l'instant, on retourne true pour indiquer que l'action est possible
        return true;
    }

    /**
     * Annule un paiement
     */
    public function annuler(Paiement $paiement): bool
    {
        return $paiement->update([
            'montant_paye' => 0,
            'statut' => Paiement::STATUT_IMPAYE,
            'date_paiement' => null,
            'reference' => null,
        ]);
    }
}

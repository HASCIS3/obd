<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\SuiviScolaire;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SuiviScolaireService
{
    public const SEUIL_SATISFAISANT = 10;
    public const SEUIL_EXCELLENT = 14;
    public const SEUIL_INSUFFISANT = 8;

    /**
     * Crée un nouveau suivi scolaire
     */
    public function creer(array $data, ?UploadedFile $bulletin = null): SuiviScolaire
    {
        if ($bulletin) {
            $data['bulletin_path'] = $bulletin->store('bulletins', 'public');
        }

        return SuiviScolaire::create($data);
    }

    /**
     * Met à jour un suivi scolaire
     */
    public function mettreAJour(SuiviScolaire $suivi, array $data, ?UploadedFile $bulletin = null): SuiviScolaire
    {
        if ($bulletin) {
            // Supprimer l'ancien bulletin
            if ($suivi->bulletin_path) {
                Storage::disk('public')->delete($suivi->bulletin_path);
            }
            $data['bulletin_path'] = $bulletin->store('bulletins', 'public');
        }

        $suivi->update($data);

        return $suivi->fresh();
    }

    /**
     * Supprime un suivi scolaire
     */
    public function supprimer(SuiviScolaire $suivi): bool
    {
        // Supprimer le bulletin
        if ($suivi->bulletin_path) {
            Storage::disk('public')->delete($suivi->bulletin_path);
        }

        return $suivi->delete();
    }

    /**
     * Récupère les statistiques scolaires globales
     */
    public function getStatistiquesGlobales(?string $anneeScolaire = null): array
    {
        $query = SuiviScolaire::query();

        if ($anneeScolaire) {
            $query->where('annee_scolaire', $anneeScolaire);
        }

        $suivis = $query->get();

        if ($suivis->isEmpty()) {
            return [
                'total' => 0,
                'satisfaisants' => 0,
                'insuffisants' => 0,
                'excellents' => 0,
                'moyenne_generale' => null,
                'taux_reussite' => 0,
            ];
        }

        $avecMoyenne = $suivis->whereNotNull('moyenne_generale');

        return [
            'total' => $suivis->count(),
            'satisfaisants' => $avecMoyenne->where('moyenne_generale', '>=', self::SEUIL_SATISFAISANT)->count(),
            'insuffisants' => $avecMoyenne->where('moyenne_generale', '<', self::SEUIL_SATISFAISANT)->count(),
            'excellents' => $avecMoyenne->where('moyenne_generale', '>=', self::SEUIL_EXCELLENT)->count(),
            'moyenne_generale' => $avecMoyenne->isNotEmpty() ? round($avecMoyenne->avg('moyenne_generale'), 2) : null,
            'taux_reussite' => $avecMoyenne->isNotEmpty()
                ? round(($avecMoyenne->where('moyenne_generale', '>=', self::SEUIL_SATISFAISANT)->count() / $avecMoyenne->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Récupère les athlètes en difficulté scolaire
     */
    public function getAthletesEnDifficulte(?string $anneeScolaire = null): Collection
    {
        $query = SuiviScolaire::with('athlete')
            ->where('moyenne_generale', '<', self::SEUIL_INSUFFISANT);

        if ($anneeScolaire) {
            $query->where('annee_scolaire', $anneeScolaire);
        }

        return $query->orderBy('moyenne_generale')->get();
    }

    /**
     * Récupère les meilleurs élèves
     */
    public function getMeilleursEleves(?string $anneeScolaire = null, int $limit = 10): Collection
    {
        $query = SuiviScolaire::with('athlete')
            ->whereNotNull('moyenne_generale');

        if ($anneeScolaire) {
            $query->where('annee_scolaire', $anneeScolaire);
        }

        return $query->orderByDesc('moyenne_generale')
            ->take($limit)
            ->get();
    }

    /**
     * Vérifie si un athlète est éligible selon ses résultats scolaires
     */
    public function estEligibleScolairement(Athlete $athlete): bool
    {
        $dernierSuivi = $athlete->suiviScolaire;

        if (!$dernierSuivi || !$dernierSuivi->moyenne_generale) {
            return true; // Pas de données = éligible par défaut
        }

        return $dernierSuivi->moyenne_generale >= self::SEUIL_INSUFFISANT;
    }

    /**
     * Récupère l'évolution scolaire d'un athlète
     */
    public function getEvolutionAthlete(Athlete $athlete): Collection
    {
        return SuiviScolaire::where('athlete_id', $athlete->id)
            ->orderBy('annee_scolaire')
            ->get();
    }

    /**
     * Récupère les années scolaires disponibles
     */
    public function getAnneesScolaires(): Collection
    {
        return SuiviScolaire::distinct()
            ->pluck('annee_scolaire')
            ->filter()
            ->sort()
            ->reverse()
            ->values();
    }

    /**
     * Génère un rapport scolaire pour une année
     */
    public function genererRapport(string $anneeScolaire): array
    {
        $suivis = SuiviScolaire::with('athlete')
            ->where('annee_scolaire', $anneeScolaire)
            ->get();

        $parClasse = $suivis->groupBy('classe')->map(function ($group) {
            $avecMoyenne = $group->whereNotNull('moyenne_generale');
            return [
                'effectif' => $group->count(),
                'moyenne' => $avecMoyenne->isNotEmpty() ? round($avecMoyenne->avg('moyenne_generale'), 2) : null,
                'reussite' => $avecMoyenne->isNotEmpty()
                    ? round(($avecMoyenne->where('moyenne_generale', '>=', self::SEUIL_SATISFAISANT)->count() / $avecMoyenne->count()) * 100, 1)
                    : 0,
            ];
        });

        $parEtablissement = $suivis->groupBy('etablissement')->map(function ($group) {
            $avecMoyenne = $group->whereNotNull('moyenne_generale');
            return [
                'effectif' => $group->count(),
                'moyenne' => $avecMoyenne->isNotEmpty() ? round($avecMoyenne->avg('moyenne_generale'), 2) : null,
            ];
        });

        return [
            'annee_scolaire' => $anneeScolaire,
            'statistiques' => $this->getStatistiquesGlobales($anneeScolaire),
            'par_classe' => $parClasse,
            'par_etablissement' => $parEtablissement,
            'en_difficulte' => $this->getAthletesEnDifficulte($anneeScolaire),
            'meilleurs' => $this->getMeilleursEleves($anneeScolaire),
        ];
    }

    /**
     * Évalue le niveau scolaire
     */
    public function evaluerNiveau(?float $moyenne): string
    {
        if ($moyenne === null) {
            return 'Non évalué';
        }

        if ($moyenne >= self::SEUIL_EXCELLENT) {
            return 'Excellent';
        } elseif ($moyenne >= self::SEUIL_SATISFAISANT) {
            return 'Satisfaisant';
        } elseif ($moyenne >= self::SEUIL_INSUFFISANT) {
            return 'Passable';
        }

        return 'Insuffisant';
    }
}

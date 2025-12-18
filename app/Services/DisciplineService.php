<?php

namespace App\Services;

use App\Models\Discipline;
use Illuminate\Database\Eloquent\Collection;

class DisciplineService
{
    /**
     * Crée une nouvelle discipline
     */
    public function creer(array $data): Discipline
    {
        $data['actif'] = true;
        return Discipline::create($data);
    }

    /**
     * Met à jour une discipline
     */
    public function mettreAJour(Discipline $discipline, array $data): Discipline
    {
        $discipline->update($data);
        return $discipline->fresh();
    }

    /**
     * Désactive une discipline
     */
    public function desactiver(Discipline $discipline): bool
    {
        return $discipline->update(['actif' => false]);
    }

    /**
     * Réactive une discipline
     */
    public function reactiver(Discipline $discipline): bool
    {
        return $discipline->update(['actif' => true]);
    }

    /**
     * Supprime une discipline si possible
     */
    public function supprimer(Discipline $discipline): bool|string
    {
        // Vérifier s'il y a des athlètes inscrits
        if ($discipline->athletes()->count() > 0) {
            return 'Impossible de supprimer cette discipline car des athlètes y sont inscrits.';
        }

        // Vérifier s'il y a des coachs assignés
        if ($discipline->coachs()->count() > 0) {
            return 'Impossible de supprimer cette discipline car des coachs y sont assignés.';
        }

        return $discipline->delete();
    }

    /**
     * Récupère les statistiques d'une discipline
     */
    public function getStatistiques(Discipline $discipline): array
    {
        $presencesMois = $discipline->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        $totalPresences = $presencesMois->count();
        $presents = (clone $presencesMois)->where('present', true)->count();

        return [
            'athletes_actifs' => $discipline->athletes()->wherePivot('actif', true)->count(),
            'athletes_total' => $discipline->athletes()->count(),
            'coachs_count' => $discipline->coachs()->count(),
            'presences_mois' => $totalPresences,
            'presents_mois' => $presents,
            'absents_mois' => $totalPresences - $presents,
            'taux_presence' => $totalPresences > 0 ? round(($presents / $totalPresences) * 100, 1) : 0,
            'performances_count' => $discipline->performances()->count(),
            'revenus_potentiels' => $discipline->athletes()->wherePivot('actif', true)->count() * $discipline->tarif_mensuel,
        ];
    }

    /**
     * Récupère les disciplines actives
     */
    public function getDisciplinesActives(): Collection
    {
        return Discipline::where('actif', true)->orderBy('nom')->get();
    }

    /**
     * Récupère les disciplines avec leurs compteurs
     */
    public function getDisciplinesAvecCompteurs(): Collection
    {
        return Discipline::withCount(['athletes', 'coachs'])
            ->orderBy('nom')
            ->get();
    }

    /**
     * Récupère le classement des disciplines par nombre d'athlètes
     */
    public function getClassementParPopularite(): Collection
    {
        return Discipline::withCount(['athletes' => function ($query) {
            $query->where('athlete_discipline.actif', true);
        }])
            ->where('actif', true)
            ->orderByDesc('athletes_count')
            ->get();
    }

    /**
     * Calcule les revenus potentiels mensuels
     */
    public function calculerRevenusPotentiels(): float
    {
        return Discipline::where('actif', true)
            ->get()
            ->sum(function ($discipline) {
                return $discipline->athletes()->wherePivot('actif', true)->count() * $discipline->tarif_mensuel;
            });
    }

    /**
     * Récupère les disciplines sans coach
     */
    public function getDisciplinesSansCoach(): Collection
    {
        return Discipline::where('actif', true)
            ->whereDoesntHave('coachs')
            ->get();
    }

    /**
     * Récupère les disciplines sans athlète
     */
    public function getDisciplinesSansAthlete(): Collection
    {
        return Discipline::where('actif', true)
            ->whereDoesntHave('athletes', function ($query) {
                $query->where('athlete_discipline.actif', true);
            })
            ->get();
    }

    /**
     * Met à jour le tarif d'une discipline
     */
    public function mettreAJourTarif(Discipline $discipline, float $nouveauTarif): bool
    {
        return $discipline->update(['tarif_mensuel' => $nouveauTarif]);
    }

    /**
     * Recherche de disciplines
     */
    public function rechercher(array $criteres): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Discipline::withCount(['athletes', 'coachs']);

        if (!empty($criteres['search'])) {
            $query->where('nom', 'like', "%{$criteres['search']}%");
        }

        if (isset($criteres['actif'])) {
            $query->where('actif', $criteres['actif']);
        }

        if (!empty($criteres['tarif_min'])) {
            $query->where('tarif_mensuel', '>=', $criteres['tarif_min']);
        }

        if (!empty($criteres['tarif_max'])) {
            $query->where('tarif_mensuel', '<=', $criteres['tarif_max']);
        }

        return $query->orderBy('nom')
            ->paginate($criteres['per_page'] ?? 15)
            ->withQueryString();
    }
}

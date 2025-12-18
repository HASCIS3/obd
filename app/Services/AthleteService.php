<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AthleteService
{
    /**
     * Crée un nouvel athlète avec ses disciplines
     */
    public function creer(array $data, ?UploadedFile $photo = null): Athlete
    {
        return DB::transaction(function () use ($data, $photo) {
            // Gestion de la photo
            if ($photo) {
                $data['photo'] = $photo->store('athletes', 'public');
            }

            $data['date_inscription'] = $data['date_inscription'] ?? now();
            $data['actif'] = true;

            $athlete = Athlete::create($data);

            // Attacher les disciplines avec date d'inscription
            if (!empty($data['disciplines'])) {
                $this->attacherDisciplines($athlete, $data['disciplines']);
            }

            return $athlete;
        });
    }

    /**
     * Met à jour un athlète
     */
    public function mettreAJour(Athlete $athlete, array $data, ?UploadedFile $photo = null): Athlete
    {
        return DB::transaction(function () use ($athlete, $data, $photo) {
            // Gestion de la photo
            if ($photo) {
                // Supprimer l'ancienne photo
                if ($athlete->photo) {
                    Storage::disk('public')->delete($athlete->photo);
                }
                $data['photo'] = $photo->store('athletes', 'public');
            }

            $athlete->update($data);

            // Synchroniser les disciplines
            if (isset($data['disciplines'])) {
                $this->synchroniserDisciplines($athlete, $data['disciplines']);
            }

            return $athlete->fresh();
        });
    }

    /**
     * Désactive un athlète (soft delete logique)
     */
    public function desactiver(Athlete $athlete): bool
    {
        return $athlete->update(['actif' => false]);
    }

    /**
     * Réactive un athlète
     */
    public function reactiver(Athlete $athlete): bool
    {
        return $athlete->update(['actif' => true]);
    }

    /**
     * Supprime un athlète et ses données associées
     */
    public function supprimer(Athlete $athlete): bool
    {
        return DB::transaction(function () use ($athlete) {
            // Supprimer la photo
            if ($athlete->photo) {
                Storage::disk('public')->delete($athlete->photo);
            }

            // Les relations seront supprimées par cascade
            return $athlete->delete();
        });
    }

    /**
     * Attache des disciplines à un athlète
     */
    public function attacherDisciplines(Athlete $athlete, array $disciplineIds): void
    {
        $disciplines = collect($disciplineIds)->mapWithKeys(fn($id) => [
            $id => ['date_inscription' => now(), 'actif' => true]
        ]);
        $athlete->disciplines()->attach($disciplines);
    }

    /**
     * Synchronise les disciplines d'un athlète
     */
    public function synchroniserDisciplines(Athlete $athlete, array $disciplineIds): void
    {
        $disciplines = collect($disciplineIds)->mapWithKeys(fn($id) => [
            $id => ['date_inscription' => now(), 'actif' => true]
        ]);
        $athlete->disciplines()->sync($disciplines);
    }

    /**
     * Inscrit un athlète à une discipline
     */
    public function inscrireDiscipline(Athlete $athlete, Discipline $discipline): void
    {
        if (!$athlete->disciplines()->where('discipline_id', $discipline->id)->exists()) {
            $athlete->disciplines()->attach($discipline->id, [
                'date_inscription' => now(),
                'actif' => true
            ]);
        }
    }

    /**
     * Désinscrit un athlète d'une discipline
     */
    public function desinscrireDiscipline(Athlete $athlete, Discipline $discipline): void
    {
        $athlete->disciplines()->updateExistingPivot($discipline->id, ['actif' => false]);
    }

    /**
     * Calcule les statistiques d'un athlète
     */
    public function calculerStatistiques(Athlete $athlete): array
    {
        $presences = $athlete->presences();
        $totalPresences = $presences->count();
        $presentsCount = $presences->where('present', true)->count();

        return [
            'disciplines_count' => $athlete->disciplines()->wherePivot('actif', true)->count(),
            'presences_total' => $totalPresences,
            'presences_presents' => $presentsCount,
            'presences_absents' => $totalPresences - $presentsCount,
            'taux_presence' => $totalPresences > 0 ? round(($presentsCount / $totalPresences) * 100, 1) : 0,
            'arrieres' => $this->calculerArrieres($athlete),
            'performances_count' => $athlete->performances()->count(),
            'derniere_presence' => $athlete->presences()->latest('date')->first()?->date,
        ];
    }

    /**
     * Calcule le total des arriérés d'un athlète
     */
    public function calculerArrieres(Athlete $athlete): float
    {
        return $athlete->paiements()
            ->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL])
            ->get()
            ->sum(fn($p) => $p->montant - $p->montant_paye);
    }

    /**
     * Vérifie si un athlète est éligible (pas d'arriérés importants)
     */
    public function estEligible(Athlete $athlete, float $seuilArrieres = 50000): bool
    {
        return $this->calculerArrieres($athlete) < $seuilArrieres;
    }

    /**
     * Récupère les athlètes avec arriérés
     */
    public function getAthletesAvecArrieres(): Collection
    {
        return Athlete::whereHas('paiements', function ($query) {
            $query->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL]);
        })->with(['paiements' => function ($query) {
            $query->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL]);
        }])->get();
    }

    /**
     * Récupère les athlètes inactifs depuis X jours
     */
    public function getAthletesInactifs(int $joursInactivite = 30): Collection
    {
        $dateLimit = now()->subDays($joursInactivite);

        return Athlete::where('actif', true)
            ->where(function ($query) use ($dateLimit) {
                $query->whereDoesntHave('presences')
                    ->orWhereHas('presences', function ($q) use ($dateLimit) {
                        $q->where('date', '<', $dateLimit);
                    }, '=', $query->whereDoesntHave('presences', function ($q) use ($dateLimit) {
                        $q->where('date', '>=', $dateLimit);
                    }));
            })
            ->get();
    }

    /**
     * Recherche avancée d'athlètes
     */
    public function rechercher(array $criteres): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Athlete::with('disciplines');

        if (!empty($criteres['search'])) {
            $search = $criteres['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if (!empty($criteres['discipline'])) {
            $query->whereHas('disciplines', function ($q) use ($criteres) {
                $q->where('disciplines.id', $criteres['discipline']);
            });
        }

        if (isset($criteres['actif'])) {
            $query->where('actif', $criteres['actif']);
        }

        if (!empty($criteres['sexe'])) {
            $query->where('sexe', $criteres['sexe']);
        }

        if (!empty($criteres['age_min'])) {
            $query->whereDate('date_naissance', '<=', now()->subYears($criteres['age_min']));
        }

        if (!empty($criteres['age_max'])) {
            $query->whereDate('date_naissance', '>=', now()->subYears($criteres['age_max']));
        }

        return $query->orderBy('nom')->paginate($criteres['per_page'] ?? 15)->withQueryString();
    }
}

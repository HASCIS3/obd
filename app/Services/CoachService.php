<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CoachService
{
    /**
     * Crée un nouveau coach avec son compte utilisateur
     */
    public function creer(array $data): Coach
    {
        return DB::transaction(function () use ($data) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_COACH,
                'email_verified_at' => now(),
            ]);

            // Créer le profil coach
            $coach = Coach::create([
                'user_id' => $user->id,
                'telephone' => $data['telephone'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'specialite' => $data['specialite'] ?? null,
                'date_embauche' => $data['date_embauche'] ?? now(),
                'actif' => true,
            ]);

            // Attacher les disciplines
            if (!empty($data['disciplines'])) {
                $coach->disciplines()->attach($data['disciplines']);
            }

            return $coach;
        });
    }

    /**
     * Met à jour un coach
     */
    public function mettreAJour(Coach $coach, array $data): Coach
    {
        return DB::transaction(function () use ($coach, $data) {
            // Mettre à jour l'utilisateur
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $coach->user->update($userData);

            // Mettre à jour le profil coach
            $coach->update([
                'telephone' => $data['telephone'] ?? null,
                'adresse' => $data['adresse'] ?? null,
                'specialite' => $data['specialite'] ?? null,
                'date_embauche' => $data['date_embauche'] ?? $coach->date_embauche,
                'actif' => $data['actif'] ?? $coach->actif,
            ]);

            // Synchroniser les disciplines
            if (isset($data['disciplines'])) {
                $coach->disciplines()->sync($data['disciplines']);
            }

            return $coach->fresh();
        });
    }

    /**
     * Désactive un coach
     */
    public function desactiver(Coach $coach): bool
    {
        return $coach->update(['actif' => false]);
    }

    /**
     * Réactive un coach
     */
    public function reactiver(Coach $coach): bool
    {
        return $coach->update(['actif' => true]);
    }

    /**
     * Supprime un coach et son compte utilisateur
     */
    public function supprimer(Coach $coach): bool
    {
        return DB::transaction(function () use ($coach) {
            $user = $coach->user;
            $coach->delete();
            return $user->delete();
        });
    }

    /**
     * Assigne une discipline à un coach
     */
    public function assignerDiscipline(Coach $coach, Discipline $discipline): void
    {
        if (!$coach->disciplines()->where('discipline_id', $discipline->id)->exists()) {
            $coach->disciplines()->attach($discipline->id);
        }
    }

    /**
     * Retire une discipline d'un coach
     */
    public function retirerDiscipline(Coach $coach, Discipline $discipline): void
    {
        $coach->disciplines()->detach($discipline->id);
    }

    /**
     * Récupère les statistiques d'un coach
     */
    public function getStatistiques(Coach $coach): array
    {
        $presencesMois = $coach->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        return [
            'disciplines_count' => $coach->disciplines()->count(),
            'presences_total' => $coach->presences()->count(),
            'presences_mois' => $presencesMois,
            'athletes_suivis' => $this->getAthletesSuivis($coach)->count(),
            'anciennete_jours' => $coach->date_embauche ? now()->diffInDays($coach->date_embauche) : 0,
        ];
    }

    /**
     * Récupère les athlètes suivis par un coach (via ses disciplines)
     */
    public function getAthletesSuivis(Coach $coach): Collection
    {
        $disciplineIds = $coach->disciplines()->pluck('disciplines.id');

        return \App\Models\Athlete::whereHas('disciplines', function ($query) use ($disciplineIds) {
            $query->whereIn('disciplines.id', $disciplineIds)
                ->where('athlete_discipline.actif', true);
        })->where('actif', true)->get();
    }

    /**
     * Récupère les coachs disponibles pour une discipline
     */
    public function getCoachsParDiscipline(Discipline $discipline): Collection
    {
        return Coach::whereHas('disciplines', function ($query) use ($discipline) {
            $query->where('disciplines.id', $discipline->id);
        })->where('actif', true)->with('user')->get();
    }

    /**
     * Récupère les coachs actifs
     */
    public function getCoachsActifs(): Collection
    {
        return Coach::where('actif', true)->with(['user', 'disciplines'])->get();
    }

    /**
     * Recherche de coachs
     */
    public function rechercher(array $criteres): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Coach::with(['user', 'disciplines']);

        if (!empty($criteres['search'])) {
            $search = $criteres['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
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

        return $query->orderBy('created_at', 'desc')
            ->paginate($criteres['per_page'] ?? 15)
            ->withQueryString();
    }

    /**
     * Vérifie si un coach peut enregistrer des présences pour une discipline
     */
    public function peutGererDiscipline(Coach $coach, Discipline $discipline): bool
    {
        return $coach->disciplines()->where('disciplines.id', $discipline->id)->exists();
    }
}

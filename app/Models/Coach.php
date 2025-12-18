<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    use HasFactory;

    protected $table = 'coachs';

    protected $fillable = [
        'user_id',
        'telephone',
        'adresse',
        'specialite',
        'photo',
        'date_embauche',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'date_embauche' => 'date',
            'actif' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    /**
     * L'utilisateur associé au coach
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Les disciplines enseignées par ce coach
     */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'coach_discipline');
    }

    /**
     * Les disciplines actives
     */
    public function disciplinesActives(): BelongsToMany
    {
        return $this->disciplines()->where('actif', true);
    }

    /**
     * Les présences enregistrées par ce coach
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les coachs actifs
     */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les coachs inactifs
     */
    public function scopeInactifs(Builder $query): Builder
    {
        return $query->where('actif', false);
    }

    /**
     * Scope pour les coachs d'une discipline
     */
    public function scopePourDiscipline(Builder $query, int $disciplineId): Builder
    {
        return $query->whereHas('disciplines', function ($q) use ($disciplineId) {
            $q->where('disciplines.id', $disciplineId);
        });
    }

    // ==================== ACCESSEURS ====================

    /**
     * Nom complet du coach via l'utilisateur
     */
    public function getNomCompletAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Email du coach
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * Ancienneté en jours
     */
    public function getAncienneteJoursAttribute(): int
    {
        return $this->date_embauche ? now()->diffInDays($this->date_embauche) : 0;
    }

    /**
     * Ancienneté formatée
     */
    public function getAncienneteFormateAttribute(): string
    {
        if (!$this->date_embauche) {
            return 'Non définie';
        }

        $jours = $this->anciennete_jours;
        if ($jours < 30) {
            return "{$jours} jour(s)";
        } elseif ($jours < 365) {
            $mois = floor($jours / 30);
            return "{$mois} mois";
        }
        $annees = floor($jours / 365);
        $moisRestants = floor(($jours % 365) / 30);
        return $moisRestants > 0 ? "{$annees} an(s) et {$moisRestants} mois" : "{$annees} an(s)";
    }

    /**
     * Nombre de présences enregistrées ce mois
     */
    public function getNbPresencesMoisAttribute(): int
    {
        return $this->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
    }

    /**
     * URL de la photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        return asset('storage/' . $this->photo);
    }

    // ==================== METHODES METIER ====================

    /**
     * Vérifie si le coach est actif
     */
    public function estActif(): bool
    {
        return $this->actif === true;
    }

    /**
     * Vérifie si le coach enseigne une discipline
     */
    public function enseigneDiscipline(Discipline $discipline): bool
    {
        return $this->disciplines()->where('discipline_id', $discipline->id)->exists();
    }

    /**
     * Peut enregistrer des présences pour une discipline
     */
    public function peutGererDiscipline(Discipline $discipline): bool
    {
        return $this->enseigneDiscipline($discipline);
    }

    /**
     * Récupère les athlètes suivis (via les disciplines)
     */
    public function getAthletesSuivis()
    {
        $disciplineIds = $this->disciplines()->pluck('disciplines.id');

        return Athlete::whereHas('disciplines', function ($query) use ($disciplineIds) {
            $query->whereIn('disciplines.id', $disciplineIds)
                ->where('athlete_discipline.actif', true);
        })->where('actif', true)->get();
    }

    /**
     * Nombre d'athlètes suivis
     */
    public function getNbAthletesSuivis(): int
    {
        return $this->getAthletesSuivis()->count();
    }

    /**
     * Calcule les statistiques du coach
     */
    public function getStatistiques(): array
    {
        return [
            'disciplines_count' => $this->disciplines()->count(),
            'presences_total' => $this->presences()->count(),
            'presences_mois' => $this->nb_presences_mois,
            'athletes_suivis' => $this->getNbAthletesSuivis(),
            'anciennete_jours' => $this->anciennete_jours,
        ];
    }
}

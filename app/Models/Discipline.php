<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'tarif_mensuel',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'tarif_mensuel' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    /**
     * Les coachs qui enseignent cette discipline
     */
    public function coachs(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_discipline');
    }

    /**
     * Les coachs actifs
     */
    public function coachsActifs(): BelongsToMany
    {
        return $this->coachs()->where('actif', true);
    }

    /**
     * Les athlètes inscrits à cette discipline
     */
    public function athletes(): BelongsToMany
    {
        return $this->belongsToMany(Athlete::class, 'athlete_discipline')
            ->withPivot('date_inscription', 'actif')
            ->withTimestamps();
    }

    /**
     * Les athlètes actifs
     */
    public function athletesActifs(): BelongsToMany
    {
        return $this->athletes()->wherePivot('actif', true)->where('athletes.actif', true);
    }

    /**
     * Les présences pour cette discipline
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Les performances dans cette discipline
     */
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les disciplines actives
     */
    public function scopeActives(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les disciplines inactives
     */
    public function scopeInactives(Builder $query): Builder
    {
        return $query->where('actif', false);
    }

    /**
     * Scope pour les disciplines avec athlètes
     */
    public function scopeAvecAthletes(Builder $query): Builder
    {
        return $query->whereHas('athletes', function ($q) {
            $q->where('athlete_discipline.actif', true);
        });
    }

    /**
     * Scope pour les disciplines sans coach
     */
    public function scopeSansCoach(Builder $query): Builder
    {
        return $query->whereDoesntHave('coachs');
    }

    // ==================== ACCESSEURS ====================

    /**
     * Tarif formaté
     */
    public function getTarifFormateAttribute(): string
    {
        return number_format($this->tarif_mensuel, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Nombre d'athlètes actifs
     */
    public function getNbAthletesActifsAttribute(): int
    {
        return $this->athletesActifs()->count();
    }

    /**
     * Revenus potentiels mensuels
     */
    public function getRevenusPotentielsAttribute(): float
    {
        return $this->nb_athletes_actifs * $this->tarif_mensuel;
    }

    // ==================== METHODES METIER ====================

    /**
     * Vérifie si la discipline est active
     */
    public function estActive(): bool
    {
        return $this->actif === true;
    }

    /**
     * Vérifie si un coach enseigne cette discipline
     */
    public function estEnseigneePar(Coach $coach): bool
    {
        return $this->coachs()->where('coach_id', $coach->id)->exists();
    }

    /**
     * Vérifie si un athlète est inscrit
     */
    public function aInscrit(Athlete $athlete): bool
    {
        return $this->athletes()
            ->where('athlete_id', $athlete->id)
            ->wherePivot('actif', true)
            ->exists();
    }

    /**
     * Calcule les statistiques de présence du mois
     */
    public function getStatistiquesPresenceMois(): array
    {
        $presences = $this->presences()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        return Presence::calculerStatistiques($presences);
    }

    /**
     * Récupère le taux de présence du mois
     */
    public function getTauxPresenceMois(): float
    {
        return $this->getStatistiquesPresenceMois()['taux'];
    }

    /**
     * Peut être supprimée (pas d'athlètes ni de coachs)
     */
    public function peutEtreSupprimee(): bool
    {
        return $this->athletes()->count() === 0 && $this->coachs()->count() === 0;
    }
}

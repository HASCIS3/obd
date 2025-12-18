<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'discipline_id',
        'coach_id',
        'date',
        'present',
        'remarque',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'present' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    /**
     * L'athlète concerné
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * La discipline concernée
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    /**
     * Le coach qui a enregistré la présence
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les présences
     */
    public function scopePresents(Builder $query): Builder
    {
        return $query->where('present', true);
    }

    /**
     * Scope pour les absences
     */
    public function scopeAbsents(Builder $query): Builder
    {
        return $query->where('present', false);
    }

    /**
     * Scope pour une date
     */
    public function scopePourDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope pour une discipline
     */
    public function scopePourDiscipline(Builder $query, int $disciplineId): Builder
    {
        return $query->where('discipline_id', $disciplineId);
    }

    /**
     * Scope pour un mois
     */
    public function scopePourMois(Builder $query, int $mois, int $annee): Builder
    {
        return $query->whereMonth('date', $mois)->whereYear('date', $annee);
    }

    /**
     * Scope pour le mois en cours
     */
    public function scopeMoisCourant(Builder $query): Builder
    {
        return $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }

    /**
     * Scope pour une période
     */
    public function scopePourPeriode(Builder $query, $dateDebut, $dateFin): Builder
    {
        return $query->whereBetween('date', [$dateDebut, $dateFin]);
    }

    // ==================== ACCESSEURS ====================

    /**
     * Libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        return $this->present ? 'Présent' : 'Absent';
    }

    /**
     * Couleur du statut pour l'affichage
     */
    public function getStatutCouleurAttribute(): string
    {
        return $this->present ? 'success' : 'danger';
    }

    // ==================== METHODES METIER ====================

    /**
     * Vérifie si c'est une présence
     */
    public function estPresent(): bool
    {
        return $this->present === true;
    }

    /**
     * Vérifie si c'est une absence
     */
    public function estAbsent(): bool
    {
        return $this->present === false;
    }

    /**
     * Marque comme présent
     */
    public function marquerPresent(?string $remarque = null): self
    {
        $this->update([
            'present' => true,
            'remarque' => $remarque ?? $this->remarque,
        ]);
        return $this->fresh();
    }

    /**
     * Marque comme absent
     */
    public function marquerAbsent(?string $remarque = null): self
    {
        $this->update([
            'present' => false,
            'remarque' => $remarque ?? $this->remarque,
        ]);
        return $this->fresh();
    }

    /**
     * Calcule les statistiques pour un ensemble de présences
     */
    public static function calculerStatistiques($presences): array
    {
        $total = $presences->count();
        $presents = $presences->where('present', true)->count();
        $absents = $total - $presents;

        return [
            'total' => $total,
            'presents' => $presents,
            'absents' => $absents,
            'taux' => $total > 0 ? round(($presents / $total) * 100, 1) : 0,
        ];
    }
}

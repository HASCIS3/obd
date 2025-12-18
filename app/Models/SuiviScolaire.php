<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuiviScolaire extends Model
{
    use HasFactory;

    protected $table = 'suivis_scolaires';

    public const SEUIL_PASSABLE = 10;
    public const SEUIL_SATISFAISANT = 12;
    public const SEUIL_TRES_BIEN = 14;
    public const SEUIL_EXCELLENT = 17;

    protected $fillable = [
        'athlete_id',
        'etablissement',
        'classe',
        'annee_scolaire',
        'moyenne_generale',
        'rang',
        'observations',
        'bulletin_path',
    ];

    protected function casts(): array
    {
        return [
            'moyenne_generale' => 'decimal:2',
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

    // ==================== SCOPES ====================

    /**
     * Scope pour les résultats satisfaisants
     */
    public function scopeSatisfaisants(Builder $query): Builder
    {
        return $query->where('moyenne_generale', '>=', self::SEUIL_SATISFAISANT);
    }

    /**
     * Scope pour les résultats insuffisants
     */
    public function scopeInsuffisants(Builder $query): Builder
    {
        return $query->where('moyenne_generale', '<', self::SEUIL_SATISFAISANT);
    }

    /**
     * Scope pour les excellents résultats
     */
    public function scopeExcellents(Builder $query): Builder
    {
        return $query->where('moyenne_generale', '>=', self::SEUIL_EXCELLENT);
    }

    /**
     * Scope pour une année scolaire
     */
    public function scopePourAnnee(Builder $query, string $anneeScolaire): Builder
    {
        return $query->where('annee_scolaire', $anneeScolaire);
    }

    // ==================== ACCESSEURS ====================

    /**
     * Niveau évalué
     */
    public function getNiveauAttribute(): string
    {
        if ($this->moyenne_generale === null) {
            return 'Non évalué';
        }

        if ($this->moyenne_generale >= self::SEUIL_EXCELLENT) {
            return 'Excellent';
        } elseif ($this->moyenne_generale >= self::SEUIL_TRES_BIEN) {
            return 'Très bien';
        } elseif ($this->moyenne_generale >= self::SEUIL_SATISFAISANT) {
            return 'Satisfaisant';
        } elseif ($this->moyenne_generale >= self::SEUIL_PASSABLE) {
            return 'Passable';
        }

        return 'Insuffisant';
    }

    /**
     * Couleur du niveau pour l'affichage
     */
    public function getNiveauCouleurAttribute(): string
    {
        if ($this->moyenne_generale === null) {
            return 'gray';
        }

        if ($this->moyenne_generale >= self::SEUIL_EXCELLENT) {
            return 'success';
        } elseif ($this->moyenne_generale >= self::SEUIL_TRES_BIEN) {
            return 'primary';
        } elseif ($this->moyenne_generale >= self::SEUIL_SATISFAISANT) {
            return 'secondary';
        } elseif ($this->moyenne_generale >= self::SEUIL_PASSABLE) {
            return 'warning';
        }

        return 'danger';
    }

    /**
     * Moyenne formatée
     */
    public function getMoyenneFormateeAttribute(): string
    {
        if ($this->moyenne_generale === null) {
            return 'N/A';
        }
        return number_format($this->moyenne_generale, 2) . '/20';
    }

    /**
     * Rang formaté
     */
    public function getRangFormateAttribute(): ?string
    {
        if (!$this->rang) {
            return null;
        }
        
        $suffixe = $this->rang === 1 ? 'er' : 'ème';
        return "{$this->rang}{$suffixe}";
    }

    /**
     * URL du bulletin
     */
    public function getBulletinUrlAttribute(): ?string
    {
        if (!$this->bulletin_path) {
            return null;
        }
        return asset('storage/' . $this->bulletin_path);
    }

    // ==================== METHODES METIER ====================

    /**
     * Vérifie si la moyenne est satisfaisante (>= 12)
     */
    public function estSatisfaisant(): bool
    {
        return $this->moyenne_generale !== null && $this->moyenne_generale >= self::SEUIL_SATISFAISANT;
    }

    /**
     * Vérifie si la moyenne est excellente (>= 14)
     */
    public function estExcellent(): bool
    {
        return $this->moyenne_generale !== null && $this->moyenne_generale >= self::SEUIL_EXCELLENT;
    }

    /**
     * Vérifie si la moyenne est passable (>= 10)
     */
    public function estPassable(): bool
    {
        return $this->moyenne_generale !== null && $this->moyenne_generale >= self::SEUIL_PASSABLE;
    }

    /**
     * Vérifie si la moyenne est insuffisante (< 10)
     */
    public function estInsuffisant(): bool
    {
        return $this->moyenne_generale !== null && $this->moyenne_generale < self::SEUIL_PASSABLE;
    }

    /**
     * Vérifie si l'athlète est éligible scolairement
     */
    public function estEligible(): bool
    {
        // Pas de moyenne = éligible par défaut
        if ($this->moyenne_generale === null) {
            return true;
        }
        return $this->moyenne_generale >= self::SEUIL_PASSABLE;
    }

    /**
     * Calcule la différence avec une autre moyenne
     */
    public function comparerAvec(SuiviScolaire $autre): ?float
    {
        if ($this->moyenne_generale === null || $autre->moyenne_generale === null) {
            return null;
        }
        return round($this->moyenne_generale - $autre->moyenne_generale, 2);
    }
}

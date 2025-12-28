<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Licence extends Model
{
    use HasFactory;

    const TYPE_NATIONALE = 'nationale';
    const TYPE_REGIONALE = 'regionale';
    const TYPE_LOCALE = 'locale';

    const STATUT_ACTIVE = 'active';
    const STATUT_EXPIREE = 'expiree';
    const STATUT_SUSPENDUE = 'suspendue';
    const STATUT_ANNULEE = 'annulee';

    const CATEGORIES = ['U11', 'U13', 'U15', 'U17', 'U19', 'U21', 'Senior', 'Veteran'];

    protected $fillable = [
        'athlete_id',
        'discipline_id',
        'numero_licence',
        'federation',
        'type',
        'categorie',
        'date_emission',
        'date_expiration',
        'statut',
        'saison',
        'frais_licence',
        'paye',
        'document',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_emission' => 'date',
            'date_expiration' => 'date',
            'frais_licence' => 'decimal:2',
            'paye' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    // ==================== SCOPES ====================

    public function scopeActives(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_ACTIVE);
    }

    public function scopeExpirees(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_EXPIREE);
    }

    public function scopeExpirantBientot(Builder $query, int $jours = 30): Builder
    {
        return $query->where('statut', self::STATUT_ACTIVE)
            ->whereBetween('date_expiration', [now(), now()->addDays($jours)]);
    }

    public function scopeNonPayees(Builder $query): Builder
    {
        return $query->where('paye', false);
    }

    public function scopeDeSaison(Builder $query, ?string $saison = null): Builder
    {
        $saison = $saison ?? $this->getSaisonActuelle();
        return $query->where('saison', $saison);
    }

    // ==================== ACCESSEURS ====================

    public function getEstActiveAttribute(): bool
    {
        return $this->statut === self::STATUT_ACTIVE && $this->date_expiration >= now();
    }

    public function getEstExpireeAttribute(): bool
    {
        return $this->date_expiration < now();
    }

    public function getJoursRestantsAttribute(): int
    {
        if ($this->date_expiration < now()) {
            return 0;
        }
        return now()->diffInDays($this->date_expiration);
    }

    public function getDocumentUrlAttribute(): ?string
    {
        if (!$this->document) {
            return null;
        }
        return asset('storage/' . $this->document);
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUT_EXPIREE => 'bg-red-100 text-red-800',
            self::STATUT_SUSPENDUE => 'bg-yellow-100 text-yellow-800',
            self::STATUT_ANNULEE => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // ==================== METHODES ====================

    public static function getSaisonActuelle(): string
    {
        $annee = now()->year;
        $mois = now()->month;
        
        // La saison commence en septembre
        if ($mois >= 9) {
            return $annee . '-' . ($annee + 1);
        }
        return ($annee - 1) . '-' . $annee;
    }

    public static function genererNumeroLicence(Athlete $athlete, Discipline $discipline): string
    {
        $annee = now()->format('Y');
        $disciplineCode = strtoupper(substr($discipline->nom, 0, 3));
        $athleteId = str_pad($athlete->id, 5, '0', STR_PAD_LEFT);
        $random = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        
        return "OBD-{$annee}-{$disciplineCode}-{$athleteId}-{$random}";
    }

    public static function getCategorieParAge(int $age): string
    {
        return match (true) {
            $age < 11 => 'U11',
            $age < 13 => 'U13',
            $age < 15 => 'U15',
            $age < 17 => 'U17',
            $age < 19 => 'U19',
            $age < 21 => 'U21',
            $age < 35 => 'Senior',
            default => 'Veteran',
        };
    }

    public function renouveler(): self
    {
        $nouvelleLicence = $this->replicate();
        $nouvelleLicence->date_emission = now();
        $nouvelleLicence->date_expiration = now()->addYear();
        $nouvelleLicence->saison = self::getSaisonActuelle();
        $nouvelleLicence->statut = self::STATUT_ACTIVE;
        $nouvelleLicence->paye = false;
        $nouvelleLicence->numero_licence = self::genererNumeroLicence($this->athlete, $this->discipline);
        $nouvelleLicence->save();

        // Marquer l'ancienne comme expirée
        $this->update(['statut' => self::STATUT_EXPIREE]);

        return $nouvelleLicence;
    }

    public function verifierExpiration(): void
    {
        if ($this->date_expiration < now() && $this->statut === self::STATUT_ACTIVE) {
            $this->update(['statut' => self::STATUT_EXPIREE]);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificatMedical extends Model
{
    use HasFactory;

    protected $table = 'certificats_medicaux';

    const TYPE_APTITUDE = 'aptitude';
    const TYPE_INAPTITUDE_TEMPORAIRE = 'inaptitude_temporaire';
    const TYPE_INAPTITUDE_DEFINITIVE = 'inaptitude_definitive';
    const TYPE_SUIVI = 'suivi';

    const STATUT_VALIDE = 'valide';
    const STATUT_EXPIRE = 'expire';
    const STATUT_EN_ATTENTE = 'en_attente';

    protected $fillable = [
        'athlete_id',
        'type',
        'date_examen',
        'date_expiration',
        'medecin',
        'etablissement',
        'statut',
        'apte_competition',
        'apte_entrainement',
        'restrictions',
        'observations',
        'document',
    ];

    protected function casts(): array
    {
        return [
            'date_examen' => 'date',
            'date_expiration' => 'date',
            'apte_competition' => 'boolean',
            'apte_entrainement' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    // ==================== SCOPES ====================

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_VALIDE);
    }

    public function scopeExpires(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_EXPIRE);
    }

    public function scopeExpirantBientot(Builder $query, int $jours = 30): Builder
    {
        return $query->where('statut', self::STATUT_VALIDE)
            ->whereBetween('date_expiration', [now(), now()->addDays($jours)]);
    }

    public function scopeAptes(Builder $query): Builder
    {
        return $query->where('apte_competition', true)
            ->where('apte_entrainement', true);
    }

    // ==================== ACCESSEURS ====================

    public function getEstValideAttribute(): bool
    {
        return $this->statut === self::STATUT_VALIDE && $this->date_expiration >= now();
    }

    public function getEstExpireAttribute(): bool
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

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_APTITUDE => 'Aptitude',
            self::TYPE_INAPTITUDE_TEMPORAIRE => 'Inaptitude temporaire',
            self::TYPE_INAPTITUDE_DEFINITIVE => 'Inaptitude définitive',
            self::TYPE_SUIVI => 'Suivi médical',
            default => $this->type,
        };
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_VALIDE => 'bg-green-100 text-green-800',
            self::STATUT_EXPIRE => 'bg-red-100 text-red-800',
            self::STATUT_EN_ATTENTE => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // ==================== METHODES ====================

    public function verifierExpiration(): void
    {
        if ($this->date_expiration < now() && $this->statut === self::STATUT_VALIDE) {
            $this->update(['statut' => self::STATUT_EXPIRE]);
        }
    }

    public function renouveler(array $data): self
    {
        // Marquer l'ancien comme expiré
        $this->update(['statut' => self::STATUT_EXPIRE]);

        // Créer un nouveau certificat
        return self::create(array_merge([
            'athlete_id' => $this->athlete_id,
            'type' => self::TYPE_APTITUDE,
            'statut' => self::STATUT_VALIDE,
        ], $data));
    }
}

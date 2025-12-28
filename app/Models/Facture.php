<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends Model
{
    use HasFactory;

    const STATUT_BROUILLON = 'brouillon';
    const STATUT_EMISE = 'emise';
    const STATUT_PAYEE = 'payee';
    const STATUT_PARTIELLEMENT_PAYEE = 'partiellement_payee';
    const STATUT_ANNULEE = 'annulee';

    protected $fillable = [
        'numero',
        'athlete_id',
        'date_emission',
        'date_echeance',
        'montant_ht',
        'tva',
        'montant_ttc',
        'montant_paye',
        'statut',
        'periode',
        'description',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_emission' => 'date',
            'date_echeance' => 'date',
            'montant_ht' => 'decimal:2',
            'tva' => 'decimal:2',
            'montant_ttc' => 'decimal:2',
            'montant_paye' => 'decimal:2',
        ];
    }

    // ==================== RELATIONS ====================

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    // ==================== SCOPES ====================

    public function scopeEmises(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_EMISE);
    }

    public function scopePayees(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PAYEE);
    }

    public function scopeImpayees(Builder $query): Builder
    {
        return $query->whereIn('statut', [self::STATUT_EMISE, self::STATUT_PARTIELLEMENT_PAYEE]);
    }

    public function scopeEnRetard(Builder $query): Builder
    {
        return $query->whereIn('statut', [self::STATUT_EMISE, self::STATUT_PARTIELLEMENT_PAYEE])
            ->where('date_echeance', '<', now());
    }

    // ==================== ACCESSEURS ====================

    public function getResteAPayerAttribute(): float
    {
        return $this->montant_ttc - $this->montant_paye;
    }

    public function getEstPayeeAttribute(): bool
    {
        return $this->statut === self::STATUT_PAYEE;
    }

    public function getEstEnRetardAttribute(): bool
    {
        return in_array($this->statut, [self::STATUT_EMISE, self::STATUT_PARTIELLEMENT_PAYEE])
            && $this->date_echeance < now();
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON => 'bg-gray-100 text-gray-800',
            self::STATUT_EMISE => 'bg-blue-100 text-blue-800',
            self::STATUT_PAYEE => 'bg-green-100 text-green-800',
            self::STATUT_PARTIELLEMENT_PAYEE => 'bg-yellow-100 text-yellow-800',
            self::STATUT_ANNULEE => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON => 'Brouillon',
            self::STATUT_EMISE => 'Émise',
            self::STATUT_PAYEE => 'Payée',
            self::STATUT_PARTIELLEMENT_PAYEE => 'Partiellement payée',
            self::STATUT_ANNULEE => 'Annulée',
            default => $this->statut,
        };
    }

    // ==================== METHODES STATIQUES ====================

    public static function genererNumero(): string
    {
        $annee = now()->format('Y');
        $mois = now()->format('m');
        $derniere = self::whereYear('created_at', $annee)
            ->whereMonth('created_at', $mois)
            ->count();
        
        return sprintf('FAC-%s%s-%04d', $annee, $mois, $derniere + 1);
    }

    // ==================== METHODES ====================

    public function emettre(): void
    {
        if ($this->statut !== self::STATUT_BROUILLON) {
            return;
        }

        $this->update([
            'statut' => self::STATUT_EMISE,
            'date_emission' => now(),
        ]);
    }

    public function enregistrerPaiement(float $montant): void
    {
        $nouveauMontantPaye = $this->montant_paye + $montant;

        if ($nouveauMontantPaye >= $this->montant_ttc) {
            $this->update([
                'montant_paye' => $this->montant_ttc,
                'statut' => self::STATUT_PAYEE,
            ]);
        } else {
            $this->update([
                'montant_paye' => $nouveauMontantPaye,
                'statut' => self::STATUT_PARTIELLEMENT_PAYEE,
            ]);
        }
    }

    public function annuler(): void
    {
        $this->update(['statut' => self::STATUT_ANNULEE]);
    }
}

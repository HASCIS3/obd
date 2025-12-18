<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory;

    public const STATUT_PAYE = 'paye';
    public const STATUT_IMPAYE = 'impaye';
    public const STATUT_PARTIEL = 'partiel';

    public const MODE_ESPECES = 'especes';
    public const MODE_VIREMENT = 'virement';
    public const MODE_MOBILE = 'mobile_money';

    public const TYPE_COTISATION = 'cotisation';
    public const TYPE_INSCRIPTION = 'inscription';
    public const TYPE_EQUIPEMENT = 'equipement';

    public const EQUIPEMENT_MAILLOT = 'maillot';
    public const EQUIPEMENT_DOBOK = 'dobok';
    public const EQUIPEMENT_DOBOK_ENFANT = 'dobok_enfant';
    public const EQUIPEMENT_DOBOK_JUNIOR = 'dobok_junior';
    public const EQUIPEMENT_DOBOK_SENIOR = 'dobok_senior';

    protected $fillable = [
        'athlete_id',
        'type_paiement',
        'frais_inscription',
        'type_equipement',
        'frais_equipement',
        'montant',
        'montant_paye',
        'mois',
        'annee',
        'date_paiement',
        'mode_paiement',
        'statut',
        'reference',
        'remarque',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'montant_paye' => 'decimal:2',
            'frais_inscription' => 'decimal:2',
            'frais_equipement' => 'decimal:2',
            'date_paiement' => 'date',
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
     * Scope pour les paiements payés
     */
    public function scopePayes(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PAYE);
    }

    /**
     * Scope pour les paiements impayés
     */
    public function scopeImpayes(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_IMPAYE);
    }

    /**
     * Scope pour les paiements partiels
     */
    public function scopePartiels(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PARTIEL);
    }

    /**
     * Scope pour les arriérés (impayés + partiels)
     */
    public function scopeArrieres(Builder $query): Builder
    {
        return $query->whereIn('statut', [self::STATUT_IMPAYE, self::STATUT_PARTIEL]);
    }

    /**
     * Scope pour un mois/année spécifique
     */
    public function scopePourPeriode(Builder $query, int $mois, int $annee): Builder
    {
        return $query->where('mois', $mois)->where('annee', $annee);
    }

    /**
     * Scope pour une année
     */
    public function scopePourAnnee(Builder $query, int $annee): Builder
    {
        return $query->where('annee', $annee);
    }

    /**
     * Scope pour le mois en cours
     */
    public function scopeMoisCourant(Builder $query): Builder
    {
        return $query->where('mois', now()->month)->where('annee', now()->year);
    }

    // ==================== ACCESSEURS ====================

    /**
     * Montant restant à payer
     */
    public function getResteAPayerAttribute(): float
    {
        return max(0, $this->montant - ($this->montant_paye ?? 0));
    }

    /**
     * Libellé de la période
     */
    public function getPeriodeAttribute(): string
    {
        $moisNoms = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return ($moisNoms[$this->mois] ?? $this->mois) . ' ' . $this->annee;
    }

    /**
     * Pourcentage payé
     */
    public function getPourcentagePayeAttribute(): float
    {
        if ($this->montant <= 0) {
            return 0;
        }
        return round(($this->montant_paye / $this->montant) * 100, 1);
    }

    /**
     * Libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        return self::statuts()[$this->statut] ?? $this->statut;
    }

    /**
     * Libellé du mode de paiement
     */
    public function getModePaiementLibelleAttribute(): string
    {
        return self::modesPaiement()[$this->mode_paiement] ?? $this->mode_paiement;
    }

    // ==================== METHODES METIER ====================

    /**
     * Vérifie si le paiement est complet
     */
    public function estComplet(): bool
    {
        return $this->statut === self::STATUT_PAYE;
    }

    /**
     * Vérifie si le paiement est en retard
     */
    public function estEnRetard(): bool
    {
        if ($this->statut === self::STATUT_PAYE) {
            return false;
        }

        // Considéré en retard si le mois est passé
        $dateLimite = \Carbon\Carbon::create($this->annee, $this->mois)->endOfMonth();
        return now()->isAfter($dateLimite);
    }

    /**
     * Calcule le nombre de jours de retard
     */
    public function getJoursRetard(): int
    {
        if (!$this->estEnRetard()) {
            return 0;
        }

        $dateLimite = \Carbon\Carbon::create($this->annee, $this->mois)->endOfMonth();
        return now()->diffInDays($dateLimite);
    }

    /**
     * Enregistre un paiement partiel ou complet
     */
    public function enregistrerPaiement(float $montant, string $modePaiement = self::MODE_ESPECES, ?string $reference = null): self
    {
        $nouveauMontantPaye = min($this->montant, $this->montant_paye + $montant);

        $this->update([
            'montant_paye' => $nouveauMontantPaye,
            'statut' => $this->determinerStatut($this->montant, $nouveauMontantPaye),
            'mode_paiement' => $modePaiement,
            'date_paiement' => now(),
            'reference' => $reference ?? $this->reference,
        ]);

        return $this->fresh();
    }

    /**
     * Détermine le statut en fonction des montants
     */
    public static function determinerStatut(float $montant, float $montantPaye): string
    {
        if ($montantPaye >= $montant) {
            return self::STATUT_PAYE;
        } elseif ($montantPaye > 0) {
            return self::STATUT_PARTIEL;
        }
        return self::STATUT_IMPAYE;
    }

    /**
     * Liste des statuts disponibles
     */
    public static function statuts(): array
    {
        return [
            self::STATUT_PAYE => 'Payé',
            self::STATUT_IMPAYE => 'Impayé',
            self::STATUT_PARTIEL => 'Partiel',
        ];
    }

    /**
     * Liste des modes de paiement
     */
    public static function modesPaiement(): array
    {
        return [
            self::MODE_ESPECES => 'Espèces',
            self::MODE_VIREMENT => 'Virement bancaire',
            self::MODE_MOBILE => 'Mobile Money',
        ];
    }

    /**
     * Liste des mois
     */
    public static function mois(): array
    {
        return [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
    }

    /**
     * Liste des types de paiement
     */
    public static function typesPaiement(): array
    {
        return [
            self::TYPE_COTISATION => 'Cotisation mensuelle',
            self::TYPE_INSCRIPTION => 'Frais d\'inscription',
            self::TYPE_EQUIPEMENT => 'Équipement',
        ];
    }

    /**
     * Liste des types d'équipement
     */
    public static function typesEquipement(): array
    {
        return [
            self::EQUIPEMENT_MAILLOT => 'Maillot (Basket/Volley) - 4 000 FCFA',
            self::EQUIPEMENT_DOBOK_ENFANT => 'Dobok Enfant (Taekwondo) - 5 000 FCFA',
            self::EQUIPEMENT_DOBOK_JUNIOR => 'Dobok Junior (Taekwondo) - 6 000 à 7 000 FCFA',
            self::EQUIPEMENT_DOBOK_SENIOR => 'Dobok Senior (Taekwondo) - 8 000 à 10 000 FCFA',
        ];
    }

    /**
     * Prix suggéré pour chaque type d'équipement
     */
    public static function prixEquipement(): array
    {
        return [
            self::EQUIPEMENT_MAILLOT => 4000,
            self::EQUIPEMENT_DOBOK_ENFANT => 5000,
            self::EQUIPEMENT_DOBOK_JUNIOR => 7000,
            self::EQUIPEMENT_DOBOK_SENIOR => 10000,
        ];
    }

    /**
     * Libellé du type de paiement
     */
    public function getTypePaiementLibelleAttribute(): string
    {
        return self::typesPaiement()[$this->type_paiement] ?? $this->type_paiement;
    }

    /**
     * Libellé du type d'équipement
     */
    public function getTypeEquipementLibelleAttribute(): ?string
    {
        if (!$this->type_equipement) {
            return null;
        }
        return self::typesEquipement()[$this->type_equipement] ?? $this->type_equipement;
    }
}

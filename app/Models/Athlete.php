<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'telephone',
        'email',
        'adresse',
        'photo',
        'nom_tuteur',
        'telephone_tuteur',
        'date_inscription',
        'actif',
        'bulletin_token',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'date_inscription' => 'date',
            'actif' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    /**
     * Les disciplines de l'athlète
     */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'athlete_discipline')
            ->withPivot('date_inscription', 'actif')
            ->withTimestamps();
    }

    /**
     * Les disciplines actives de l'athlète
     */
    public function disciplinesActives(): BelongsToMany
    {
        return $this->disciplines()->wherePivot('actif', true);
    }

    /**
     * Les présences de l'athlète
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Les paiements de l'athlète
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Le suivi scolaire de l'athlète
     */
    public function suiviScolaire(): HasOne
    {
        return $this->hasOne(SuiviScolaire::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Les suivis scolaires (historique)
     */
    public function suivisScolaires(): HasMany
    {
        return $this->hasMany(SuiviScolaire::class);
    }

    /**
     * Les performances de l'athlète
     */
    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }

    /**
     * Les licences de l'athlète
     */
    public function licences(): HasMany
    {
        return $this->hasMany(Licence::class);
    }

    /**
     * Les certificats médicaux de l'athlète
     */
    public function certificatsMedicaux(): HasMany
    {
        return $this->hasMany(CertificatMedical::class);
    }

    /**
     * Le certificat médical valide de l'athlète
     */
    public function certificatMedicalValide(): ?CertificatMedical
    {
        return $this->certificatsMedicaux()
            ->where('statut', CertificatMedical::STATUT_VALIDE)
            ->where('date_expiration', '>=', now())
            ->first();
    }

    /**
     * Vérifie si l'athlète est apte médicalement
     */
    public function estApteMedicalement(): bool
    {
        $certificat = $this->certificatMedicalValide();
        return $certificat && $certificat->apte_competition && $certificat->apte_entrainement;
    }

    /**
     * La licence active de l'athlète pour une discipline
     */
    public function licenceActive(?Discipline $discipline = null): ?Licence
    {
        $query = $this->licences()->where('statut', Licence::STATUT_ACTIVE);
        
        if ($discipline) {
            $query->where('discipline_id', $discipline->id);
        }
        
        return $query->first();
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les athlètes actifs
     */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les athlètes inactifs
     */
    public function scopeInactifs(Builder $query): Builder
    {
        return $query->where('actif', false);
    }

    /**
     * Scope pour les athlètes avec arriérés
     */
    public function scopeAvecArrieres(Builder $query): Builder
    {
        return $query->whereHas('paiements', function ($q) {
            $q->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL]);
        });
    }

    /**
     * Scope pour les athlètes d'une discipline
     */
    public function scopeDeDiscipline(Builder $query, int $disciplineId): Builder
    {
        return $query->whereHas('disciplines', function ($q) use ($disciplineId) {
            $q->where('disciplines.id', $disciplineId);
        });
    }

    /**
     * Scope pour les athlètes par sexe
     */
    public function scopeParSexe(Builder $query, string $sexe): Builder
    {
        return $query->where('sexe', $sexe);
    }

    /**
     * Scope pour les athlètes mineurs
     */
    public function scopeMineurs(Builder $query): Builder
    {
        return $query->whereDate('date_naissance', '>', now()->subYears(18));
    }

    /**
     * Scope pour les athlètes majeurs
     */
    public function scopeMajeurs(Builder $query): Builder
    {
        return $query->whereDate('date_naissance', '<=', now()->subYears(18));
    }

    // ==================== ACCESSEURS ====================

    /**
     * Nom complet de l'athlète
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Âge de l'athlète
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->age;
    }

    /**
     * Calcule le total des arriérés
     */
    public function getArrieresAttribute(): float
    {
        return $this->paiements()
            ->whereIn('statut', [Paiement::STATUT_IMPAYE, Paiement::STATUT_PARTIEL])
            ->get()
            ->sum(fn($p) => $p->montant - $p->montant_paye);
    }

    /**
     * Taux de présence
     */
    public function getTauxPresenceAttribute(): float
    {
        $total = $this->presences()->count();
        if ($total === 0) {
            return 0;
        }
        $presents = $this->presences()->where('present', true)->count();
        return round(($presents / $total) * 100, 1);
    }

    /**
     * Catégorie d'âge
     */
    public function getCategorieAgeAttribute(): string
    {
        $age = $this->age;
        if ($age === null) {
            return 'Non défini';
        }
        if ($age < 10) {
            return 'Poussin';
        } elseif ($age < 13) {
            return 'Benjamin';
        } elseif ($age < 15) {
            return 'Minime';
        } elseif ($age < 18) {
            return 'Cadet';
        } elseif ($age < 21) {
            return 'Junior';
        }
        return 'Senior';
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
     * Vérifie si l'athlète est à jour de ses paiements
     */
    public function estAJourPaiements(): bool
    {
        return $this->arrieres <= 0;
    }

    /**
     * Vérifie si l'athlète est éligible (pas d'arriérés importants)
     */
    public function estEligible(float $seuilArrieres = 50000): bool
    {
        return $this->arrieres < $seuilArrieres;
    }

    /**
     * Vérifie si l'athlète est inscrit à une discipline
     */
    public function estInscritA(Discipline $discipline): bool
    {
        return $this->disciplines()
            ->where('discipline_id', $discipline->id)
            ->wherePivot('actif', true)
            ->exists();
    }

    /**
     * Calcule le tarif mensuel total
     */
    public function getTarifMensuelTotal(): float
    {
        return $this->disciplinesActives()->sum('tarif_mensuel');
    }

    /**
     * Récupère la dernière présence
     */
    public function getDernierePresence(): ?Presence
    {
        return $this->presences()->latest('date')->first();
    }

    /**
     * Récupère le dernier paiement
     */
    public function getDernierPaiement(): ?Paiement
    {
        return $this->paiements()
            ->where('statut', Paiement::STATUT_PAYE)
            ->latest('date_paiement')
            ->first();
    }

    /**
     * Vérifie si l'athlète est mineur
     */
    public function estMineur(): bool
    {
        return $this->age !== null && $this->age < 18;
    }
}

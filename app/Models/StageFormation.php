<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class StageFormation extends Model
{
    use HasFactory;

    protected $table = 'stages_formation';

    protected $fillable = [
        'titre',
        'code',
        'description',
        'type',
        'discipline_id',
        'date_debut',
        'date_fin',
        'lieu',
        'organisme',
        'programme',
        'duree_heures',
        'places_disponibles',
        'frais_inscription',
        'type_certification',
        'intitule_certification',
        'statut',
        'conditions_admission',
        'objectifs',
        'encadreurs',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'frais_inscription' => 'decimal:2',
        'encadreurs' => 'array',
    ];

    // Types de formation
    public const TYPES = [
        'formation_formateurs' => 'Formation des Formateurs',
        'recyclage' => 'Recyclage',
        'specialisation' => 'Spécialisation',
        'initiation' => 'Initiation',
        'perfectionnement' => 'Perfectionnement',
    ];

    // Types de certification
    public const TYPES_CERTIFICATION = [
        'diplome' => 'Diplôme',
        'certificat' => 'Certificat',
        'attestation' => 'Attestation',
    ];

    // Statuts
    public const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
    ];

    // Relations
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(InscriptionStage::class);
    }

    // Scopes
    public function scopePlanifie($query)
    {
        return $query->where('statut', 'planifie');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeAVenir($query)
    {
        return $query->where('date_debut', '>', now());
    }

    // Accesseurs
    public function getTypeLibelleAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type ?? 'Non défini';
    }

    public function getStatutLibelleAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut ?? 'Non défini';
    }

    public function getTypeCertificationLibelleAttribute(): string
    {
        return self::TYPES_CERTIFICATION[$this->type_certification] ?? $this->type_certification ?? 'Non défini';
    }

    public function getDureeJoursAttribute(): int
    {
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }

    public function getDureeSemainesAttribute(): float
    {
        return round($this->duree_jours / 7, 1);
    }

    public function getPlacesRestantesAttribute(): int
    {
        return max(0, $this->places_disponibles - $this->inscriptions()->whereNotIn('statut', ['abandon', 'echec'])->count());
    }

    public function getNombreInscritsAttribute(): int
    {
        return $this->inscriptions()->count();
    }

    public function getNombreDiplomesAttribute(): int
    {
        return $this->inscriptions()->where('statut', 'diplome')->count();
    }

    public function getEstCompletAttribute(): bool
    {
        return $this->places_restantes <= 0;
    }

    public function getEstEnCoursAttribute(): bool
    {
        $now = now();
        return $now->between($this->date_debut, $this->date_fin);
    }

    public function getEstTermineAttribute(): bool
    {
        return $this->date_fin->isPast();
    }

    public function getEstAVenirAttribute(): bool
    {
        return $this->date_debut->isFuture();
    }

    // Méthodes
    public static function genererCode(string $type = 'FF'): string
    {
        $prefixes = [
            'formation_formateurs' => 'FF',
            'recyclage' => 'RC',
            'specialisation' => 'SP',
            'initiation' => 'IN',
            'perfectionnement' => 'PF',
        ];
        
        $prefix = $prefixes[$type] ?? 'ST';
        $annee = now()->year;
        $dernier = self::where('code', 'like', "{$prefix}-{$annee}-%")->count() + 1;
        
        return sprintf('%s-%d-%03d', $prefix, $annee, $dernier);
    }

    public function mettreAJourStatut(): void
    {
        $now = now();
        
        if ($this->statut === 'annule') {
            return;
        }
        
        if ($now->lt($this->date_debut)) {
            $this->statut = 'planifie';
        } elseif ($now->between($this->date_debut, $this->date_fin)) {
            $this->statut = 'en_cours';
        } else {
            $this->statut = 'termine';
        }
        
        $this->save();
    }
}

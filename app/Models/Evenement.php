<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model
{
    use HasFactory;

    const TYPE_ENTRAINEMENT = 'entrainement';
    const TYPE_COMPETITION = 'competition';
    const TYPE_REUNION = 'reunion';
    const TYPE_STAGE = 'stage';
    const TYPE_AUTRE = 'autre';

    const TYPES = [
        self::TYPE_ENTRAINEMENT => 'Entraînement',
        self::TYPE_COMPETITION => 'Compétition',
        self::TYPE_REUNION => 'Réunion',
        self::TYPE_STAGE => 'Stage',
        self::TYPE_AUTRE => 'Autre',
    ];

    const COULEURS = [
        self::TYPE_ENTRAINEMENT => '#14532d',
        self::TYPE_COMPETITION => '#dc2626',
        self::TYPE_REUNION => '#2563eb',
        self::TYPE_STAGE => '#7c3aed',
        self::TYPE_AUTRE => '#6b7280',
    ];

    protected $fillable = [
        'titre',
        'description',
        'type',
        'discipline_id',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'lieu',
        'couleur',
        'toute_journee',
        'recurrent',
        'recurrence_type',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'toute_journee' => 'boolean',
            'recurrent' => 'boolean',
        ];
    }

    // ==================== RELATIONS ====================

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== SCOPES ====================

    public function scopeAVenir(Builder $query): Builder
    {
        return $query->where('date_debut', '>=', now()->startOfDay());
    }

    public function scopePasses(Builder $query): Builder
    {
        return $query->where('date_debut', '<', now()->startOfDay());
    }

    public function scopeEntreDates(Builder $query, $debut, $fin): Builder
    {
        return $query->where(function ($q) use ($debut, $fin) {
            $q->whereBetween('date_debut', [$debut, $fin])
                ->orWhereBetween('date_fin', [$debut, $fin])
                ->orWhere(function ($q) use ($debut, $fin) {
                    $q->where('date_debut', '<=', $debut)
                        ->where('date_fin', '>=', $fin);
                });
        });
    }

    public function scopeDeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeDeDiscipline(Builder $query, int $disciplineId): Builder
    {
        return $query->where('discipline_id', $disciplineId);
    }

    // ==================== ACCESSEURS ====================

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getEstPasseAttribute(): bool
    {
        $dateFin = $this->date_fin ?? $this->date_debut;
        return $dateFin < now()->startOfDay();
    }

    public function getEstAujourdhuiAttribute(): bool
    {
        $today = now()->startOfDay();
        $dateFin = $this->date_fin ?? $this->date_debut;
        return $this->date_debut <= $today && $dateFin >= $today;
    }

    public function getDureeJoursAttribute(): int
    {
        if (!$this->date_fin) {
            return 1;
        }
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }

    // ==================== METHODES ====================

    public function toFullCalendarEvent(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->titre,
            'start' => $this->date_debut->format('Y-m-d') . ($this->heure_debut ? 'T' . $this->heure_debut : ''),
            'end' => ($this->date_fin ?? $this->date_debut)->format('Y-m-d') . ($this->heure_fin ? 'T' . $this->heure_fin : ''),
            'color' => $this->couleur ?? self::COULEURS[$this->type] ?? '#14532d',
            'allDay' => $this->toute_journee,
            'extendedProps' => [
                'type' => $this->type,
                'type_label' => $this->type_label,
                'discipline' => $this->discipline?->nom,
                'lieu' => $this->lieu,
                'description' => $this->description,
            ],
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    public const TYPE_COMPETITION = 'competition';
    public const TYPE_TOURNOI = 'tournoi';
    public const TYPE_MATCH = 'match';
    public const TYPE_ENTRAINEMENT = 'entrainement';
    public const TYPE_EVENEMENT = 'evenement';
    public const TYPE_GALERIE = 'galerie';

    public const TYPES = [
        self::TYPE_COMPETITION => 'Competition',
        self::TYPE_TOURNOI => 'Tournoi',
        self::TYPE_MATCH => 'Match',
        self::TYPE_ENTRAINEMENT => 'Entrainement',
        self::TYPE_EVENEMENT => 'Evenement',
        self::TYPE_GALERIE => 'Galerie',
    ];

    protected $fillable = [
        'type',
        'titre',
        'description',
        'lieu',
        'image',
        'video_url',
        'discipline_id',
        'debut',
        'fin',
        'publie',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'debut' => 'datetime',
            'fin' => 'datetime',
            'publie' => 'boolean',
        ];
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function medias(): HasMany
    {
        return $this->hasMany(ActivityMedia::class)->orderBy('ordre');
    }

    public function photos(): HasMany
    {
        return $this->medias()->where('type', 'photo');
    }

    public function videos(): HasMany
    {
        return $this->medias()->where('type', 'video');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_COMPETITION => 'danger',
            self::TYPE_TOURNOI => 'warning',
            self::TYPE_MATCH => 'primary',
            self::TYPE_ENTRAINEMENT => 'info',
            self::TYPE_GALERIE => 'secondary',
            default => 'gray',
        };
    }

    public function isPast(): bool
    {
        return $this->debut < now();
    }

    public function isUpcoming(): bool
    {
        return $this->debut >= now();
    }
}

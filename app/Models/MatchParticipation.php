<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'athlete_id',
        'titulaire',
        'minutes_jouees',
        'points_marques',
        'passes_decisives',
        'rebonds',
        'interceptions',
        'fautes',
        'cartons_jaunes',
        'cartons_rouges',
        'note_performance',
        'remarques',
    ];

    protected $casts = [
        'titulaire' => 'boolean',
        'minutes_jouees' => 'integer',
        'points_marques' => 'integer',
        'passes_decisives' => 'integer',
        'rebonds' => 'integer',
        'interceptions' => 'integer',
        'fautes' => 'integer',
        'cartons_jaunes' => 'integer',
        'cartons_rouges' => 'integer',
        'note_performance' => 'decimal:1',
    ];

    /**
     * Relation avec le match
     */
    public function rencontre(): BelongsTo
    {
        return $this->belongsTo(Rencontre::class, 'match_id');
    }

    /**
     * Relation avec l'athlète
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Note formatée
     */
    public function getNoteFormateeAttribute(): string
    {
        return $this->note_performance ? number_format($this->note_performance, 1) . '/10' : '-';
    }

    /**
     * Statut (titulaire ou remplaçant)
     */
    public function getStatutAttribute(): string
    {
        return $this->titulaire ? 'Titulaire' : 'Remplaçant';
    }
}

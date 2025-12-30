<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rencontre extends Model
{
    use HasFactory;

    protected $table = 'matchs';

    protected $fillable = [
        'discipline_id',
        'date_match',
        'heure_match',
        'type_match',
        'adversaire',
        'lieu',
        'score_obd',
        'score_adversaire',
        'resultat',
        'type_competition',
        'nom_competition',
        'saison',
        'phase',
        'remarques',
    ];

    protected $casts = [
        'date_match' => 'date',
        'heure_match' => 'datetime:H:i',
        'score_obd' => 'integer',
        'score_adversaire' => 'integer',
    ];

    /**
     * Relation avec la discipline
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    /**
     * Relation avec les participations
     */
    public function participations(): HasMany
    {
        return $this->hasMany(MatchParticipation::class, 'match_id');
    }

    /**
     * Relation avec les athlètes via participations
     */
    public function athletes(): BelongsToMany
    {
        return $this->belongsToMany(Athlete::class, 'match_participations', 'match_id', 'athlete_id')
            ->withPivot([
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
            ])
            ->withTimestamps();
    }

    /**
     * Athlètes titulaires
     */
    public function titulaires(): BelongsToMany
    {
        return $this->athletes()->wherePivot('titulaire', true);
    }

    /**
     * Athlètes remplaçants
     */
    public function remplacants(): BelongsToMany
    {
        return $this->athletes()->wherePivot('titulaire', false);
    }

    /**
     * Combats Taekwondo
     */
    public function combatsTaekwondo(): HasMany
    {
        return $this->hasMany(CombatTaekwondo::class, 'rencontre_id');
    }

    /**
     * Vérifie si c'est un sport individuel (Taekwondo, etc.)
     */
    public function isSportIndividuel(): bool
    {
        $sportsIndividuels = ['taekwondo', 'judo', 'karate', 'boxe', 'lutte', 'athletisme'];
        return in_array(strtolower($this->discipline?->nom ?? ''), $sportsIndividuels);
    }

    /**
     * Score formaté
     */
    public function getScoreFormateAttribute(): string
    {
        if ($this->resultat === 'a_jouer') {
            return 'À jouer';
        }
        return ($this->score_obd ?? '-') . ' - ' . ($this->score_adversaire ?? '-');
    }

    /**
     * Libellé du résultat
     */
    public function getResultatLibelleAttribute(): string
    {
        return match($this->resultat) {
            'victoire' => 'Victoire',
            'defaite' => 'Défaite',
            'nul' => 'Match nul',
            'a_jouer' => 'À jouer',
            default => $this->resultat,
        };
    }

    /**
     * Couleur du résultat pour l'affichage
     */
    public function getResultatColorAttribute(): string
    {
        return match($this->resultat) {
            'victoire' => 'green',
            'defaite' => 'red',
            'nul' => 'yellow',
            'a_jouer' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Type de match formaté
     */
    public function getTypeMatchLibelleAttribute(): string
    {
        return $this->type_match === 'domicile' ? 'Domicile' : 'Extérieur';
    }

    /**
     * Type de compétition formaté
     */
    public function getTypeCompetitionLibelleAttribute(): string
    {
        return match($this->type_competition) {
            'championnat' => 'Championnat',
            'coupe' => 'Coupe',
            'tournoi' => 'Tournoi',
            'amical' => 'Match amical',
            default => $this->type_competition,
        };
    }

    /**
     * Phase formatée
     */
    public function getPhaseLibelleAttribute(): string
    {
        return match($this->phase) {
            'aller' => 'Match aller',
            'retour' => 'Match retour',
            'finale' => 'Finale',
            'demi_finale' => 'Demi-finale',
            'quart_finale' => 'Quart de finale',
            'poule' => 'Phase de poules',
            'autre' => 'Autre',
            default => $this->phase ?? '',
        };
    }

    /**
     * Nombre de participants
     */
    public function getNbParticipantsAttribute(): int
    {
        return $this->participations()->count();
    }

    /**
     * Total des points marqués par l'équipe
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->participations()->sum('points_marques') ?? 0;
    }

    /**
     * Meilleur marqueur du match
     */
    public function getMeilleurMarqueurAttribute()
    {
        $participation = $this->participations()
            ->whereNotNull('points_marques')
            ->orderByDesc('points_marques')
            ->with('athlete')
            ->first();

        return $participation?->athlete;
    }

    /**
     * Scope pour les matchs à venir
     */
    public function scopeAVenir($query)
    {
        return $query->where('resultat', 'a_jouer')
            ->where('date_match', '>=', now()->toDateString())
            ->orderBy('date_match');
    }

    /**
     * Scope pour les matchs passés
     */
    public function scopePasses($query)
    {
        return $query->where('resultat', '!=', 'a_jouer')
            ->orderByDesc('date_match');
    }

    /**
     * Scope pour une discipline
     */
    public function scopeParDiscipline($query, $disciplineId)
    {
        return $query->where('discipline_id', $disciplineId);
    }

    /**
     * Scope pour une saison
     */
    public function scopeParSaison($query, $saison)
    {
        return $query->where('saison', $saison);
    }

    /**
     * Types de match disponibles
     */
    public static function typesMatch(): array
    {
        return [
            'domicile' => 'Domicile',
            'exterieur' => 'Extérieur',
        ];
    }

    /**
     * Résultats disponibles
     */
    public static function resultats(): array
    {
        return [
            'a_jouer' => 'À jouer',
            'victoire' => 'Victoire',
            'defaite' => 'Défaite',
            'nul' => 'Match nul',
        ];
    }

    /**
     * Types de compétition disponibles
     */
    public static function typesCompetition(): array
    {
        return [
            'amical' => 'Match amical',
            'championnat' => 'Championnat',
            'coupe' => 'Coupe',
            'tournoi' => 'Tournoi',
        ];
    }

    /**
     * Phases disponibles
     */
    public static function phases(): array
    {
        return [
            'aller' => 'Match aller',
            'retour' => 'Match retour',
            'poule' => 'Phase de poules',
            'quart_finale' => 'Quart de finale',
            'demi_finale' => 'Demi-finale',
            'finale' => 'Finale',
            'autre' => 'Autre',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Performance extends Model
{
    use HasFactory;

    // Constantes pour les contextes
    public const CONTEXTE_ENTRAINEMENT = 'entrainement';
    public const CONTEXTE_MATCH = 'match';
    public const CONTEXTE_COMPETITION = 'competition';
    public const CONTEXTE_TEST_PHYSIQUE = 'test_physique';

    // Constantes pour les résultats de match
    public const RESULTAT_VICTOIRE = 'victoire';
    public const RESULTAT_DEFAITE = 'defaite';
    public const RESULTAT_NUL = 'nul';

    // Constantes pour les médailles
    public const MEDAILLE_OR = 'or';
    public const MEDAILLE_ARGENT = 'argent';
    public const MEDAILLE_BRONZE = 'bronze';

    protected $fillable = [
        'athlete_id',
        'discipline_id',
        'date_evaluation',
        'type_evaluation',
        'contexte',
        'resultat_match',
        'points_marques',
        'points_encaisses',
        'score',
        'unite',
        'observations',
        'competition',
        'adversaire',
        'lieu',
        'classement',
        'medaille',
        'note_physique',
        'note_technique',
        'note_comportement',
        'note_globale',
    ];

    protected function casts(): array
    {
        return [
            'date_evaluation' => 'date',
            'score' => 'decimal:2',
            'note_globale' => 'decimal:1',
            'points_marques' => 'integer',
            'points_encaisses' => 'integer',
            'note_physique' => 'integer',
            'note_technique' => 'integer',
            'note_comportement' => 'integer',
        ];
    }

    /**
     * Liste des contextes
     */
    public static function contextes(): array
    {
        return [
            self::CONTEXTE_ENTRAINEMENT => 'Entraînement',
            self::CONTEXTE_MATCH => 'Match',
            self::CONTEXTE_COMPETITION => 'Compétition',
            self::CONTEXTE_TEST_PHYSIQUE => 'Test physique',
        ];
    }

    /**
     * Liste des résultats de match
     */
    public static function resultatsMatch(): array
    {
        return [
            self::RESULTAT_VICTOIRE => 'Victoire',
            self::RESULTAT_DEFAITE => 'Défaite',
            self::RESULTAT_NUL => 'Nul',
        ];
    }

    /**
     * Liste des médailles
     */
    public static function medailles(): array
    {
        return [
            self::MEDAILLE_OR => 'Or 🥇',
            self::MEDAILLE_ARGENT => 'Argent 🥈',
            self::MEDAILLE_BRONZE => 'Bronze 🥉',
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

    /**
     * La discipline concernée
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les performances en compétition
     */
    public function scopeEnCompetition(Builder $query): Builder
    {
        return $query->whereNotNull('competition');
    }

    /**
     * Scope pour les performances avec score
     */
    public function scopeAvecScore(Builder $query): Builder
    {
        return $query->whereNotNull('score');
    }

    /**
     * Scope pour une discipline
     */
    public function scopePourDiscipline(Builder $query, int $disciplineId): Builder
    {
        return $query->where('discipline_id', $disciplineId);
    }

    /**
     * Scope pour un type d'évaluation
     */
    public function scopePourType(Builder $query, string $type): Builder
    {
        return $query->where('type_evaluation', $type);
    }

    /**
     * Scope pour une période
     */
    public function scopePourPeriode(Builder $query, $dateDebut, $dateFin): Builder
    {
        return $query->whereBetween('date_evaluation', [$dateDebut, $dateFin]);
    }

    // ==================== ACCESSEURS ====================

    /**
     * Formatage du score avec unité
     */
    public function getScoreFormateAttribute(): string
    {
        if ($this->score === null) {
            return 'N/A';
        }
        return trim("{$this->score} {$this->unite}");
    }

    /**
     * Libellé du classement
     */
    public function getClassementLibelleAttribute(): ?string
    {
        if (!$this->classement) {
            return null;
        }
        
        $suffixe = match($this->classement) {
            1 => 'er',
            default => 'ème',
        };
        
        return "{$this->classement}{$suffixe}";
    }

    /**
     * Vérifie si c'est un podium
     */
    public function getEstPodiumAttribute(): bool
    {
        return $this->classement !== null && $this->classement <= 3;
    }

    // ==================== METHODES METIER ====================

    /**
     * Vérifie si c'est une performance en compétition
     */
    public function estEnCompetition(): bool
    {
        return !empty($this->competition);
    }

    /**
     * Vérifie si c'est un record personnel
     */
    public function estRecordPersonnel(): bool
    {
        if ($this->score === null) {
            return false;
        }

        $meilleurePerf = Performance::where('athlete_id', $this->athlete_id)
            ->where('discipline_id', $this->discipline_id)
            ->where('id', '!=', $this->id)
            ->whereNotNull('score')
            ->max('score');

        return $meilleurePerf === null || $this->score > $meilleurePerf;
    }

    /**
     * Compare avec une autre performance
     */
    public function comparerAvec(Performance $autre): array
    {
        $difference = null;
        $pourcentage = null;

        if ($this->score !== null && $autre->score !== null && $autre->score > 0) {
            $difference = $this->score - $autre->score;
            $pourcentage = round(($difference / $autre->score) * 100, 2);
        }

        return [
            'difference' => $difference,
            'pourcentage' => $pourcentage,
            'meilleure' => $this->score > $autre->score,
        ];
    }

    /**
     * Calcule la note globale automatiquement
     */
    public function calculerNoteGlobale(): float
    {
        $notes = array_filter([
            $this->note_physique,
            $this->note_technique,
            $this->note_comportement,
        ]);

        if (empty($notes)) {
            return 0;
        }

        return round(array_sum($notes) / count($notes), 1);
    }

    /**
     * Libellé du contexte
     */
    public function getContexteLibelleAttribute(): string
    {
        return self::contextes()[$this->contexte] ?? $this->contexte;
    }

    /**
     * Libellé du résultat match
     */
    public function getResultatMatchLibelleAttribute(): ?string
    {
        if (!$this->resultat_match) {
            return null;
        }
        return self::resultatsMatch()[$this->resultat_match] ?? $this->resultat_match;
    }

    /**
     * Libellé de la médaille
     */
    public function getMedailleLibelleAttribute(): ?string
    {
        if (!$this->medaille) {
            return null;
        }
        return self::medailles()[$this->medaille] ?? $this->medaille;
    }

    /**
     * Score du match formaté (ex: 3-1)
     */
    public function getScoreMatchAttribute(): ?string
    {
        if ($this->points_marques === null || $this->points_encaisses === null) {
            return null;
        }
        return "{$this->points_marques} - {$this->points_encaisses}";
    }

    /**
     * Couleur du résultat
     */
    public function getCouleurResultatAttribute(): string
    {
        return match($this->resultat_match) {
            self::RESULTAT_VICTOIRE => 'success',
            self::RESULTAT_DEFAITE => 'danger',
            self::RESULTAT_NUL => 'warning',
            default => 'gray',
        };
    }

    /**
     * Statistiques d'un athlète
     */
    public static function statistiquesAthlete(int $athleteId, ?int $disciplineId = null): array
    {
        $query = self::where('athlete_id', $athleteId);
        
        if ($disciplineId) {
            $query->where('discipline_id', $disciplineId);
        }

        $performances = $query->get();

        $matchs = $performances->where('contexte', self::CONTEXTE_MATCH);
        $competitions = $performances->where('contexte', self::CONTEXTE_COMPETITION);

        return [
            'total_performances' => $performances->count(),
            'matchs' => [
                'total' => $matchs->count(),
                'victoires' => $matchs->where('resultat_match', self::RESULTAT_VICTOIRE)->count(),
                'defaites' => $matchs->where('resultat_match', self::RESULTAT_DEFAITE)->count(),
                'nuls' => $matchs->where('resultat_match', self::RESULTAT_NUL)->count(),
                'points_marques' => $matchs->sum('points_marques'),
                'points_encaisses' => $matchs->sum('points_encaisses'),
            ],
            'competitions' => [
                'total' => $competitions->count(),
                'medailles_or' => $competitions->where('medaille', self::MEDAILLE_OR)->count(),
                'medailles_argent' => $competitions->where('medaille', self::MEDAILLE_ARGENT)->count(),
                'medailles_bronze' => $competitions->where('medaille', self::MEDAILLE_BRONZE)->count(),
                'podiums' => $competitions->whereIn('classement', [1, 2, 3])->count(),
            ],
            'notes' => [
                'moyenne_physique' => round($performances->avg('note_physique') ?? 0, 1),
                'moyenne_technique' => round($performances->avg('note_technique') ?? 0, 1),
                'moyenne_comportement' => round($performances->avg('note_comportement') ?? 0, 1),
                'moyenne_globale' => round($performances->avg('note_globale') ?? 0, 1),
            ],
            'entrainements' => $performances->where('contexte', self::CONTEXTE_ENTRAINEMENT)->count(),
            'tests_physiques' => $performances->where('contexte', self::CONTEXTE_TEST_PHYSIQUE)->count(),
        ];
    }

    /**
     * Statistiques d'une discipline (équipe)
     */
    public static function statistiquesDiscipline(int $disciplineId): array
    {
        $performances = self::where('discipline_id', $disciplineId)->get();

        $matchs = $performances->where('contexte', self::CONTEXTE_MATCH);
        $competitions = $performances->where('contexte', self::CONTEXTE_COMPETITION);

        $victoires = $matchs->where('resultat_match', self::RESULTAT_VICTOIRE)->count();
        $totalMatchs = $matchs->count();

        return [
            'total_performances' => $performances->count(),
            'nb_athletes' => $performances->pluck('athlete_id')->unique()->count(),
            'matchs' => [
                'total' => $totalMatchs,
                'victoires' => $victoires,
                'defaites' => $matchs->where('resultat_match', self::RESULTAT_DEFAITE)->count(),
                'nuls' => $matchs->where('resultat_match', self::RESULTAT_NUL)->count(),
                'taux_victoire' => $totalMatchs > 0 ? round(($victoires / $totalMatchs) * 100, 1) : 0,
                'points_marques' => $matchs->sum('points_marques'),
                'points_encaisses' => $matchs->sum('points_encaisses'),
            ],
            'competitions' => [
                'total' => $competitions->count(),
                'medailles_or' => $competitions->where('medaille', self::MEDAILLE_OR)->count(),
                'medailles_argent' => $competitions->where('medaille', self::MEDAILLE_ARGENT)->count(),
                'medailles_bronze' => $competitions->where('medaille', self::MEDAILLE_BRONZE)->count(),
                'total_medailles' => $competitions->whereNotNull('medaille')->count(),
            ],
            'notes' => [
                'moyenne_globale' => round($performances->avg('note_globale') ?? 0, 1),
            ],
        ];
    }
}

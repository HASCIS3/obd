<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatTaekwondo extends Model
{
    protected $table = 'combats_taekwondo';

    protected $fillable = [
        'rencontre_id',
        'athlete_rouge_id',
        'nom_rouge',
        'club_rouge',
        'categorie_rouge',
        'athlete_bleu_id',
        'nom_bleu',
        'club_bleu',
        'categorie_bleu',
        'rounds',
        'score_rouge',
        'score_bleu',
        'statut',
        'vainqueur',
        'type_victoire',
        'round_actuel',
        'categorie_poids',
        'categorie_age',
        'remarques',
    ];

    protected function casts(): array
    {
        return [
            'rounds' => 'array',
            'score_rouge' => 'integer',
            'score_bleu' => 'integer',
            'round_actuel' => 'integer',
        ];
    }

    public function rencontre(): BelongsTo
    {
        return $this->belongsTo(Rencontre::class, 'rencontre_id');
    }

    public function athleteRouge(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_rouge_id');
    }

    public function athleteBleu(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_bleu_id');
    }

    public function getNomRougeCompletAttribute(): string
    {
        return $this->athleteRouge?->nom_complet ?? $this->nom_rouge ?? 'Combattant Rouge';
    }

    public function getNomBleuCompletAttribute(): string
    {
        return $this->athleteBleu?->nom_complet ?? $this->nom_bleu ?? 'Combattant Bleu';
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'a_jouer' => 'À jouer',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'a_jouer' => 'gray',
            'en_cours' => 'yellow',
            'termine' => 'green',
            default => 'gray',
        };
    }

    public function getVainqueurLabelAttribute(): string
    {
        return match($this->vainqueur) {
            'rouge' => $this->nom_rouge_complet,
            'bleu' => $this->nom_bleu_complet,
            'nul' => 'Match nul',
            default => 'Non déterminé',
        };
    }

    public function calculerScores(): void
    {
        $scoreRouge = 0;
        $scoreBleu = 0;

        if ($this->rounds) {
            foreach ($this->rounds as $round) {
                $scoreRouge += $this->calculerScoreRound($round['rouge'] ?? []);
                $scoreBleu += $this->calculerScoreRound($round['bleu'] ?? []);
                
                // Gam-jeom: chaque pénalité ajoute 1 point à l'adversaire
                $scoreRouge += ($round['bleu']['gamjeom'] ?? 0);
                $scoreBleu += ($round['rouge']['gamjeom'] ?? 0);
            }
        }

        $this->score_rouge = $scoreRouge;
        $this->score_bleu = $scoreBleu;
    }

    private function calculerScoreRound(array $actions): int
    {
        return 
            (($actions['poing_tronc'] ?? 0) * 1) +
            (($actions['pied_tronc'] ?? 0) * 2) +
            (($actions['pied_rotatif_tronc'] ?? 0) * 4) +
            (($actions['pied_tete'] ?? 0) * 3) +
            (($actions['pied_rotatif_tete'] ?? 0) * 5);
    }

    public function verifierVictoireAutomatique(): ?string
    {
        $ecart = abs($this->score_rouge - $this->score_bleu);
        
        if ($ecart >= 20) {
            return 'ecart_20';
        }

        return null;
    }

    public static function getDefaultRounds(): array
    {
        return [
            1 => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            2 => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            3 => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
            'golden' => [
                'rouge' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
                'bleu' => ['poing_tronc' => 0, 'pied_tronc' => 0, 'pied_rotatif_tronc' => 0, 'pied_tete' => 0, 'pied_rotatif_tete' => 0, 'gamjeom' => 0],
            ],
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'telephone',
        'telephone_secondaire',
        'adresse',
        'profession',
        'lien_parente',
        'notes',
        'recevoir_notifications',
        'recevoir_sms',
        'actif',
    ];

    protected $casts = [
        'recevoir_notifications' => 'boolean',
        'recevoir_sms' => 'boolean',
        'actif' => 'boolean',
    ];

    const LIENS_PARENTE = [
        'pere' => 'Père',
        'mere' => 'Mère',
        'tuteur' => 'Tuteur',
        'autre' => 'Autre',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function athletes(): BelongsToMany
    {
        return $this->belongsToMany(Athlete::class, 'athlete_parent', 'parent_id', 'athlete_id')
            ->withPivot(['lien', 'contact_principal', 'autorise_recuperation'])
            ->withTimestamps();
    }

    // Alias pour "enfants"
    public function enfants(): BelongsToMany
    {
        return $this->athletes();
    }

    // Accesseurs
    public function getNomCompletAttribute(): string
    {
        return $this->user->name ?? 'Parent inconnu';
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    public function getLienParenteLibelleAttribute(): string
    {
        return self::LIENS_PARENTE[$this->lien_parente] ?? $this->lien_parente;
    }

    public function getNombreEnfantsAttribute(): int
    {
        return $this->athletes()->count();
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeAvecNotifications($query)
    {
        return $query->where('recevoir_notifications', true);
    }

    public function scopeAvecSms($query)
    {
        return $query->where('recevoir_sms', true);
    }

    // Méthodes utilitaires
    public function peutVoirAthlete(Athlete $athlete): bool
    {
        return $this->athletes()->where('athlete_id', $athlete->id)->exists();
    }

    public function getPresencesEnfants()
    {
        $athleteIds = $this->athletes()->pluck('athletes.id');
        return Presence::whereIn('athlete_id', $athleteIds)
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();
    }

    public function getPaiementsEnfants()
    {
        $athleteIds = $this->athletes()->pluck('athletes.id');
        return Paiement::whereIn('athlete_id', $athleteIds)
            ->orderBy('date_paiement', 'desc')
            ->get();
    }

    public function getSuivisScolairesEnfants()
    {
        $athleteIds = $this->athletes()->pluck('athletes.id');
        return SuiviScolaire::whereIn('athlete_id', $athleteIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saison extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'date_debut',
        'date_fin',
        'active',
        'archivee',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'active' => 'boolean',
            'archivee' => 'boolean',
        ];
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeNonArchivees(Builder $query): Builder
    {
        return $query->where('archivee', false);
    }

    // ==================== ACCESSEURS ====================

    public function getEstEnCoursAttribute(): bool
    {
        return $this->date_debut <= now() && $this->date_fin >= now();
    }

    public function getEstTermineeAttribute(): bool
    {
        return $this->date_fin < now();
    }

    public function getEstFutureAttribute(): bool
    {
        return $this->date_debut > now();
    }

    public function getDureeJoursAttribute(): int
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }

    // ==================== METHODES STATIQUES ====================

    public static function actuelle(): ?self
    {
        return self::where('active', true)->first();
    }

    public static function genererNom(int $anneeDebut): string
    {
        return $anneeDebut . '-' . ($anneeDebut + 1);
    }

    public static function creerNouvelle(int $anneeDebut, ?string $description = null): self
    {
        return self::create([
            'nom' => self::genererNom($anneeDebut),
            'date_debut' => "{$anneeDebut}-09-01",
            'date_fin' => ($anneeDebut + 1) . "-08-31",
            'active' => false,
            'archivee' => false,
            'description' => $description,
        ]);
    }

    // ==================== METHODES ====================

    public function activer(): void
    {
        // Désactiver toutes les autres saisons
        self::where('id', '!=', $this->id)->update(['active' => false]);
        
        $this->update(['active' => true]);
    }

    public function archiver(): void
    {
        $this->update([
            'active' => false,
            'archivee' => true,
        ]);
    }
}

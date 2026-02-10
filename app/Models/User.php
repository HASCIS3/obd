<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_COACH = 'coach';
    public const ROLE_PARENT = 'parent';
    public const ROLE_ATHLETE = 'athlete';

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'athlete_id',
        'name',
        'email',
        'password',
        'role',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est coach
     */
    public function isCoach(): bool
    {
        return $this->role === self::ROLE_COACH && $this->athlete_id === null;
    }

    public function isAthlete(): bool
    {
        return $this->athlete_id !== null;
    }

    /**
     * Relation avec le profil coach
     */
    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    /**
     * Relation avec le profil parent
     */
    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentModel::class);
    }

    /**
     * Vérifie si l'utilisateur est parent
     */
    public function isParent(): bool
    {
        return $this->role === self::ROLE_PARENT;
    }

    /**
     * Obtenir l'URL de la photo de profil
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }
}

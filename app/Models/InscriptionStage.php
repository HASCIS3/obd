<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscriptionStage extends Model
{
    use HasFactory;

    protected $table = 'inscriptions_stage';

    protected $fillable = [
        'stage_formation_id',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'telephone',
        'email',
        'adresse',
        'fonction',
        'structure',
        'niveau_etude',
        'experience',
        'coach_id',
        'statut',
        'note_finale',
        'appreciation',
        'numero_certificat',
        'date_delivrance',
        'certificat_delivre',
        'observations',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_delivrance' => 'date',
        'note_finale' => 'decimal:2',
        'certificat_delivre' => 'boolean',
    ];

    // Statuts
    public const STATUTS = [
        'inscrit' => 'Inscrit',
        'confirme' => 'Confirmé',
        'en_formation' => 'En formation',
        'diplome' => 'Diplômé',
        'echec' => 'Échec',
        'abandon' => 'Abandon',
    ];

    // Niveaux d'étude
    public const NIVEAUX_ETUDE = [
        'primaire' => 'Primaire',
        'secondaire' => 'Secondaire',
        'bac' => 'Baccalauréat',
        'bac+2' => 'Bac+2',
        'licence' => 'Licence (Bac+3)',
        'master' => 'Master (Bac+5)',
        'doctorat' => 'Doctorat',
        'autre' => 'Autre',
    ];

    // Relations
    public function stageFormation(): BelongsTo
    {
        return $this->belongsTo(StageFormation::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    // Accesseurs
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getStatutLibelleAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getNiveauEtudeLibelleAttribute(): string
    {
        return self::NIVEAUX_ETUDE[$this->niveau_etude] ?? $this->niveau_etude;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    public function getEstDiplomeAttribute(): bool
    {
        return $this->statut === 'diplome';
    }

    public function getEstEnFormationAttribute(): bool
    {
        return in_array($this->statut, ['confirme', 'en_formation']);
    }

    // Méthodes
    public static function genererNumeroCertificat(StageFormation $stage): string
    {
        $prefixes = [
            'diplome' => 'DIP',
            'certificat' => 'CERT',
            'attestation' => 'ATT',
        ];
        
        $prefix = $prefixes[$stage->type_certification] ?? 'CERT';
        $annee = now()->year;
        $numero = self::where('stage_formation_id', $stage->id)
            ->whereNotNull('numero_certificat')
            ->count() + 1;
        
        return sprintf('%s-%s-%d-%03d', $prefix, $stage->code, $annee, $numero);
    }

    public function delivrerCertificat(): void
    {
        if (!$this->numero_certificat) {
            $this->numero_certificat = self::genererNumeroCertificat($this->stageFormation);
        }
        
        $this->date_delivrance = now();
        $this->certificat_delivre = true;
        $this->statut = 'diplome';
        $this->save();
    }

    public function annulerCertificat(): void
    {
        $this->certificat_delivre = false;
        $this->date_delivrance = null;
        $this->save();
    }
}

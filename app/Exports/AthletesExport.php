<?php

namespace App\Exports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AthletesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Athlete::with(['disciplines', 'licences', 'certificatsMedicaux']);

        if (!empty($this->filters['discipline_id'])) {
            $query->whereHas('disciplines', fn($q) => $q->where('disciplines.id', $this->filters['discipline_id']));
        }

        if (isset($this->filters['actif'])) {
            $query->where('actif', $this->filters['actif']);
        }

        return $query->orderBy('nom')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Date de naissance',
            'Âge',
            'Sexe',
            'Catégorie',
            'Téléphone',
            'Email',
            'Adresse',
            'Tuteur',
            'Tél. Tuteur',
            'Disciplines',
            'Date inscription',
            'Statut',
            'Licence active',
            'Certificat médical',
            'Taux présence',
            'Arriérés (FCFA)',
        ];
    }

    public function map($athlete): array
    {
        $licenceActive = $athlete->licences->where('statut', 'active')->first();
        $certificatValide = $athlete->certificatsMedicaux->where('statut', 'valide')->first();

        return [
            $athlete->id,
            $athlete->nom,
            $athlete->prenom,
            $athlete->date_naissance?->format('d/m/Y'),
            $athlete->age,
            $athlete->sexe === 'M' ? 'Masculin' : 'Féminin',
            $athlete->categorie_age,
            $athlete->telephone,
            $athlete->email,
            $athlete->adresse,
            $athlete->nom_tuteur,
            $athlete->telephone_tuteur,
            $athlete->disciplines->pluck('nom')->implode(', '),
            $athlete->date_inscription?->format('d/m/Y'),
            $athlete->actif ? 'Actif' : 'Inactif',
            $licenceActive ? $licenceActive->numero_licence : 'Aucune',
            $certificatValide ? 'Valide jusqu\'au ' . $certificatValide->date_expiration->format('d/m/Y') : 'Aucun',
            $athlete->taux_presence . '%',
            number_format($athlete->arrieres, 0, ',', ' '),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

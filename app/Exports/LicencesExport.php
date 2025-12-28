<?php

namespace App\Exports;

use App\Models\Licence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LicencesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Licence::with(['athlete', 'discipline']);

        if (!empty($this->filters['statut'])) {
            $query->where('statut', $this->filters['statut']);
        }

        if (!empty($this->filters['discipline_id'])) {
            $query->where('discipline_id', $this->filters['discipline_id']);
        }

        if (!empty($this->filters['saison'])) {
            $query->where('saison', $this->filters['saison']);
        }

        return $query->orderBy('date_expiration')->get();
    }

    public function headings(): array
    {
        return [
            'N° Licence',
            'Athlète',
            'Discipline',
            'Fédération',
            'Type',
            'Catégorie',
            'Saison',
            'Date émission',
            'Date expiration',
            'Statut',
            'Frais (FCFA)',
            'Payée',
        ];
    }

    public function map($licence): array
    {
        return [
            $licence->numero_licence,
            $licence->athlete->nom_complet,
            $licence->discipline->nom,
            $licence->federation,
            ucfirst($licence->type),
            $licence->categorie ?? 'N/A',
            $licence->saison ?? 'N/A',
            $licence->date_emission->format('d/m/Y'),
            $licence->date_expiration->format('d/m/Y'),
            ucfirst($licence->statut),
            number_format($licence->frais_licence, 0, ',', ' '),
            $licence->paye ? 'Oui' : 'Non',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

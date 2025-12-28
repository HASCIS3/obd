<?php

namespace App\Exports;

use App\Models\Paiement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaiementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Paiement::with(['athlete', 'discipline']);

        if (!empty($this->filters['statut'])) {
            $query->where('statut', $this->filters['statut']);
        }

        if (!empty($this->filters['mois'])) {
            $query->where('mois', $this->filters['mois']);
        }

        if (!empty($this->filters['annee'])) {
            $query->where('annee', $this->filters['annee']);
        }

        return $query->orderBy('date_paiement', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Athlète',
            'Discipline',
            'Mois',
            'Année',
            'Montant (FCFA)',
            'Montant payé (FCFA)',
            'Reste (FCFA)',
            'Statut',
            'Type',
            'Date paiement',
            'Mode paiement',
        ];
    }

    public function map($paiement): array
    {
        return [
            $paiement->id,
            $paiement->athlete->nom_complet,
            $paiement->discipline->nom ?? 'N/A',
            $paiement->mois,
            $paiement->annee,
            number_format($paiement->montant, 0, ',', ' '),
            number_format($paiement->montant_paye, 0, ',', ' '),
            number_format($paiement->montant - $paiement->montant_paye, 0, ',', ' '),
            ucfirst($paiement->statut),
            ucfirst($paiement->type_paiement ?? 'mensualité'),
            $paiement->date_paiement?->format('d/m/Y'),
            $paiement->mode_paiement ?? 'Espèces',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

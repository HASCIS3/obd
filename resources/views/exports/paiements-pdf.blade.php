<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Paiements</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { text-align: center; color: #14532d; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #14532d; color: white; padding: 8px 4px; text-align: left; font-size: 9px; }
        td { padding: 6px 4px; border-bottom: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 8px; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .totaux { margin-top: 20px; background: #f3f4f6; padding: 15px; border-radius: 5px; }
        .totaux h3 { margin: 0 0 10px 0; color: #14532d; }
        .totaux-grid { display: flex; justify-content: space-between; }
        .totaux-item { text-align: center; }
        .totaux-value { font-size: 14px; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; color: #666; font-size: 8px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Liste des Paiements - OBD</h1>
    <p class="subtitle">Généré le {{ now()->format('d/m/Y à H:i') }} - {{ $paiements->count() }} paiement(s)</p>

    <table>
        <thead>
            <tr>
                <th>Athlète</th>
                <th>Discipline</th>
                <th>Période</th>
                <th class="text-right">Montant</th>
                <th class="text-right">Payé</th>
                <th class="text-right">Reste</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiements as $paiement)
                <tr>
                    <td>{{ $paiement->athlete->nom_complet }}</td>
                    <td>{{ $paiement->discipline->nom ?? '-' }}</td>
                    <td>{{ $paiement->mois }}/{{ $paiement->annee }}</td>
                    <td class="text-right">{{ number_format($paiement->montant, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($paiement->montant_paye, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($paiement->montant - $paiement->montant_paye, 0, ',', ' ') }}</td>
                    <td>
                        <span class="badge {{ $paiement->statut === 'paye' ? 'badge-success' : ($paiement->statut === 'impaye' ? 'badge-danger' : 'badge-warning') }}">
                            {{ ucfirst($paiement->statut) }}
                        </span>
                    </td>
                    <td>{{ $paiement->date_paiement?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totaux">
        <h3>Récapitulatif</h3>
        <table>
            <tr>
                <td><strong>Total attendu:</strong></td>
                <td class="text-right">{{ number_format($totaux['montant'], 0, ',', ' ') }} FCFA</td>
                <td><strong>Total payé:</strong></td>
                <td class="text-right">{{ number_format($totaux['paye'], 0, ',', ' ') }} FCFA</td>
                <td><strong>Reste à percevoir:</strong></td>
                <td class="text-right">{{ number_format($totaux['reste'], 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        OBD - Centre Sportif | Document généré automatiquement
    </div>
</body>
</html>

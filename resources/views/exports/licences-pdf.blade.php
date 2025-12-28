<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Licences</title>
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
        .footer { margin-top: 20px; text-align: center; color: #666; font-size: 8px; }
    </style>
</head>
<body>
    <h1>Liste des Licences Sportives - OBD</h1>
    <p class="subtitle">Généré le {{ now()->format('d/m/Y à H:i') }} - {{ $licences->count() }} licence(s)</p>

    <table>
        <thead>
            <tr>
                <th>N° Licence</th>
                <th>Athlète</th>
                <th>Discipline</th>
                <th>Catégorie</th>
                <th>Saison</th>
                <th>Émission</th>
                <th>Expiration</th>
                <th>Statut</th>
                <th>Payée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($licences as $licence)
                <tr>
                    <td>{{ $licence->numero_licence }}</td>
                    <td>{{ $licence->athlete->nom_complet }}</td>
                    <td>{{ $licence->discipline->nom }}</td>
                    <td>{{ $licence->categorie ?? '-' }}</td>
                    <td>{{ $licence->saison ?? '-' }}</td>
                    <td>{{ $licence->date_emission->format('d/m/Y') }}</td>
                    <td>{{ $licence->date_expiration->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $licence->statut === 'active' ? 'badge-success' : ($licence->statut === 'expiree' ? 'badge-danger' : 'badge-warning') }}">
                            {{ ucfirst($licence->statut) }}
                        </span>
                    </td>
                    <td>{{ $licence->paye ? 'Oui' : 'Non' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        OBD - Centre Sportif | Document généré automatiquement
    </div>
</body>
</html>

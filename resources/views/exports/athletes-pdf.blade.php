<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Athlètes</title>
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
        .footer { margin-top: 20px; text-align: center; color: #666; font-size: 8px; }
    </style>
</head>
<body>
    <h1>Liste des Athlètes - OBD</h1>
    <p class="subtitle">Généré le {{ now()->format('d/m/Y à H:i') }} - {{ $athletes->count() }} athlète(s)</p>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Âge</th>
                <th>Sexe</th>
                <th>Catégorie</th>
                <th>Disciplines</th>
                <th>Statut</th>
                <th>Licence</th>
                <th>Certificat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($athletes as $athlete)
                @php
                    $licenceActive = $athlete->licences->where('statut', 'active')->first();
                    $certificatValide = $athlete->certificatsMedicaux->where('statut', 'valide')->first();
                @endphp
                <tr>
                    <td>{{ $athlete->nom }}</td>
                    <td>{{ $athlete->prenom }}</td>
                    <td>{{ $athlete->age ?? '-' }}</td>
                    <td>{{ $athlete->sexe }}</td>
                    <td>{{ $athlete->categorie_age }}</td>
                    <td>{{ $athlete->disciplines->pluck('nom')->implode(', ') }}</td>
                    <td>
                        <span class="badge {{ $athlete->actif ? 'badge-success' : 'badge-danger' }}">
                            {{ $athlete->actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td>{{ $licenceActive ? 'Oui' : 'Non' }}</td>
                    <td>{{ $certificatValide ? 'Valide' : 'Non' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        OBD - Centre Sportif | Document généré automatiquement
    </div>
</body>
</html>

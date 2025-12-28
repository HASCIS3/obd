<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche Athlète - {{ $athlete->nom_complet }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #14532d; padding-bottom: 15px; }
        .header h1 { color: #14532d; margin: 0; }
        .header p { color: #666; margin: 5px 0 0 0; }
        .section { margin-bottom: 20px; }
        .section-title { background-color: #14532d; color: white; padding: 8px 12px; margin-bottom: 10px; font-weight: bold; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 30%; padding: 5px; font-weight: bold; color: #666; }
        .info-value { display: table-cell; padding: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; padding: 8px; text-align: left; font-size: 10px; border-bottom: 1px solid #ddd; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .stats-box { background: #f9fafb; padding: 15px; border-radius: 5px; margin-top: 10px; }
        .stats-grid { display: table; width: 100%; }
        .stat-item { display: table-cell; text-align: center; padding: 10px; }
        .stat-value { font-size: 18px; font-weight: bold; color: #14532d; }
        .stat-label { font-size: 9px; color: #666; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 9px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FICHE ATHLÈTE</h1>
        <p>OBD - Centre Sportif</p>
    </div>

    <div class="section">
        <div class="section-title">Informations Personnelles</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom complet</div>
                <div class="info-value">{{ $athlete->nom_complet }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de naissance</div>
                <div class="info-value">{{ $athlete->date_naissance?->format('d/m/Y') ?? 'Non renseignée' }} ({{ $athlete->age }} ans)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Sexe</div>
                <div class="info-value">{{ $athlete->sexe === 'M' ? 'Masculin' : 'Féminin' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Catégorie</div>
                <div class="info-value">{{ $athlete->categorie_age }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $athlete->telephone ?? 'Non renseigné' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $athlete->email ?? 'Non renseigné' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse</div>
                <div class="info-value">{{ $athlete->adresse ?? 'Non renseignée' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tuteur</div>
                <div class="info-value">{{ $athlete->nom_tuteur ?? 'Non renseigné' }} - {{ $athlete->telephone_tuteur ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'inscription</div>
                <div class="info-value">{{ $athlete->date_inscription?->format('d/m/Y') ?? 'Non renseignée' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut</div>
                <div class="info-value">
                    <span class="badge {{ $athlete->actif ? 'badge-success' : 'badge-danger' }}">
                        {{ $athlete->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Statistiques</div>
        <div class="stats-box">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ $athlete->taux_presence }}%</div>
                    <div class="stat-label">Taux de présence</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $athlete->presences->count() }}</div>
                    <div class="stat-label">Séances</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $athlete->performances->count() }}</div>
                    <div class="stat-label">Évaluations</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($athlete->arrieres, 0, ',', ' ') }}</div>
                    <div class="stat-label">Arriérés (FCFA)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Disciplines</div>
        <table>
            <thead>
                <tr>
                    <th>Discipline</th>
                    <th>Tarif mensuel</th>
                    <th>Date inscription</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($athlete->disciplines as $discipline)
                    <tr>
                        <td>{{ $discipline->nom }}</td>
                        <td>{{ number_format($discipline->tarif_mensuel, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $discipline->pivot->date_inscription ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $discipline->pivot->actif ? 'badge-success' : 'badge-danger' }}">
                                {{ $discipline->pivot->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">Aucune discipline</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($athlete->licences->count() > 0)
    <div class="section">
        <div class="section-title">Licences</div>
        <table>
            <thead>
                <tr>
                    <th>N° Licence</th>
                    <th>Discipline</th>
                    <th>Catégorie</th>
                    <th>Expiration</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($athlete->licences->take(5) as $licence)
                    <tr>
                        <td>{{ $licence->numero_licence }}</td>
                        <td>{{ $licence->discipline->nom }}</td>
                        <td>{{ $licence->categorie ?? '-' }}</td>
                        <td>{{ $licence->date_expiration->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $licence->statut === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($licence->statut) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($athlete->certificatsMedicaux->count() > 0)
    <div class="section">
        <div class="section-title">Certificats Médicaux</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Médecin</th>
                    <th>Date examen</th>
                    <th>Expiration</th>
                    <th>Aptitude</th>
                </tr>
            </thead>
            <tbody>
                @foreach($athlete->certificatsMedicaux->take(3) as $certificat)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $certificat->type)) }}</td>
                        <td>{{ $certificat->medecin }}</td>
                        <td>{{ $certificat->date_examen->format('d/m/Y') }}</td>
                        <td>{{ $certificat->date_expiration->format('d/m/Y') }}</td>
                        <td>
                            @if($certificat->apte_competition && $certificat->apte_entrainement)
                                <span class="badge badge-success">Apte</span>
                            @else
                                <span class="badge badge-warning">Restrictions</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Fiche générée le {{ now()->format('d/m/Y à H:i') }} | OBD - Centre Sportif
    </div>
</body>
</html>

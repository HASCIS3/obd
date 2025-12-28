<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des participants - {{ $stageFormation->code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #14532d;
            padding-bottom: 20px;
        }
        .republic {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .ministry {
            font-size: 10px;
            color: #666;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #14532d;
            margin: 20px 0 10px;
        }
        .subtitle {
            font-size: 14px;
            color: #333;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #14532d;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-inscrit { background: #fef3c7; color: #92400e; }
        .status-confirme { background: #dbeafe; color: #1e40af; }
        .status-en_formation { background: #d1fae5; color: #065f46; }
        .status-diplome { background: #dcfce7; color: #14532d; }
        .status-echec { background: #fee2e2; color: #991b1b; }
        .status-abandon { background: #f3f4f6; color: #374151; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .stats {
            margin-top: 20px;
            display: flex;
            gap: 20px;
        }
        .stat-box {
            background: #f0f0f0;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #14532d;
        }
        .stat-label {
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="republic">RÉPUBLIQUE DU MALI</div>
        <div class="ministry">Ministère de la Jeunesse et des Sports</div>
        <div class="ministry">{{ $stageFormation->organisme }}</div>
        <div class="title">LISTE DES PARTICIPANTS</div>
        <div class="subtitle">{{ $stageFormation->titre }}</div>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Code du stage :</span>
            <span>{{ $stageFormation->code }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Type de formation :</span>
            <span>{{ $stageFormation->type_libelle }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Période :</span>
            <span>Du {{ $stageFormation->date_debut->format('d/m/Y') }} au {{ $stageFormation->date_fin->format('d/m/Y') }} ({{ $stageFormation->duree_jours }} jours)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Lieu :</span>
            <span>{{ $stageFormation->lieu }}</span>
        </div>
        @if($stageFormation->discipline)
            <div class="info-row">
                <span class="info-label">Discipline :</span>
                <span>{{ $stageFormation->discipline->nom }}</span>
            </div>
        @endif
        <div class="info-row">
            <span class="info-label">Certification :</span>
            <span>{{ $stageFormation->type_certification_libelle }} - {{ $stageFormation->intitule_certification ?? 'Non défini' }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">N°</th>
                <th>Nom et Prénom</th>
                <th>Sexe</th>
                <th>Date/Lieu Naissance</th>
                <th>Téléphone</th>
                <th>Structure</th>
                <th>Fonction</th>
                <th>Statut</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stageFormation->inscriptions as $index => $inscription)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $inscription->nom }}</strong> {{ $inscription->prenom }}</td>
                    <td>{{ $inscription->sexe }}</td>
                    <td>
                        {{ $inscription->date_naissance?->format('d/m/Y') ?? '-' }}
                        @if($inscription->lieu_naissance)
                            <br><small>{{ $inscription->lieu_naissance }}</small>
                        @endif
                    </td>
                    <td>{{ $inscription->telephone ?? '-' }}</td>
                    <td>{{ $inscription->structure ?? '-' }}</td>
                    <td>{{ $inscription->fonction ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ $inscription->statut }}">
                            {{ $inscription->statut_libelle }}
                        </span>
                    </td>
                    <td>{{ $inscription->note_finale ? number_format($inscription->note_finale, 1) . '/20' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">Aucun participant inscrit</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{{ $stageFormation->inscriptions->count() }}</div>
            <div class="stat-label">Total inscrits</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stageFormation->inscriptions->where('sexe', 'M')->count() }}</div>
            <div class="stat-label">Hommes</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stageFormation->inscriptions->where('sexe', 'F')->count() }}</div>
            <div class="stat-label">Femmes</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stageFormation->inscriptions->where('statut', 'diplome')->count() }}</div>
            <div class="stat-label">Diplômés</div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>{{ $stageFormation->organisme }}</p>
    </div>
</body>
</html>

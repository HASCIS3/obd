<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport - {{ $athlete->nom_complet }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { background: #16a34a; color: white; padding: 20px; text-align: center; }
        .header h1 { font-size: 20px; margin-bottom: 5px; }
        .header p { font-size: 11px; opacity: 0.9; }
        .content { padding: 20px; }
        .athlete-info { display: flex; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e5e7eb; }
        .athlete-name { font-size: 18px; font-weight: bold; color: #111; }
        .athlete-details { font-size: 11px; color: #666; margin-top: 5px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #16a34a; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb; }
        .stats-grid { display: table; width: 100%; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 10px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .stat-value { font-size: 20px; font-weight: bold; color: #16a34a; }
        .stat-label { font-size: 10px; color: #666; margin-top: 3px; }
        .observation { padding: 10px; margin-bottom: 8px; background: #f0fdf4; border-left: 3px solid #16a34a; }
        .observation-title { font-weight: bold; color: #111; }
        .observation-message { font-size: 11px; color: #666; margin-top: 3px; }
        .recommendation { padding: 8px 12px; margin-bottom: 5px; background: #fef3c7; border-radius: 4px; font-size: 11px; }
        .calendar { margin-top: 10px; }
        .calendar-day { display: inline-block; width: 20px; height: 20px; line-height: 20px; text-align: center; margin: 2px; font-size: 9px; border-radius: 3px; }
        .present { background: #22c55e; color: white; }
        .absent { background: #ef4444; color: white; }
        .footer { background: #f9fafb; padding: 15px; text-align: center; font-size: 10px; color: #666; margin-top: 20px; }
        .equilibre-bar { height: 10px; background: #e5e7eb; border-radius: 5px; margin: 10px 0; }
        .equilibre-fill { height: 100%; border-radius: 5px; }
        .equilibre-fill.success { background: #22c55e; }
        .equilibre-fill.primary { background: #3b82f6; }
        .equilibre-fill.warning { background: #eab308; }
        .equilibre-fill.danger { background: #ef4444; }
        .message-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .message-box p { font-size: 11px; color: #1e40af; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>Olympiade Baco-Djicoroni</h1>
        <p>Centre Sportif d'Excellence - Rapport mensuel</p>
    </div>

    <div class="content">
        <!-- Message personnalisé -->
        <div class="message-box">
            <p><strong>Cher(e) {{ $athlete->nom_tuteur ?: 'Parent' }},</strong></p>
            <p>Voici le rapport mensuel de suivi de votre enfant {{ $athlete->prenom }} au sein de notre centre sportif.</p>
        </div>

        <!-- Infos athlète -->
        <div class="athlete-info">
            <div>
                <div class="athlete-name">{{ $athlete->nom_complet }}</div>
                <div class="athlete-details">
                    {{ $athlete->age }} ans • {{ $athlete->categorie_age }} • 
                    {{ $athlete->disciplinesActives->pluck('nom')->join(', ') ?: 'Aucune discipline' }}
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="section">
            <div class="section-title">📊 Résumé du mois - {{ now()->format('F Y') }}</div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value" style="color: {{ $analyse['taux_presence'] >= 80 ? '#22c55e' : ($analyse['taux_presence'] >= 60 ? '#eab308' : '#ef4444') }}">
                        {{ number_format($analyse['taux_presence'], 0) }}%
                    </div>
                    <div class="stat-label">Assiduité</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $analyse['nb_seances'] }}</div>
                    <div class="stat-label">Séances</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: {{ $analyse['moyenne'] >= 10 ? '#22c55e' : '#ef4444' }}">
                        {{ $analyse['moyenne'] ? number_format($analyse['moyenne'], 1) : 'N/A' }}
                    </div>
                    <div class="stat-label">Moyenne /20</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">
                        @if($analyse['tendance'] === 'hausse') ↗
                        @elseif($analyse['tendance'] === 'baisse') ↘
                        @else →
                        @endif
                    </div>
                    <div class="stat-label">Tendance</div>
                </div>
            </div>
        </div>

        <!-- Équilibre -->
        <div class="section">
            <div class="section-title">⚖️ Équilibre Sport / Études</div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>Niveau d'équilibre</span>
                <span style="font-weight: bold;">{{ $analyse['equilibre'] }}</span>
            </div>
            <div class="equilibre-bar">
                <div class="equilibre-fill {{ $analyse['equilibre_color'] }}" style="width: {{ $analyse['equilibre_score'] }}%"></div>
            </div>
        </div>

        <!-- Observations -->
        <div class="section">
            <div class="section-title">👨‍🏫 Observations</div>
            @foreach($observations as $obs)
            <div class="observation">
                <div class="observation-title">{{ $obs['icon'] }} {{ $obs['titre'] }}</div>
                <div class="observation-message">{{ $obs['message'] }}</div>
            </div>
            @endforeach
        </div>

        <!-- Calendrier des présences -->
        <div class="section">
            <div class="section-title">📅 Calendrier des présences</div>
            <div class="calendar">
                @foreach($presences as $presence)
                <span class="calendar-day {{ $presence->present ? 'present' : 'absent' }}">
                    {{ $presence->date->format('d') }}
                </span>
                @endforeach
            </div>
            <div style="margin-top: 10px; font-size: 10px; color: #666;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #22c55e; border-radius: 2px; vertical-align: middle;"></span> Présent ({{ $presences->where('present', true)->count() }})
                &nbsp;&nbsp;
                <span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 2px; vertical-align: middle;"></span> Absent ({{ $presences->where('present', false)->count() }})
            </div>
        </div>

        <!-- Recommandations -->
        <div class="section">
            <div class="section-title">💡 Recommandations</div>
            @foreach($recommandations as $reco)
            <div class="recommendation">✓ {{ $reco }}</div>
            @endforeach
        </div>

        <!-- Contact -->
        <div class="section" style="background: #f0fdf4; padding: 12px; border-radius: 6px;">
            <strong>📞 Contact</strong>
            <p style="font-size: 11px; margin-top: 5px;">
                Pour toute question, n'hésitez pas à nous contacter au centre sportif.
            </p>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p><strong>Olympiade Baco-Djicoroni</strong> - Centre Sportif d'Excellence</p>
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>

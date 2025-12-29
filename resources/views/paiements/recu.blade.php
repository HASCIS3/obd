<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de Paiement #{{ $paiement->id }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            position: relative;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            position: relative;
        }
        
        /* Filigrane logo au centre - GRAND et VISIBLE */
        .watermark-container {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            text-align: center;
        }
        
        .watermark-logo {
            width: 280px;
            height: 280px;
            opacity: 0.12;
        }
        
        /* En-tête avec logo CENTRÉ */
        .header {
            text-align: center;
            padding: 15px;
            border-bottom: 3px solid #14532d;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .header-logo {
            margin-bottom: 8px;
        }
        
        .header-logo img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #14532d;
        }
        
        .header h1 {
            font-size: 20px;
            color: #14532d;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 11px;
            color: #6b7280;
        }
        
        .header .receipt-num {
            position: absolute;
            top: 10px;
            right: 0;
            background: #14532d;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Bande tricolore Mali */
        .mali-stripe {
            height: 6px;
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .mali-stripe div {
            display: table-cell;
            width: 33.33%;
        }
        
        .mali-stripe .green { background: #14532d; }
        .mali-stripe .yellow { background: #fbbf24; }
        .mali-stripe .red { background: #dc2626; }
        
        /* Corps du reçu */
        .receipt-body {
            position: relative;
            z-index: 1;
            padding: 0;
        }
        
        /* Titre */
        .receipt-title {
            text-align: center;
            color: #14532d;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 15px 0;
            padding: 8px;
            background: #f0fdf4;
            border: 1px solid #16a34a;
            border-radius: 5px;
        }
        
        /* Section titre */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #14532d;
            border-bottom: 2px solid #14532d;
            padding-bottom: 4px;
            margin-bottom: 10px;
            margin-top: 12px;
        }
        
        /* Tableau d'informations compact */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 11px;
        }
        
        .info-table .label {
            color: #6b7280;
            width: 35%;
        }
        
        .info-table .value {
            font-weight: 600;
            color: #111;
        }
        
        /* Deux colonnes */
        .two-columns {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .column:last-child {
            padding-right: 0;
            padding-left: 15px;
            border-left: 1px solid #e5e7eb;
        }
        
        /* Montant principal */
        .amount-box {
            background: #f0fdf4;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }
        
        .amount-box .label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        
        .amount-box .amount {
            font-size: 28px;
            font-weight: bold;
            color: #14532d;
        }
        
        .amount-box .currency {
            font-size: 14px;
            color: #16a34a;
        }
        
        /* Statut */
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .status-paye { background: #dcfce7; color: #14532d; }
        .status-partiel { background: #fef3c7; color: #92400e; }
        .status-impaye { background: #fee2e2; color: #991b1b; }
        
        /* Récapitulatif compact */
        .recap-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }
        
        .recap-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .recap-table .label-cell {
            background: #f9fafb;
            font-weight: 600;
            width: 55%;
        }
        
        .recap-table .total-row td {
            font-weight: bold;
            background: #f3f4f6;
        }
        
        /* Remarque */
        .remarque-box {
            background: #fefce8;
            border: 1px solid #fbbf24;
            border-radius: 5px;
            padding: 8px 12px;
            margin: 12px 0;
            font-size: 11px;
        }
        
        .remarque-box strong {
            color: #92400e;
        }
        
        /* Signatures */
        .signature-section {
            margin-top: 25px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 8px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Pied de page */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
            text-align: center;
        }
        
        .footer p {
            color: #6b7280;
            font-size: 10px;
            margin-bottom: 3px;
        }
        
        .footer .center-name {
            color: #14532d;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <!-- Filigrane logo au centre - GRAND et VISIBLE -->
    <div class="watermark-container">
        <img src="{{ public_path('images/logo_obd.jpg') }}" class="watermark-logo" alt="">
    </div>

    <div class="container">
        <!-- En-tête avec logo CENTRÉ -->
        <div class="header">
            <span class="receipt-num">N° {{ str_pad($paiement->id, 5, '0', STR_PAD_LEFT) }}</span>
            <div class="header-logo">
                <img src="{{ public_path('images/logo_obd.jpg') }}" alt="Logo OBD">
            </div>
            <h1>Centre Sportif OBD</h1>
            <p>Excellence Sportive - Formation - Discipline</p>
        </div>
        
        <!-- Bande tricolore Mali -->
        <div class="mali-stripe">
            <div class="green"></div>
            <div class="yellow"></div>
            <div class="red"></div>
        </div>
        
        <!-- Corps du reçu -->
        <div class="receipt-body">
            <!-- Titre -->
            <div class="receipt-title">REÇU DE PAIEMENT</div>
            
            <!-- Deux colonnes: Athlète et Paiement -->
            <div class="two-columns">
                <div class="column">
                    <div class="section-title">Informations Athlète</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nom :</td>
                            <td class="value">{{ $paiement->athlete->nom_complet }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tél :</td>
                            <td class="value">{{ $paiement->athlete->telephone ?: '-' }}</td>
                        </tr>
                        @if($paiement->athlete->disciplines->count() > 0)
                        <tr>
                            <td class="label">Discipline(s) :</td>
                            <td class="value">{{ $paiement->athlete->disciplines->pluck('nom')->join(', ') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="column">
                    <div class="section-title">Détails Paiement</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Type :</td>
                            <td class="value">
                                @if($paiement->type_paiement === 'cotisation') Cotisation
                                @elseif($paiement->type_paiement === 'inscription') Inscription
                                @else Équipement @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Période :</td>
                            <td class="value">{{ $paiement->periode }}</td>
                        </tr>
                        <tr>
                            <td class="label">Mode :</td>
                            <td class="value">{{ \App\Models\Paiement::modesPaiement()[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</td>
                        </tr>
                        <tr>
                            <td class="label">Référence :</td>
                            <td class="value">{{ $paiement->reference ?: 'REF-' . str_pad($paiement->id, 4, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Date :</td>
                            <td class="value">{{ $paiement->date_paiement?->format('d/m/Y') ?? now()->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Statut :</td>
                            <td class="value">
                                <span class="status-badge status-{{ $paiement->statut }}">
                                    @if($paiement->statut === 'paye') PAYÉ
                                    @elseif($paiement->statut === 'partiel') PARTIEL
                                    @else IMPAYÉ @endif
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Montant principal -->
            <div class="amount-box">
                <div class="label">Montant payé</div>
                <div class="amount">
                    {{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                    <span class="currency">FCFA</span>
                </div>
            </div>
            
            <!-- Récapitulatif des montants -->
            @php
                // Calcul dynamique du total à payer basé sur les frais détaillés
                $totalFraisDetails = 0;
                if ($paiement->frais_inscription) {
                    $totalFraisDetails += $paiement->frais_inscription;
                }
                if ($paiement->frais_equipement) {
                    $totalFraisDetails += $paiement->frais_equipement;
                }
                // Si aucun frais détaillé, utiliser le montant du paiement (cotisation)
                $totalAPayer = $totalFraisDetails > 0 ? $totalFraisDetails : $paiement->montant;
                
                // Calcul du reste à payer et du montant remboursé
                $resteAPayer = max(0, $totalAPayer - $paiement->montant_paye);
                $montantRembourse = max(0, $paiement->montant_paye - $totalAPayer);
            @endphp
            <table class="recap-table">
                @if($paiement->frais_inscription)
                <tr>
                    <td class="label-cell">Frais d'inscription</td>
                    <td>{{ number_format($paiement->frais_inscription, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                @if($paiement->type_equipement && $paiement->frais_equipement)
                <tr>
                    <td class="label-cell">
                        @switch($paiement->type_equipement)
                            @case('maillot') Maillot (Basket/Volley) @break
                            @case('dobok_enfant') Dobok Enfant @break
                            @case('dobok_junior') Dobok Junior @break
                            @case('dobok_senior') Dobok Senior @break
                            @default Équipement @endswitch
                    </td>
                    <td>{{ number_format($paiement->frais_equipement, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label-cell">Total à payer</td>
                    <td>{{ number_format($totalAPayer, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label-cell">Montant payé</td>
                    <td style="color: #16a34a; font-weight: bold;">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label-cell">Reste à payer</td>
                    <td style="color: {{ $resteAPayer > 0 ? '#dc2626' : '#16a34a' }}; font-weight: bold;">{{ number_format($resteAPayer, 0, ',', ' ') }} FCFA</td>
                </tr>
                @if($montantRembourse > 0)
                <tr>
                    <td class="label-cell">Montant remboursé</td>
                    <td style="color: #2563eb; font-weight: bold;">{{ number_format($montantRembourse, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
            </table>
            
            @if($paiement->remarque)
            <div class="remarque-box">
                <strong>Remarque :</strong> {{ $paiement->remarque }}
            </div>
            @endif
            
            <!-- Signatures -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">Le Responsable</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Cachet du Centre</div>
                </div>
            </div>
            
            <!-- Pied de page -->
            <div class="footer">
                <p class="center-name">Centre Sportif OBD</p>
                <p>Merci de votre confiance. Ce reçu fait foi de paiement.</p>
                <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

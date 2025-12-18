<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de Paiement #{{ $paiement->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* En-tête avec couleurs du Mali */
        .header {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #14532d 0%, #16a34a 100%);
            color: white;
            border-radius: 8px 8px 0 0;
            margin-bottom: 0;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Bande tricolore Mali */
        .mali-stripe {
            height: 8px;
            display: flex;
        }
        
        .mali-stripe .green {
            flex: 1;
            background: #14532d;
        }
        
        .mali-stripe .yellow {
            flex: 1;
            background: #fbbf24;
        }
        
        .mali-stripe .red {
            flex: 1;
            background: #dc2626;
        }
        
        /* Corps du reçu */
        .receipt-body {
            border: 2px solid #e5e7eb;
            border-top: none;
            padding: 25px;
            border-radius: 0 0 8px 8px;
        }
        
        /* Numéro de reçu */
        .receipt-number {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .receipt-number span {
            background: #14532d;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        /* Section titre */
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #14532d;
            border-bottom: 2px solid #14532d;
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 20px;
        }
        
        /* Tableau d'informations */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        
        .info-table .label {
            color: #6b7280;
            width: 40%;
        }
        
        .info-table .value {
            font-weight: 600;
            color: #111;
        }
        
        /* Montants */
        .amount-box {
            background: #f0fdf4;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .amount-box .label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .amount-box .amount {
            font-size: 28px;
            font-weight: bold;
            color: #14532d;
        }
        
        .amount-box .currency {
            font-size: 16px;
            color: #16a34a;
        }
        
        /* Statut */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .status-paye {
            background: #dcfce7;
            color: #14532d;
        }
        
        .status-partiel {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-impaye {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Détails supplémentaires */
        .details-grid {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        
        .details-row {
            display: table-row;
        }
        
        .details-cell {
            display: table-cell;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .details-cell.label {
            background: #f9fafb;
            font-weight: 600;
            width: 50%;
        }
        
        /* Pied de page */
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #e5e7eb;
            text-align: center;
        }
        
        .footer p {
            color: #6b7280;
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .footer .date {
            font-size: 10px;
            color: #9ca3af;
        }
        
        /* Signature */
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
            color: #6b7280;
        }
        
        /* Watermark pour les paiements non complets */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(220, 38, 38, 0.1);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    @if($paiement->statut !== 'paye')
    <div class="watermark">
        @if($paiement->statut === 'partiel')
            PARTIEL
        @else
            IMPAYÉ
        @endif
    </div>
    @endif

    <div class="container">
        <!-- En-tête -->
        <div class="header">
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
            <!-- Numéro de reçu -->
            <div class="receipt-number">
                <span>REÇU N° {{ str_pad($paiement->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            
            <!-- Titre -->
            <h2 style="text-align: center; color: #14532d; margin-bottom: 20px;">
                REÇU DE PAIEMENT
            </h2>
            
            <!-- Informations de l'athlète -->
            <div class="section-title">Informations de l'Athlète</div>
            <table class="info-table">
                <tr>
                    <td class="label">Nom complet :</td>
                    <td class="value">{{ $paiement->athlete->nom_complet }}</td>
                </tr>
                <tr>
                    <td class="label">Téléphone :</td>
                    <td class="value">{{ $paiement->athlete->telephone ?: 'Non renseigné' }}</td>
                </tr>
                @if($paiement->athlete->disciplines->count() > 0)
                <tr>
                    <td class="label">Discipline(s) :</td>
                    <td class="value">{{ $paiement->athlete->disciplines->pluck('nom')->join(', ') }}</td>
                </tr>
                @endif
            </table>
            
            <!-- Détails du paiement -->
            <div class="section-title">Détails du Paiement</div>
            <table class="info-table">
                <tr>
                    <td class="label">Type de paiement :</td>
                    <td class="value">
                        @if($paiement->type_paiement === 'cotisation')
                            Cotisation mensuelle
                        @elseif($paiement->type_paiement === 'inscription')
                            Frais d'inscription
                        @else
                            Équipement
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Période :</td>
                    <td class="value">{{ $paiement->periode }}</td>
                </tr>
                <tr>
                    <td class="label">Mode de paiement :</td>
                    <td class="value">{{ \App\Models\Paiement::modesPaiement()[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</td>
                </tr>
                @if($paiement->reference)
                <tr>
                    <td class="label">Référence :</td>
                    <td class="value">{{ $paiement->reference }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Date de paiement :</td>
                    <td class="value">{{ $paiement->date_paiement?->format('d/m/Y') ?? now()->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Statut :</td>
                    <td class="value">
                        <span class="status-badge status-{{ $paiement->statut }}">
                            @if($paiement->statut === 'paye')
                                PAYÉ
                            @elseif($paiement->statut === 'partiel')
                                PARTIEL
                            @else
                                IMPAYÉ
                            @endif
                        </span>
                    </td>
                </tr>
            </table>
            
            <!-- Montant -->
            <div class="amount-box">
                <div class="label">Montant payé</div>
                <div class="amount">
                    {{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                    <span class="currency">FCFA</span>
                </div>
            </div>
            
            <!-- Récapitulatif des montants -->
            <div class="details-grid">
                @if($paiement->type_paiement === 'inscription' && $paiement->frais_inscription)
                <div class="details-row">
                    <div class="details-cell label">Frais d'inscription</div>
                    <div class="details-cell">{{ number_format($paiement->frais_inscription, 0, ',', ' ') }} FCFA</div>
                </div>
                @endif
                @if($paiement->type_equipement && $paiement->frais_equipement)
                <div class="details-row">
                    <div class="details-cell label">
                        @switch($paiement->type_equipement)
                            @case('maillot')
                                Maillot (Basket/Volley) - 4 000 FCFA
                                @break
                            @case('dobok_enfant')
                                Dobok Enfant (Taekwondo)
                                @break
                            @case('dobok_junior')
                                Dobok Junior (Taekwondo)
                                @break
                            @case('dobok_senior')
                                Dobok Senior (Taekwondo)
                                @break
                            @default
                                Dobok (Taekwondo)
                        @endswitch
                    </div>
                    <div class="details-cell">{{ number_format($paiement->frais_equipement, 0, ',', ' ') }} FCFA</div>
                </div>
                @endif
                @if($paiement->type_paiement === 'cotisation')
                <div class="details-row">
                    <div class="details-cell label">Cotisation mensuelle</div>
                    <div class="details-cell">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</div>
                </div>
                @endif
                <div class="details-row">
                    <div class="details-cell label" style="font-weight: bold;">Total à payer</div>
                    <div class="details-cell" style="font-weight: bold;">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="details-row">
                    <div class="details-cell label">Montant payé</div>
                    <div class="details-cell" style="color: #16a34a; font-weight: bold;">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</div>
                </div>
                @if($paiement->reste_a_payer > 0)
                <div class="details-row">
                    <div class="details-cell label">Reste à payer</div>
                    <div class="details-cell" style="color: #dc2626; font-weight: bold;">{{ number_format($paiement->reste_a_payer, 0, ',', ' ') }} FCFA</div>
                </div>
                @endif
            </div>
            
            @if($paiement->remarque)
            <div class="section-title">Remarque</div>
            <p style="color: #6b7280; font-style: italic;">{{ $paiement->remarque }}</p>
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
                <p><strong>Centre Sportif OBD</strong></p>
                <p>Merci de votre confiance. Ce reçu fait foi de paiement.</p>
                <p class="date">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

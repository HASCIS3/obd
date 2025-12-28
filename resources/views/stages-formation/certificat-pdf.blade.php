<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $inscription->stageFormation->type_certification_libelle }} - {{ $inscription->numero_certificat }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            box-sizing: border-box;
        }
        .certificate {
            background: white;
            border: 3px solid #14532d;
            padding: 40px 60px;
            position: relative;
            min-height: calc(100vh - 80px);
            box-sizing: border-box;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #FCD116;
            pointer-events: none;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .republic {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }
        .ministry {
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        .logo-container {
            margin: 20px 0;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background: #14532d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .certificate-type {
            font-size: 32px;
            font-weight: bold;
            color: #14532d;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 20px 0;
        }
        .certificate-title {
            font-size: 18px;
            color: #CE1126;
            margin-bottom: 30px;
        }
        .content {
            text-align: center;
            margin: 30px 0;
        }
        .intro-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        .recipient-name {
            font-size: 28px;
            font-weight: bold;
            color: #14532d;
            margin: 20px 0;
            padding: 10px 0;
            border-bottom: 2px solid #FCD116;
            display: inline-block;
        }
        .birth-info {
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        .formation-text {
            font-size: 14px;
            color: #333;
            margin: 20px 0;
            line-height: 1.8;
        }
        .formation-title {
            font-size: 16px;
            font-weight: bold;
            color: #14532d;
            margin: 15px 0;
        }
        .details {
            margin: 30px auto;
            max-width: 500px;
            text-align: left;
            font-size: 12px;
            color: #666;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ddd;
        }
        .details-label {
            font-weight: bold;
        }
        .note-section {
            margin: 20px 0;
            text-align: center;
        }
        .note {
            font-size: 24px;
            font-weight: bold;
            color: #14532d;
        }
        .note-label {
            font-size: 12px;
            color: #666;
        }
        .appreciation {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-left {
            text-align: left;
            font-size: 11px;
            color: #666;
        }
        .footer-right {
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 12px;
            color: #333;
        }
        .date-place {
            font-size: 12px;
            color: #333;
            margin-bottom: 10px;
        }
        .certificate-number {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            color: #999;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(20, 83, 45, 0.05);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="watermark">INJS</div>
        
        <div class="header">
            <div class="republic">RÉPUBLIQUE DU MALI</div>
            <div class="ministry">Ministère de la Jeunesse et des Sports</div>
            <div class="ministry">{{ $inscription->stageFormation->organisme }}</div>
            
            <div class="certificate-type">
                {{ $inscription->stageFormation->type_certification_libelle }}
            </div>
            
            @if($inscription->stageFormation->intitule_certification)
                <div class="certificate-title">
                    {{ $inscription->stageFormation->intitule_certification }}
                </div>
            @endif
        </div>

        <div class="content">
            <div class="intro-text">
                Le Directeur de l'Institut National de la Jeunesse et des Sports certifie que
            </div>
            
            <div class="recipient-name">
                {{ $inscription->prenom }} {{ $inscription->nom }}
            </div>
            
            @if($inscription->date_naissance || $inscription->lieu_naissance)
                <div class="birth-info">
                    @if($inscription->date_naissance)
                        Né(e) le {{ $inscription->date_naissance->format('d/m/Y') }}
                    @endif
                    @if($inscription->lieu_naissance)
                        à {{ $inscription->lieu_naissance }}
                    @endif
                </div>
            @endif

            <div class="formation-text">
                a suivi avec succès le stage de formation
            </div>
            
            <div class="formation-title">
                « {{ $inscription->stageFormation->titre }} »
            </div>

            <div class="details">
                <div class="details-row">
                    <span class="details-label">Type de formation :</span>
                    <span>{{ $inscription->stageFormation->type_libelle }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Période :</span>
                    <span>Du {{ $inscription->stageFormation->date_debut->format('d/m/Y') }} au {{ $inscription->stageFormation->date_fin->format('d/m/Y') }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Durée :</span>
                    <span>{{ $inscription->stageFormation->duree_jours }} jours ({{ $inscription->stageFormation->duree_heures ?? '-' }} heures)</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Lieu :</span>
                    <span>{{ $inscription->stageFormation->lieu }}</span>
                </div>
                @if($inscription->stageFormation->discipline)
                    <div class="details-row">
                        <span class="details-label">Discipline :</span>
                        <span>{{ $inscription->stageFormation->discipline->nom }}</span>
                    </div>
                @endif
            </div>

            @if($inscription->note_finale)
                <div class="note-section">
                    <div class="note-label">Note obtenue</div>
                    <div class="note">{{ number_format($inscription->note_finale, 2) }} / 20</div>
                    @if($inscription->appreciation)
                        <div class="appreciation">« {{ $inscription->appreciation }} »</div>
                    @endif
                </div>
            @endif
        </div>

        <div class="footer">
            <div class="footer-left">
                <strong>N° {{ $inscription->numero_certificat }}</strong><br>
                Délivré le {{ $inscription->date_delivrance?->format('d/m/Y') ?? now()->format('d/m/Y') }}
            </div>
            <div class="footer-right">
                <div class="date-place">
                    Fait à Bamako, le {{ $inscription->date_delivrance?->format('d F Y') ?? now()->format('d F Y') }}
                </div>
                <div class="signature-line">
                    Le Directeur de l'INJS
                </div>
            </div>
        </div>

        <div class="certificate-number">
            Ce document est authentique - Référence : {{ $inscription->numero_certificat }}
        </div>
    </div>
</body>
</html>

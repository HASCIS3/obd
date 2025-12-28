<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diplôme OBD - {{ $inscription->numero_certificat }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .diploma-page {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: #fff;
            overflow: hidden;
        }
        
        /* ========== EN-TÊTE VERT ========== */
        .header-band {
            background: linear-gradient(135deg, #0d3320 0%, #14532d 30%, #166534 70%, #14532d 100%);
            height: 155px;
            width: 100%;
            position: relative;
        }
        .header-content {
            height: 100%;
            padding: 20px 25px;
        }
        .header-row {
            display: table;
            width: 100%;
            height: 100%;
        }
        .header-col-left {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            text-align: left;
            color: rgba(255,255,255,0.9);
            font-size: 10px;
            line-height: 1.5;
        }
        .header-col-center {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: center;
        }
        .header-col-right {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
            text-align: right;
            color: rgba(255,255,255,0.9);
            font-size: 10px;
            line-height: 1.5;
        }
        
        /* Logo centré */
        .logo-circle {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            background: #fff;
            margin: 0 auto 10px;
            padding: 5px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .logo-circle img {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center center;
        }
        .header-title {
            color: #D4AF37;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .header-tagline {
            color: rgba(255,255,255,0.85);
            font-size: 11px;
            margin-top: 5px;
            letter-spacing: 1px;
        }
        
        /* Ligne dorée sous l'en-tête */
        .gold-line {
            height: 4px;
            background: linear-gradient(90deg, transparent 0%, #D4AF37 20%, #F4D03F 50%, #D4AF37 80%, transparent 100%);
        }
        
        /* ========== FILIGRANE ========== */
        .watermark-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -45%);
            z-index: 0;
            pointer-events: none;
        }
        .watermark-logo {
            width: 320px;
            height: 320px;
            opacity: 0.07;
        }
        .watermark-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        /* ========== BORDURE DÉCORATIVE ========== */
        .border-outer {
            position: absolute;
            top: 170px;
            left: 12px;
            right: 12px;
            bottom: 50px;
            border: 3px solid #14532d;
        }
        .border-inner {
            position: absolute;
            top: 175px;
            left: 17px;
            right: 17px;
            bottom: 55px;
            border: 1px solid #D4AF37;
        }
        
        /* Coins dorés */
        .corner-decor {
            position: absolute;
            width: 25px;
            height: 25px;
            z-index: 5;
        }
        .corner-tl { top: 175px; left: 17px; border-top: 3px solid #D4AF37; border-left: 3px solid #D4AF37; }
        .corner-tr { top: 175px; right: 17px; border-top: 3px solid #D4AF37; border-right: 3px solid #D4AF37; }
        .corner-bl { bottom: 55px; left: 17px; border-bottom: 3px solid #D4AF37; border-left: 3px solid #D4AF37; }
        .corner-br { bottom: 55px; right: 17px; border-bottom: 3px solid #D4AF37; border-right: 3px solid #D4AF37; }
        
        /* ========== CONTENU PRINCIPAL ========== */
        .main-content {
            position: relative;
            z-index: 1;
            padding: 25px 45px 20px;
            text-align: center;
        }
        
        /* Type de diplôme */
        .diploma-type {
            font-size: 42px;
            font-weight: bold;
            color: #14532d;
            text-transform: uppercase;
            letter-spacing: 10px;
            margin-bottom: 5px;
        }
        .diploma-type-underline {
            width: 200px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            margin: 0 auto 10px;
        }
        .diploma-intitule {
            font-size: 14px;
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
        }
        
        /* Texte de certification */
        .certify-intro {
            font-size: 12px;
            color: #555;
            margin-bottom: 8px;
        }
        
        /* Nom du récipiendaire */
        .recipient-block {
            margin: 15px 0;
        }
        .recipient-name {
            font-size: 34px;
            font-weight: bold;
            color: #14532d;
            letter-spacing: 2px;
            padding-bottom: 8px;
            border-bottom: 3px double #D4AF37;
            display: inline-block;
        }
        .recipient-info {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
        }
        
        /* Texte de formation */
        .formation-intro {
            font-size: 12px;
            color: #444;
            margin: 18px 0 10px;
            line-height: 1.6;
        }
        .formation-name {
            font-size: 16px;
            font-weight: bold;
            color: #14532d;
            background: rgba(20, 83, 45, 0.06);
            padding: 10px 25px;
            border-radius: 5px;
            display: inline-block;
            border-left: 4px solid #D4AF37;
            border-right: 4px solid #D4AF37;
        }
        
        /* Détails de la formation */
        .details-section {
            margin: 20px auto;
            max-width: 420px;
        }
        .details-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .detail-item {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            color: #14532d;
            text-align: left;
            width: 35%;
            border-bottom: 1px dotted #ddd;
        }
        .detail-value {
            display: table-cell;
            padding: 6px 10px;
            font-size: 11px;
            color: #333;
            text-align: right;
            border-bottom: 1px dotted #ddd;
        }
        .detail-item:last-child .detail-label,
        .detail-item:last-child .detail-value {
            border-bottom: none;
        }
        
        /* Note et appréciation */
        .evaluation-section {
            margin: 18px 0;
        }
        .note-display {
            display: inline-block;
            background: linear-gradient(135deg, #14532d 0%, #166534 100%);
            color: #fff;
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(20, 83, 45, 0.3);
        }
        .appreciation-text {
            font-size: 12px;
            font-style: italic;
            color: #555;
            margin-top: 8px;
        }
        
        /* ========== SIGNATURES ========== */
        .signatures-section {
            margin-top: 30px;
            display: table;
            width: 100%;
            padding: 0 20px;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 25px;
            vertical-align: bottom;
        }
        .signature-space {
            height: 50px;
        }
        .signature-line-box {
            border-top: 1px solid #333;
            padding-top: 8px;
        }
        .signature-title {
            font-size: 11px;
            font-weight: bold;
            color: #14532d;
        }
        .signature-name {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        
        /* ========== PIED DE PAGE ========== */
        .footer-section {
            position: absolute;
            bottom: 15px;
            left: 20px;
            right: 20px;
            height: 35px;
            background: #fff;
        }
        .footer-left {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30%;
            text-align: left;
            font-size: 8px;
            color: #777;
            line-height: 1.3;
        }
        .footer-center {
            position: absolute;
            left: 30%;
            bottom: 0;
            width: 40%;
            text-align: center;
            font-size: 7px;
            color: #999;
            line-height: 1.3;
        }
        .footer-right {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 30%;
            text-align: right;
            font-size: 8px;
            color: #777;
            line-height: 1.3;
        }
        .cert-number {
            font-weight: bold;
            color: #14532d;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="diploma-page">
        
        <!-- Filigrane central -->
        <div class="watermark-container">
            <div class="watermark-logo">
                <img src="{{ public_path('images/logo_obd.jpg') }}" alt="Filigrane OBD">
            </div>
        </div>
        
        <!-- En-tête vert -->
        <div class="header-band">
            <div class="header-content">
                <div class="header-row">
                    <div class="header-col-left">
                        <strong>RÉPUBLIQUE DU MALI</strong><br>
                        Un Peuple - Un But - Une Foi<br><br>
                        <em>Ministère de la Jeunesse<br>et des Sports</em>
                    </div>
                    <div class="header-col-center">
                        <div class="logo-circle">
                            <img src="{{ public_path('images/logo_obd.jpg') }}" alt="Logo OBD">
                        </div>
                        <div class="header-title">OLYMPIADE DE BACO-DJICORONI</div>
                        <div class="header-tagline">Centre de Formation Sportive d'Excellence</div>
                    </div>
                    <div class="header-col-right">
                        <strong>{{ $inscription->stageFormation->organisme }}</strong><br><br>
                        <em>Bamako, Mali</em>
                    </div>
                </div>
            </div>
        </div>
        <div class="gold-line"></div>
        
        <!-- Bordures décoratives -->
        <div class="border-outer"></div>
        <div class="border-inner"></div>
        <div class="corner-decor corner-tl"></div>
        <div class="corner-decor corner-tr"></div>
        <div class="corner-decor corner-bl"></div>
        <div class="corner-decor corner-br"></div>
        
        <!-- Contenu principal -->
        <div class="main-content">
            
            <!-- Type de diplôme -->
            <div class="diploma-type">{{ $inscription->stageFormation->type_certification_libelle }}</div>
            <div class="diploma-type-underline"></div>
            
            @if($inscription->stageFormation->intitule_certification)
                <div class="diploma-intitule">{{ $inscription->stageFormation->intitule_certification }}</div>
            @endif
            
            <!-- Certification -->
            <div class="certify-intro">
                Le Président du Centre d'Olympiade de Baco-Djicoroni (heremakono) certifie que
            </div>
            
            <!-- Récipiendaire -->
            <div class="recipient-block">
                <div class="recipient-name">{{ strtoupper($inscription->prenom) }} {{ strtoupper($inscription->nom) }}</div>
                @if($inscription->date_naissance || $inscription->lieu_naissance)
                    <div class="recipient-info">
                        @if($inscription->date_naissance)
                            Né(e) le {{ $inscription->date_naissance->format('d/m/Y') }}
                        @endif
                        @if($inscription->lieu_naissance)
                            à {{ $inscription->lieu_naissance }}
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Formation suivie -->
            <div class="formation-intro">
                a suivi avec succès le stage de formation et a satisfait<br>
                aux épreuves d'évaluation de la formation intitulée :
            </div>
            
            <div class="formation-name">« {{ $inscription->stageFormation->titre }} »</div>
            
            <!-- Détails -->
            <div class="details-section">
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Type de formation</span>
                        <span class="detail-value">{{ $inscription->stageFormation->type_libelle }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Période</span>
                        <span class="detail-value">Du {{ $inscription->stageFormation->date_debut->format('d/m/Y') }} au {{ $inscription->stageFormation->date_fin->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Durée</span>
                        <span class="detail-value">{{ $inscription->stageFormation->duree_jours }} jours @if($inscription->stageFormation->duree_heures)({{ $inscription->stageFormation->duree_heures }} heures)@endif</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lieu</span>
                        <span class="detail-value">{{ $inscription->stageFormation->lieu }}</span>
                    </div>
                    @if($inscription->stageFormation->discipline)
                        <div class="detail-item">
                            <span class="detail-label">Discipline</span>
                            <span class="detail-value">{{ $inscription->stageFormation->discipline->nom }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Note et appréciation -->
            @if($inscription->note_finale)
                <div class="evaluation-section">
                    <div class="note-display">Note obtenue : {{ number_format($inscription->note_finale, 2) }} / 20</div>
                    @if($inscription->appreciation)
                        <div class="appreciation-text">« {{ $inscription->appreciation }} »</div>
                    @endif
                </div>
            @endif
            
            <!-- Signatures -->
            <div class="signatures-section">
                <div class="signature-col">
                    <div class="signature-space"></div>
                    <div class="signature-line-box">
                        <div class="signature-title">Le Formateur Principal</div>
                        <div class="signature-name">
                            @if($inscription->stageFormation->encadreurs && count($inscription->stageFormation->encadreurs) > 0)
                                {{ $inscription->stageFormation->encadreurs[0] }}
                            @else
                                ________________________
                            @endif
                        </div>
                    </div>
                </div>
                <div class="signature-col">
                    <div class="signature-space"></div>
                    <div class="signature-line-box">
                        <div class="signature-title">Le Président du Centre OBD</div>
                        <div class="signature-name">________________________</div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Pied de page -->
        <div class="footer-section">
            <div class="footer-left">
                <span class="cert-number">{{ $inscription->numero_certificat }}</span><br>
                Delivre le {{ $inscription->date_delivrance?->format('d/m/Y') ?? now()->format('d/m/Y') }}
            </div>
            <div class="footer-center">
                Document officiel - Olympiade de Baco-Djicoroni
            </div>
            <div class="footer-right">
                Bamako, {{ $inscription->date_delivrance?->format('d/m/Y') ?? now()->format('d/m/Y') }}
            </div>
        </div>
        
    </div>
</body>
</html>

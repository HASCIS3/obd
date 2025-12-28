<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        .header { border-bottom: 2px solid #14532d; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #14532d; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #666; }
        .facture-info { float: right; text-align: right; }
        .facture-numero { font-size: 18px; font-weight: bold; color: #14532d; }
        .client-info { margin-bottom: 30px; }
        .client-info h3 { margin: 0 0 10px 0; color: #14532d; font-size: 14px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th { background-color: #14532d; color: white; padding: 10px; text-align: left; }
        .details-table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .totaux { width: 300px; float: right; margin-top: 20px; }
        .totaux table { width: 100%; }
        .totaux td { padding: 8px; }
        .totaux .total-row { font-weight: bold; font-size: 14px; background: #f3f4f6; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 10px; }
        .statut { display: inline-block; padding: 5px 15px; border-radius: 15px; font-weight: bold; }
        .statut-payee { background: #dcfce7; color: #166534; }
        .statut-emise { background: #dbeafe; color: #1e40af; }
        .statut-partiel { background: #fef3c7; color: #92400e; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <div class="facture-info">
            <div class="facture-numero">{{ $facture->numero }}</div>
            <p>Date: {{ $facture->date_emission->format('d/m/Y') }}</p>
            <p>Échéance: {{ $facture->date_echeance->format('d/m/Y') }}</p>
        </div>
        <h1>FACTURE</h1>
        <p>OBD - Centre Sportif</p>
    </div>

    <div class="client-info">
        <h3>Facturé à:</h3>
        <p><strong>{{ $facture->athlete->nom_complet }}</strong></p>
        @if($facture->athlete->adresse)
            <p>{{ $facture->athlete->adresse }}</p>
        @endif
        @if($facture->athlete->telephone)
            <p>Tél: {{ $facture->athlete->telephone }}</p>
        @endif
        @if($facture->athlete->email)
            <p>Email: {{ $facture->athlete->email }}</p>
        @endif
    </div>

    @if($facture->periode)
        <p><strong>Période:</strong> {{ $facture->periode }}</p>
    @endif

    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 70%">Description</th>
                <th style="width: 30%; text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $facture->description ?? 'Cotisation sportive' }}
                </td>
                <td style="text-align: right;">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="totaux">
        <table>
            <tr>
                <td>Montant HT</td>
                <td style="text-align: right;">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($facture->tva > 0)
                <tr>
                    <td>TVA ({{ $facture->tva }}%)</td>
                    <td style="text-align: right;">{{ number_format($facture->montant_ttc - $facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total TTC</td>
                <td style="text-align: right;">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($facture->montant_paye > 0)
                <tr>
                    <td>Déjà payé</td>
                    <td style="text-align: right;">{{ number_format($facture->montant_paye, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr class="total-row">
                    <td>Reste à payer</td>
                    <td style="text-align: right;">{{ number_format($facture->reste_a_payer, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="clear"></div>

    <div style="margin-top: 40px;">
        <p><strong>Statut:</strong> 
            <span class="statut {{ $facture->statut === 'payee' ? 'statut-payee' : ($facture->statut === 'partiellement_payee' ? 'statut-partiel' : 'statut-emise') }}">
                {{ $facture->statut_label }}
            </span>
        </p>
    </div>

    <div class="footer">
        <p>OBD - Centre Sportif | Facture générée le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Merci de votre confiance</p>
    </div>
</body>
</html>

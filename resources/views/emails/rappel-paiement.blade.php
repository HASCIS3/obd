<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #14532d; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .alert { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; }
        .info-box { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .amount { font-size: 20px; font-weight: bold; color: #dc2626; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OBD - Centre Sportif</h1>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $paiement->athlete->prenom }},</h2>
            
            <div class="alert">
                <strong>💳 Rappel de paiement</strong>
            </div>
            
            <p>Nous vous rappelons que le paiement suivant est en attente :</p>
            
            <div class="info-box">
                <p><strong>Période:</strong> {{ $paiement->mois }}/{{ $paiement->annee }}</p>
                <p><strong>Discipline:</strong> {{ $paiement->discipline->nom ?? 'Toutes disciplines' }}</p>
                <p><strong>Montant dû:</strong> <span class="amount">{{ number_format($paiement->montant - $paiement->montant_paye, 0, ',', ' ') }} FCFA</span></p>
                @if($paiement->montant_paye > 0)
                    <p><strong>Déjà payé:</strong> {{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</p>
                @endif
            </div>
            
            <p>Merci de régulariser votre situation auprès du secrétariat.</p>
            
            <p>Cordialement,<br>L'équipe OBD</p>
        </div>
        
        <div class="footer">
            <p>OBD - Centre Sportif<br>
            Cet email a été envoyé automatiquement.</p>
        </div>
    </div>
</body>
</html>

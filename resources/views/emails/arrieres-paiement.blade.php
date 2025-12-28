<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #14532d; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .alert { background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; }
        .info-box { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #dc2626; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OBD - Centre Sportif</h1>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $athlete->prenom }},</h2>
            
            <div class="alert">
                <strong>📋 Rappel: Vous avez des arriérés de paiement</strong>
            </div>
            
            <p>Nous vous informons que votre compte présente un solde impayé.</p>
            
            <div class="info-box" style="text-align: center;">
                <p>Montant total des arriérés:</p>
                <p class="amount">{{ number_format($athlete->arrieres, 0, ',', ' ') }} FCFA</p>
            </div>
            
            <p>Nous vous prions de bien vouloir régulariser votre situation dans les meilleurs délais.</p>
            
            <p>Pour tout renseignement ou pour convenir d'un échéancier de paiement, n'hésitez pas à contacter le secrétariat du centre.</p>
            
            <p>Cordialement,<br>L'équipe OBD</p>
        </div>
        
        <div class="footer">
            <p>OBD - Centre Sportif<br>
            Cet email a été envoyé automatiquement.</p>
        </div>
    </div>
</body>
</html>

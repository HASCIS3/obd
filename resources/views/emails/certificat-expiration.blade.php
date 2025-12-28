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
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OBD - Centre Sportif</h1>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $certificat->athlete->prenom }},</h2>
            
            <div class="alert">
                <strong>⚠️ Votre certificat médical expire bientôt !</strong>
            </div>
            
            <p>Nous vous informons que votre certificat médical arrive à expiration.</p>
            
            <div class="info-box">
                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $certificat->type)) }}</p>
                <p><strong>Médecin:</strong> {{ $certificat->medecin }}</p>
                <p><strong>Date d'expiration:</strong> {{ $certificat->date_expiration->format('d/m/Y') }}</p>
                <p><strong>Jours restants:</strong> {{ $certificat->jours_restants }} jour(s)</p>
            </div>
            
            <p><strong>Important:</strong> Un certificat médical valide est obligatoire pour participer aux entraînements et compétitions.</p>
            
            <p>Veuillez consulter un médecin pour obtenir un nouveau certificat d'aptitude à la pratique sportive.</p>
            
            <p>Cordialement,<br>L'équipe OBD</p>
        </div>
        
        <div class="footer">
            <p>OBD - Centre Sportif<br>
            Cet email a été envoyé automatiquement.</p>
        </div>
    </div>
</body>
</html>

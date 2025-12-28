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
        .btn { display: inline-block; background: #14532d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OBD - Centre Sportif</h1>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $licence->athlete->prenom }},</h2>
            
            <div class="alert">
                <strong>⚠️ Votre licence sportive expire bientôt !</strong>
            </div>
            
            <p>Nous vous informons que votre licence sportive arrive à expiration.</p>
            
            <div class="info-box">
                <p><strong>Numéro de licence:</strong> {{ $licence->numero_licence }}</p>
                <p><strong>Discipline:</strong> {{ $licence->discipline->nom }}</p>
                <p><strong>Date d'expiration:</strong> {{ $licence->date_expiration->format('d/m/Y') }}</p>
                <p><strong>Jours restants:</strong> {{ $licence->jours_restants }} jour(s)</p>
            </div>
            
            <p>Pour continuer à participer aux compétitions officielles, veuillez renouveler votre licence avant la date d'expiration.</p>
            
            <p>Rendez-vous au secrétariat du centre sportif avec les documents suivants :</p>
            <ul>
                <li>Certificat médical d'aptitude valide</li>
                <li>Photo d'identité récente</li>
                <li>Frais de licence</li>
            </ul>
            
            <p>Cordialement,<br>L'équipe OBD</p>
        </div>
        
        <div class="footer">
            <p>OBD - Centre Sportif<br>
            Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>

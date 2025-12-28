<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\CertificatMedical;
use App\Models\Licence;
use App\Models\Paiement;
use Illuminate\Support\Facades\Mail;
use App\Mail\LicenceExpirationMail;
use App\Mail\CertificatExpirationMail;
use App\Mail\ArrieresPaiementMail;
use App\Mail\RappelPaiementMail;

class NotificationService
{
    /**
     * Envoyer les notifications de licences expirant bientôt
     */
    public function notifierLicencesExpirant(int $joursAvant = 30): int
    {
        $licences = Licence::with(['athlete', 'discipline'])
            ->expirantBientot($joursAvant)
            ->get();

        $count = 0;
        foreach ($licences as $licence) {
            $athlete = $licence->athlete;
            
            // Envoyer à l'athlète s'il a un email
            if ($athlete->email) {
                Mail::to($athlete->email)->queue(new LicenceExpirationMail($licence));
                $count++;
            }
            
            // Envoyer au tuteur si l'athlète est mineur
            if ($athlete->estMineur() && $athlete->telephone_tuteur) {
                // SMS via service externe (à implémenter)
            }
        }

        return $count;
    }

    /**
     * Envoyer les notifications de certificats médicaux expirant
     */
    public function notifierCertificatsExpirant(int $joursAvant = 30): int
    {
        $certificats = CertificatMedical::with('athlete')
            ->expirantBientot($joursAvant)
            ->get();

        $count = 0;
        foreach ($certificats as $certificat) {
            $athlete = $certificat->athlete;
            
            if ($athlete->email) {
                Mail::to($athlete->email)->queue(new CertificatExpirationMail($certificat));
                $count++;
            }
        }

        return $count;
    }

    /**
     * Envoyer les rappels de paiement pour les arriérés
     */
    public function notifierArrieres(float $seuilMinimum = 10000): int
    {
        $athletes = Athlete::actifs()
            ->avecArrieres()
            ->get()
            ->filter(fn($a) => $a->arrieres >= $seuilMinimum);

        $count = 0;
        foreach ($athletes as $athlete) {
            if ($athlete->email) {
                Mail::to($athlete->email)->queue(new ArrieresPaiementMail($athlete));
                $count++;
            }
        }

        return $count;
    }

    /**
     * Envoyer un rappel de paiement mensuel
     */
    public function envoyerRappelMensuel(int $mois, int $annee): int
    {
        $paiements = Paiement::with('athlete')
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->whereIn('statut', ['impaye', 'partiel'])
            ->get();

        $count = 0;
        foreach ($paiements as $paiement) {
            $athlete = $paiement->athlete;
            
            if ($athlete->email) {
                Mail::to($athlete->email)->queue(new RappelPaiementMail($paiement));
                $count++;
            }
        }

        return $count;
    }
}

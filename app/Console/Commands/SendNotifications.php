<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendNotifications extends Command
{
    protected $signature = 'notifications:send 
                            {type : Type de notification (licences, certificats, arrieres, rappel-mensuel)}
                            {--jours=30 : Nombre de jours avant expiration}
                            {--mois= : Mois pour rappel mensuel}
                            {--annee= : Année pour rappel mensuel}';

    protected $description = 'Envoie les notifications par email aux athlètes';

    public function handle(NotificationService $service): int
    {
        $type = $this->argument('type');
        $jours = (int) $this->option('jours');

        $count = match ($type) {
            'licences' => $this->envoyerLicences($service, $jours),
            'certificats' => $this->envoyerCertificats($service, $jours),
            'arrieres' => $this->envoyerArrieres($service),
            'rappel-mensuel' => $this->envoyerRappelMensuel($service),
            default => $this->erreurType(),
        };

        if ($count === -1) {
            return Command::FAILURE;
        }

        $this->info("✅ {$count} notification(s) envoyée(s).");
        return Command::SUCCESS;
    }

    private function envoyerLicences(NotificationService $service, int $jours): int
    {
        $this->info("Envoi des notifications de licences expirant dans {$jours} jours...");
        return $service->notifierLicencesExpirant($jours);
    }

    private function envoyerCertificats(NotificationService $service, int $jours): int
    {
        $this->info("Envoi des notifications de certificats expirant dans {$jours} jours...");
        return $service->notifierCertificatsExpirant($jours);
    }

    private function envoyerArrieres(NotificationService $service): int
    {
        $this->info("Envoi des notifications d'arriérés...");
        return $service->notifierArrieres();
    }

    private function envoyerRappelMensuel(NotificationService $service): int
    {
        $mois = $this->option('mois') ?? now()->month;
        $annee = $this->option('annee') ?? now()->year;

        $this->info("Envoi des rappels de paiement pour {$mois}/{$annee}...");
        return $service->envoyerRappelMensuel((int) $mois, (int) $annee);
    }

    private function erreurType(): int
    {
        $this->error("Type de notification invalide. Types disponibles: licences, certificats, arrieres, rappel-mensuel");
        return -1;
    }
}

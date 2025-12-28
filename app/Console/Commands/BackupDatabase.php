<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--path= : Chemin personnalisé pour le backup}';
    protected $description = 'Crée une sauvegarde de la base de données MySQL';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$database}_{$timestamp}.sql";
        
        $backupPath = $this->option('path') ?: storage_path('app/backups');
        
        // Créer le dossier si nécessaire
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = "{$backupPath}/{$filename}";

        // Construire la commande mysqldump
        $passwordOption = $password ? "-p\"{$password}\"" : '';
        $command = "mysqldump -h {$host} -P {$port} -u {$username} {$passwordOption} {$database} > \"{$fullPath}\"";

        // Exécuter le backup
        $this->info("Sauvegarde de la base de données {$database}...");
        
        $result = null;
        $output = null;
        exec($command . ' 2>&1', $output, $result);

        if ($result === 0) {
            $size = round(filesize($fullPath) / 1024, 2);
            $this->info("✅ Backup créé avec succès: {$filename} ({$size} KB)");
            
            // Nettoyer les anciens backups (garder les 7 derniers)
            $this->cleanOldBackups($backupPath, 7);
            
            return Command::SUCCESS;
        }

        $this->error("❌ Erreur lors du backup: " . implode("\n", $output));
        return Command::FAILURE;
    }

    private function cleanOldBackups(string $path, int $keep): void
    {
        $files = glob("{$path}/backup_*.sql");
        
        if (count($files) <= $keep) {
            return;
        }

        // Trier par date de modification (plus ancien en premier)
        usort($files, fn($a, $b) => filemtime($a) - filemtime($b));

        // Supprimer les plus anciens
        $toDelete = array_slice($files, 0, count($files) - $keep);
        
        foreach ($toDelete as $file) {
            unlink($file);
            $this->line("  Ancien backup supprimé: " . basename($file));
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class PurgeExpiredTokens extends Command
{
    protected $signature = 'tokens:purge';
    protected $description = 'Supprime les tokens API expirés';

    public function handle(): int
    {
        $expiration = config('sanctum.expiration');

        if (!$expiration) {
            $this->info('Aucune expiration configurée pour les tokens.');
            return Command::SUCCESS;
        }

        $expirationDate = now()->subMinutes($expiration);

        $deleted = PersonalAccessToken::where('created_at', '<', $expirationDate)->delete();

        $this->info("$deleted tokens expirés supprimés.");

        return Command::SUCCESS;
    }
}

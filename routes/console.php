<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tâches planifiées
|--------------------------------------------------------------------------
*/

// Backup quotidien de la base de données à 2h du matin
Schedule::command('db:backup')->dailyAt('02:00');

// Nettoyage des tokens expirés chaque jour à 3h du matin
Schedule::command('tokens:purge')->dailyAt('03:00');

// Nettoyage du cache des vues chaque semaine
Schedule::command('view:clear')->weekly();

// Notifications automatiques
Schedule::command('notifications:send licences --jours=30')->weeklyOn(1, '09:00'); // Lundi 9h
Schedule::command('notifications:send certificats --jours=30')->weeklyOn(1, '09:30'); // Lundi 9h30
Schedule::command('notifications:send arrieres')->monthlyOn(1, '10:00'); // 1er du mois
Schedule::command('notifications:send rappel-mensuel')->monthlyOn(5, '10:00'); // 5 du mois

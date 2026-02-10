<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\PortailAthleteController;
use App\Http\Controllers\Api\PortailParentController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes API pour l'application mobile OBD
|
*/

// Routes publiques avec rate limiting strict (5 tentatives par minute)
Route::middleware('throttle:5,1')->group(function () {
    // Login général (détecte automatiquement le rôle)
    Route::post('/login', [AuthController::class, 'login']);
    
    // Logins spécifiques par rôle
    Route::post('/login/athlete', [AuthController::class, 'loginAthlete']);
    Route::post('/login/parent', [AuthController::class, 'loginParent']);
    Route::post('/login/staff', [AuthController::class, 'loginStaff']);
    
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Routes Chatbot publiques (avec rate limiting modéré)
Route::middleware('throttle:30,1')->prefix('chatbot')->name('api.chatbot.')->group(function () {
    Route::get('/welcome', [ChatbotController::class, 'welcome'])->name('welcome');
    Route::get('/faqs', [ChatbotController::class, 'getFaqs'])->name('faqs');
    Route::post('/message', [ChatbotController::class, 'sendMessage'])->name('message');
    Route::get('/history', [ChatbotController::class, 'getHistory'])->name('history');
    Route::post('/escalate', [ChatbotController::class, 'escalateToSupport'])->name('escalate');
    Route::post('/close', [ChatbotController::class, 'closeConversation'])->name('close');
});

// Routes protégées avec rate limiting (60 requêtes par minute)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Athletes
    Route::apiResource('athletes', AthleteController::class)->names([
        'index' => 'api.athletes.index',
        'store' => 'api.athletes.store',
        'show' => 'api.athletes.show',
        'update' => 'api.athletes.update',
        'destroy' => 'api.athletes.destroy',
    ]);
    Route::get('/athletes/{athlete}/presences', [AthleteController::class, 'presences'])->name('api.athletes.presences');
    Route::get('/athletes/{athlete}/paiements', [AthleteController::class, 'paiements'])->name('api.athletes.paiements');
    Route::get('/athletes/{athlete}/performances', [AthleteController::class, 'performances'])->name('api.athletes.performances');
    
    // Disciplines
    Route::apiResource('disciplines', DisciplineController::class)->only(['index', 'show'])->names([
        'index' => 'api.disciplines.index',
        'show' => 'api.disciplines.show',
    ]);
    
    // Paiements
    Route::apiResource('paiements', PaiementController::class)->names([
        'index' => 'api.paiements.index',
        'store' => 'api.paiements.store',
        'show' => 'api.paiements.show',
        'update' => 'api.paiements.update',
        'destroy' => 'api.paiements.destroy',
    ]);
    Route::get('/paiements/arrieres', [PaiementController::class, 'arrieres'])->name('api.paiements.arrieres');
    Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])->name('api.paiements.recu');
    
    // Presences
    Route::apiResource('presences', PresenceController::class)->names([
        'index' => 'api.presences.index',
        'store' => 'api.presences.store',
        'show' => 'api.presences.show',
        'update' => 'api.presences.update',
        'destroy' => 'api.presences.destroy',
    ]);
    Route::get('/presences/rapport-mensuel', [PresenceController::class, 'rapportMensuel'])->name('api.presences.rapport-mensuel');
    
    // Performances
    Route::apiResource('performances', PerformanceController::class)->names([
        'index' => 'api.performances.index',
        'store' => 'api.performances.store',
        'show' => 'api.performances.show',
        'update' => 'api.performances.update',
        'destroy' => 'api.performances.destroy',
    ]);
    Route::get('/performances/dashboard', [PerformanceController::class, 'dashboard'])->name('api.performances.dashboard');
    Route::get('/performances/evolution/{athlete}', [PerformanceController::class, 'evolution'])->name('api.performances.evolution');

    // Rencontres - Routes spécifiques AVANT apiResource
    Route::get('/rencontres/a-venir', [\App\Http\Controllers\RencontreController::class, 'aVenir'])->name('api.rencontres.a-venir');
    Route::get('/rencontres/resultats', [\App\Http\Controllers\RencontreController::class, 'resultats'])->name('api.rencontres.resultats');
    Route::apiResource('rencontres', \App\Http\Controllers\RencontreController::class)->names([
        'index' => 'api.rencontres.index',
        'store' => 'api.rencontres.store',
        'show' => 'api.rencontres.show',
        'update' => 'api.rencontres.update',
        'destroy' => 'api.rencontres.destroy',
    ]);

    // Activités - Routes spécifiques AVANT apiResource
    Route::get('/activities/a-venir', [\App\Http\Controllers\ActivityController::class, 'aVenir'])->name('api.activities.a-venir');
    Route::apiResource('activities', \App\Http\Controllers\ActivityController::class)->only(['index', 'show'])->names([
        'index' => 'api.activities.index',
        'show' => 'api.activities.show',
    ]);
});

/*
|--------------------------------------------------------------------------
| Routes API Portail Athlète (réservées aux athlètes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'athlete', 'throttle:60,1'])->prefix('athlete')->name('api.athlete.')->group(function () {
    Route::get('/', [PortailAthleteController::class, 'dashboard'])->name('dashboard');
    Route::get('/presences', [PortailAthleteController::class, 'presences'])->name('presences');
    Route::get('/suivi-scolaire', [PortailAthleteController::class, 'suiviScolaire'])->name('suivi-scolaire');
    Route::get('/paiements', [PortailAthleteController::class, 'paiements'])->name('paiements');
    Route::get('/performances', [PortailAthleteController::class, 'performances'])->name('performances');
    Route::get('/calendrier', [PortailAthleteController::class, 'calendrier'])->name('calendrier');
    Route::get('/profil', [PortailAthleteController::class, 'profil'])->name('profil');
});

/*
|--------------------------------------------------------------------------
| Routes API Portail Parent (réservées aux parents)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'parent', 'throttle:60,1'])->prefix('parent')->name('api.parent.')->group(function () {
    Route::get('/', [PortailParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/enfants', [PortailParentController::class, 'enfants'])->name('enfants');
    Route::get('/enfants/{athlete}', [PortailParentController::class, 'enfantShow'])->name('enfants.show');
    Route::get('/enfants/{athlete}/presences', [PortailParentController::class, 'presences'])->name('presences');
    Route::get('/enfants/{athlete}/suivi-scolaire', [PortailParentController::class, 'suiviScolaire'])->name('suivi-scolaire');
    Route::get('/enfants/{athlete}/paiements', [PortailParentController::class, 'paiements'])->name('paiements');
    Route::get('/enfants/{athlete}/performances', [PortailParentController::class, 'performances'])->name('performances');
    Route::get('/calendrier', [PortailParentController::class, 'calendrier'])->name('calendrier');
    Route::get('/profil', [PortailParentController::class, 'profil'])->name('profil');
});

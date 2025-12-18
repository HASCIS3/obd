<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
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
});

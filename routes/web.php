<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuiviScolaireController;
use App\Http\Controllers\LicenceController;
use App\Http\Controllers\CertificatMedicalController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SaisonController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\StageFormationController;
use App\Http\Controllers\PortailParentController;
use App\Http\Controllers\PortailAthleteController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\RencontreController;
use App\Http\Controllers\CombatTaekwondoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes publiques pour soumission de bulletin par l'école
Route::get('/bulletin/soumettre/{token}', [BulletinController::class, 'formulaireEcole'])->name('bulletin.formulaire');
Route::post('/bulletin/soumettre/{token}', [BulletinController::class, 'soumettreBulletin'])->name('bulletin.soumettre');
Route::get('/bulletin/confirmation/{suivi}', [BulletinController::class, 'confirmation'])->name('bulletin.confirmation');

/*
|--------------------------------------------------------------------------
| Routes authentifiées (tous les utilisateurs connectés)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapport-mensuel', [DashboardController::class, 'rapportMensuel'])->name('rapport.mensuel');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Activités (lecture pour tous les utilisateurs connectés)
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
});

/*
|--------------------------------------------------------------------------
| Routes Admin - Gestion des activités (AVANT la route {activity})
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])->prefix('activities')->group(function () {
    Route::get('/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::post('/{activity}/medias', [ActivityController::class, 'addMedia'])->name('activities.medias.store');
    Route::delete('/{activity}/medias/{media}', [ActivityController::class, 'deleteMedia'])->name('activities.medias.destroy');
});

/*
|--------------------------------------------------------------------------
| Route show activité (APRÈS /create pour éviter conflit)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
});


/*
|--------------------------------------------------------------------------
| Routes Admin - routes /create avant les routes avec paramètres
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Routes /create pour éviter conflit avec {param}
    Route::get('/disciplines/create', [DisciplineController::class, 'create'])->name('disciplines.create');
    
    // Gestion complète des disciplines
    Route::post('/disciplines', [DisciplineController::class, 'store'])->name('disciplines.store');
    Route::get('/disciplines/{discipline}/edit', [DisciplineController::class, 'edit'])->name('disciplines.edit');
    Route::put('/disciplines/{discipline}', [DisciplineController::class, 'update'])->name('disciplines.update');
    Route::delete('/disciplines/{discipline}', [DisciplineController::class, 'destroy'])->name('disciplines.destroy');
    
    // Gestion des athlètes (édition/suppression réservées aux admins)
    Route::get('/athletes/{athlete}/edit', [AthleteController::class, 'edit'])->name('athletes.edit');
    Route::put('/athletes/{athlete}', [AthleteController::class, 'update'])->name('athletes.update');
    Route::delete('/athletes/{athlete}', [AthleteController::class, 'destroy'])->name('athletes.destroy');

    // Compte athlète
    Route::get('/athletes/{athlete}/compte', [AthleteController::class, 'createAccount'])->name('athletes.account.create');
    Route::post('/athletes/{athlete}/compte', [AthleteController::class, 'storeAccount'])->name('athletes.account.store');
});

/*
|--------------------------------------------------------------------------
| Routes Coach (coachs et admins)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'coach'])->group(function () {
    // Athlètes (lecture + création pour coachs)
    Route::get('/athletes', [AthleteController::class, 'index'])->name('athletes.index');
    Route::get('/athletes/create', [AthleteController::class, 'create'])->name('athletes.create');
    Route::post('/athletes', [AthleteController::class, 'store'])->name('athletes.store');
    Route::get('/athletes/{athlete}', [AthleteController::class, 'show'])->name('athletes.show');

    // Disciplines (lecture seule)
    Route::get('/disciplines', [DisciplineController::class, 'index'])->name('disciplines.index');
    Route::get('/disciplines/{discipline}', [DisciplineController::class, 'show'])->name('disciplines.show');

    // Paiements (lecture + création pour coachs)
    Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::get('/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
    Route::get('/paiements/suivi-annuel', [PaiementController::class, 'suiviAnnuel'])->name('paiements.suivi-annuel');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
    Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');
    Route::get('/paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show')->where('paiement', '[0-9]+');

    // Présences (gestion complète pour coachs)
    Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');
    Route::get('/presences/create', [PresenceController::class, 'create'])->name('presences.create');
    Route::get('/presences/pointage', [PresenceController::class, 'pointageQuotidien'])->name('presences.pointage');
    Route::post('/presences', [PresenceController::class, 'store'])->name('presences.store');
    Route::get('/presences/athlete/{athlete}', [PresenceController::class, 'athleteStats'])->name('presences.athlete');
    Route::get('/presences/rapport-mensuel', [PresenceController::class, 'rapportMensuel'])->name('presences.rapport-mensuel');

    // Système de pointage professionnel (quotidien, hebdomadaire, mensuel, annuel)
    Route::get('/pointage/quotidien', [PointageController::class, 'quotidien'])->name('presences.pointage.quotidien');
    Route::get('/pointage/hebdomadaire', [PointageController::class, 'hebdomadaire'])->name('presences.pointage.hebdomadaire');
    Route::get('/pointage/mensuel', [PointageController::class, 'mensuel'])->name('presences.pointage.mensuel');
    Route::get('/pointage/annuel', [PointageController::class, 'annuel'])->name('presences.pointage.annuel');

    // Matchs et rencontres sportives (lecture pour tous)
    Route::get('/rencontres', [RencontreController::class, 'index'])->name('rencontres.index');
    Route::get('/rencontres/statistiques', [RencontreController::class, 'statistiques'])->name('rencontres.statistiques');
    Route::get('/rencontres/calendrier', [RencontreController::class, 'calendrier'])->name('rencontres.calendrier');
    Route::get('/rencontres/{rencontre}', [RencontreController::class, 'show'])->name('rencontres.show')->where('rencontre', '[0-9]+');

    // Performances (lecture et création pour coachs)
    Route::get('/performances', [PerformanceController::class, 'index'])->name('performances.index');
    Route::get('/performances/dashboard', [PerformanceController::class, 'dashboard'])->name('performances.dashboard');
    Route::get('/performances/create', [PerformanceController::class, 'create'])->name('performances.create');
    Route::post('/performances', [PerformanceController::class, 'store'])->name('performances.store');
    Route::get('/performances/evolution/{athlete}', [PerformanceController::class, 'evolutionAthlete'])->name('performances.evolution');
    Route::get('/performances/{performance}', [PerformanceController::class, 'show'])->name('performances.show')->where('performance', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| Routes Admin uniquement
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Gestion complète des coachs
    Route::resource('coachs', CoachController::class);

    // Gestion des paiements (édition/suppression réservées aux admins)
    Route::get('/paiements/arrieres', [PaiementController::class, 'arrieres'])->name('paiements.arrieres');
    Route::post('/paiements/generer-mensuel', [PaiementController::class, 'genererMensuel'])->name('paiements.generer-mensuel');
    Route::get('/paiements/{paiement}/edit', [PaiementController::class, 'edit'])->name('paiements.edit');
    Route::put('/paiements/{paiement}', [PaiementController::class, 'update'])->name('paiements.update');
    Route::delete('/paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');

    // Gestion complète des suivis scolaires
    Route::get('/suivis-scolaires/dashboard', [SuiviScolaireController::class, 'dashboard'])->name('suivis-scolaires.dashboard');
    Route::get('/suivis-scolaires/gestion-bulletins', [SuiviScolaireController::class, 'gestionBulletins'])->name('suivis-scolaires.gestion-bulletins');
    Route::get('/suivis-scolaires/rapport-athlete/{athlete}', [SuiviScolaireController::class, 'rapportAthlete'])->name('suivis-scolaires.rapport-athlete');
    Route::get('/suivis-scolaires/rapport-parent/{athlete}', [SuiviScolaireController::class, 'rapportParent'])->name('suivis-scolaires.rapport-parent');
    Route::resource('suivis-scolaires', SuiviScolaireController::class);

    // Gestion des bulletins et rapports
    Route::post('/bulletin/generer-lien/{athlete}', [BulletinController::class, 'genererLien'])->name('bulletin.generer-lien');
    Route::post('/bulletin/regenerer-lien/{athlete}', [BulletinController::class, 'regenererLien'])->name('bulletin.regenerer-lien');
    Route::get('/bulletin/rapport-pdf/{athlete}', [BulletinController::class, 'rapportPdf'])->name('bulletin.rapport-pdf');
    Route::post('/bulletin/envoyer-rapport/{athlete}', [BulletinController::class, 'envoyerRapport'])->name('bulletin.envoyer-rapport');

    // Gestion complète des performances (édition/suppression)
    Route::get('/performances/{performance}/edit', [PerformanceController::class, 'edit'])->name('performances.edit');
    Route::put('/performances/{performance}', [PerformanceController::class, 'update'])->name('performances.update');
    Route::delete('/performances/{performance}', [PerformanceController::class, 'destroy'])->name('performances.destroy');

    // Gestion complète des matchs et rencontres (admin uniquement)
    Route::get('/rencontres/create', [RencontreController::class, 'create'])->name('rencontres.create');
    Route::post('/rencontres', [RencontreController::class, 'store'])->name('rencontres.store');
    Route::get('/rencontres/{rencontre}/edit', [RencontreController::class, 'edit'])->name('rencontres.edit');
    Route::put('/rencontres/{rencontre}', [RencontreController::class, 'update'])->name('rencontres.update');
    Route::delete('/rencontres/{rencontre}', [RencontreController::class, 'destroy'])->name('rencontres.destroy');
    Route::get('/rencontres/{rencontre}/participations', [RencontreController::class, 'participations'])->name('rencontres.participations');
    Route::post('/rencontres/{rencontre}/participations', [RencontreController::class, 'storeParticipations'])->name('rencontres.participations.store');

    // Combats Taekwondo (sports individuels)
    Route::get('/rencontres/{rencontre}/combats', [CombatTaekwondoController::class, 'index'])->name('combats-taekwondo.index');
    Route::get('/rencontres/{rencontre}/combats/create', [CombatTaekwondoController::class, 'create'])->name('combats-taekwondo.create');
    Route::post('/rencontres/{rencontre}/combats', [CombatTaekwondoController::class, 'store'])->name('combats-taekwondo.store');
    Route::get('/rencontres/{rencontre}/combats/{combat}/saisie', [CombatTaekwondoController::class, 'saisie'])->name('combats-taekwondo.saisie');
    Route::put('/rencontres/{rencontre}/combats/{combat}/scores', [CombatTaekwondoController::class, 'updateScores'])->name('combats-taekwondo.update-scores');
    Route::put('/rencontres/{rencontre}/combats/{combat}/terminer', [CombatTaekwondoController::class, 'terminer'])->name('combats-taekwondo.terminer');
    Route::delete('/rencontres/{rencontre}/combats/{combat}', [CombatTaekwondoController::class, 'destroy'])->name('combats-taekwondo.destroy');

    // Gestion des licences sportives
    Route::get('/licences/expirant-bientot', [LicenceController::class, 'expirantBientot'])->name('licences.expirant-bientot');
    Route::post('/licences/verifier-expirations', [LicenceController::class, 'verifierExpirations'])->name('licences.verifier-expirations');
    Route::post('/licences/{licence}/renouveler', [LicenceController::class, 'renouveler'])->name('licences.renouveler');
    Route::resource('licences', LicenceController::class);

    // Gestion des certificats médicaux
    Route::get('/certificats-medicaux/expirant-bientot', [CertificatMedicalController::class, 'expirantBientot'])->name('certificats-medicaux.expirant-bientot');
    Route::post('/certificats-medicaux/verifier-expirations', [CertificatMedicalController::class, 'verifierExpirations'])->name('certificats-medicaux.verifier-expirations');
    Route::resource('certificats-medicaux', CertificatMedicalController::class);

    // Exports PDF/Excel
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/athletes/excel', [ExportController::class, 'athletesExcel'])->name('athletes.excel');
        Route::get('/athletes/pdf', [ExportController::class, 'athletesPdf'])->name('athletes.pdf');
        Route::get('/athletes/{athlete}/fiche', [ExportController::class, 'ficheAthletePdf'])->name('athletes.fiche');
        Route::get('/licences/excel', [ExportController::class, 'licencesExcel'])->name('licences.excel');
        Route::get('/licences/pdf', [ExportController::class, 'licencesPdf'])->name('licences.pdf');
        Route::get('/paiements/excel', [ExportController::class, 'paiementsExcel'])->name('paiements.excel');
        Route::get('/paiements/pdf', [ExportController::class, 'paiementsPdf'])->name('paiements.pdf');
    });

    // Gestion des saisons
    Route::post('/saisons/{saison}/activer', [SaisonController::class, 'activer'])->name('saisons.activer');
    Route::post('/saisons/{saison}/archiver', [SaisonController::class, 'archiver'])->name('saisons.archiver');
    Route::resource('saisons', SaisonController::class);

    // Gestion des factures
    Route::post('/factures/{facture}/emettre', [FactureController::class, 'emettre'])->name('factures.emettre');
    Route::post('/factures/{facture}/paiement', [FactureController::class, 'enregistrerPaiement'])->name('factures.paiement');
    Route::post('/factures/{facture}/annuler', [FactureController::class, 'annuler'])->name('factures.annuler');
    Route::get('/factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
    Route::resource('factures', FactureController::class);

    // Calendrier global
    Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier.index');
    Route::get('/calendrier/events', [CalendrierController::class, 'events'])->name('calendrier.events');
    Route::get('/calendrier/a-venir', [CalendrierController::class, 'aVenir'])->name('calendrier.a-venir');
    Route::post('/calendrier/evenements', [CalendrierController::class, 'store'])->name('calendrier.store');
    Route::get('/calendrier/evenements/{evenement}', [CalendrierController::class, 'show'])->name('calendrier.show');
    Route::put('/calendrier/evenements/{evenement}', [CalendrierController::class, 'update'])->name('calendrier.update');
    Route::delete('/calendrier/evenements/{evenement}', [CalendrierController::class, 'destroy'])->name('calendrier.destroy');

    // Stages de formation
    Route::get('/stages-formation/{stages_formation}/inscriptions', [StageFormationController::class, 'inscriptions'])->name('stages-formation.inscriptions');
    Route::post('/stages-formation/{stages_formation}/inscriptions', [StageFormationController::class, 'storeInscription'])->name('stages-formation.inscriptions.store');
    Route::get('/stages-formation/{stages_formation}/diplomes', [StageFormationController::class, 'diplomes'])->name('stages-formation.diplomes');
    Route::get('/stages-formation/{stages_formation}/liste-participants-pdf', [StageFormationController::class, 'listeParticipantsPdf'])->name('stages-formation.liste-participants-pdf');
    Route::resource('stages-formation', StageFormationController::class);
    
    // Inscriptions aux stages
    Route::put('/inscriptions/{inscription}', [StageFormationController::class, 'updateInscription'])->name('inscriptions.update');
    Route::delete('/inscriptions/{inscription}', [StageFormationController::class, 'destroyInscription'])->name('inscriptions.destroy');
    Route::post('/inscriptions/{inscription}/delivrer-certificat', [StageFormationController::class, 'delivrerCertificat'])->name('inscriptions.delivrer-certificat');
    Route::get('/inscriptions/{inscription}/certificat-pdf', [StageFormationController::class, 'certificatPdf'])->name('inscriptions.certificat-pdf');
});

/*
|--------------------------------------------------------------------------
| Routes Portail Athlète
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'athlete'])->prefix('athlete')->name('athlete.')->group(function () {
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
| Routes Portail Parent
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/', [PortailParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/enfants', [PortailParentController::class, 'enfants'])->name('enfants');
    Route::get('/enfants/{athlete}', [PortailParentController::class, 'enfantShow'])->name('enfants.show');
    Route::get('/enfants/{athlete}/presences', [PortailParentController::class, 'presences'])->name('presences');
    Route::get('/enfants/{athlete}/suivi-scolaire', [PortailParentController::class, 'suiviScolaire'])->name('suivi-scolaire');
    Route::get('/enfants/{athlete}/paiements', [PortailParentController::class, 'paiements'])->name('paiements');
    Route::get('/enfants/{athlete}/performances', [PortailParentController::class, 'performances'])->name('performances');
    Route::get('/calendrier', [PortailParentController::class, 'calendrier'])->name('calendrier');
    Route::get('/profil', [PortailParentController::class, 'profil'])->name('profil');
    Route::put('/profil', [PortailParentController::class, 'updateProfil'])->name('profil.update');
});

require __DIR__.'/auth.php';

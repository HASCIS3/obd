<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Page de sélection du portail (nouvelle page d'accueil login)
    Route::get('login', [RoleLoginController::class, 'showSelect'])->name('login');
    Route::get('login/select', [RoleLoginController::class, 'showSelect'])->name('login.select');
    
    // Login Staff (Admin/Coach)
    Route::get('login/staff', [RoleLoginController::class, 'showStaffLogin'])->name('login.staff');
    Route::post('login/staff', [RoleLoginController::class, 'loginStaff'])->name('login.staff.submit');
    
    // Login Athlète
    Route::get('login/athlete', [RoleLoginController::class, 'showAthleteLogin'])->name('login.athlete');
    Route::post('login/athlete', [RoleLoginController::class, 'loginAthlete'])->name('login.athlete.submit');
    
    // Login Parent
    Route::get('login/parent', [RoleLoginController::class, 'showParentLogin'])->name('login.parent');
    Route::post('login/parent', [RoleLoginController::class, 'loginParent'])->name('login.parent.submit');
    
    // Ancien login (redirige vers sélection ou garde pour compatibilité)
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.legacy');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

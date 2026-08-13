<?php

use App\Http\Controllers\Admin\AdministrateurController;
use App\Http\Controllers\Admin\ComptabiliteController;
use App\Http\Controllers\Admin\EmployeController;
use App\Http\Controllers\Admin\RejetDocumentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DisponibiliteController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::resource('clients', ClientController::class)
    ->middleware(['auth'])
    ->except(['show']);

Route::resource('reservations', ReservationController::class)
    ->middleware(['auth'])
    ->except(['edit']);

Route::middleware(['auth'])->group(function () {
    Route::post('reservations/{reservation}/document', [ReservationController::class, 'uploadDocument'])->name('reservations.upload-document');
    Route::post('reservations/{reservation}/valider-document', [ReservationController::class, 'validerDocument'])->name('reservations.valider-document');
    Route::get('reservations/{reservation}/document', [ReservationController::class, 'telechargerDocument'])->name('reservations.telecharger-document');

    Route::get('disponibilites', [DisponibiliteController::class, 'index'])->name('disponibilites.index');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:administrateur'])
    ->group(function () {
        Route::get('employes', [EmployeController::class, 'index'])->name('employes.index');
        Route::get('employes/create', [EmployeController::class, 'create'])->name('employes.create');
        Route::post('employes', [EmployeController::class, 'store'])->name('employes.store');
        Route::delete('employes/{employe}', [EmployeController::class, 'destroy'])->name('employes.destroy');

        Route::get('administrateurs/create', [AdministrateurController::class, 'create'])->name('administrateurs.create');
        Route::post('administrateurs', [AdministrateurController::class, 'store'])->name('administrateurs.store');

        Route::get('comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite.index');

        Route::get('rejets-documents', [RejetDocumentController::class, 'index'])->name('rejets.index');
    });

require __DIR__.'/auth.php';

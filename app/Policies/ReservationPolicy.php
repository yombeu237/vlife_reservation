<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    /** Employés et admins peuvent consulter la liste. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Employés et admins peuvent voir le détail. */
    public function view(User $user, Reservation $reservation): bool
    {
        return true;
    }

    /** Employés et admins peuvent créer. */
    public function create(User $user): bool
    {
        return true;
    }

    /** Employés et admins peuvent modifier le statut / montant (dans les limites métier gérées ailleurs). */
    public function update(User $user, Reservation $reservation): bool
    {
        return true;
    }

    /** Seul un administrateur peut annuler une réservation. */
    public function annuler(User $user, Reservation $reservation): bool
    {
        return $user->isAdministrateur();
    }

    /** Employés et admins peuvent gérer les documents justificatifs. */
    public function gererDocument(User $user, Reservation $reservation): bool
    {
        return $reservation->statut !== 'annule';
    }

    /** Seul un administrateur accède à la comptabilité. */
    public function voirComptabilite(User $user): bool
    {
        return $user->isAdministrateur();
    }
}

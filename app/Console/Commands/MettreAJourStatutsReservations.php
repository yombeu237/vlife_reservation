<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reservations:auto-statuts')]
#[Description("Fait basculer les réservations « deja_paye » vers « en_cours » puis « termine » selon la date et l'heure système (gère le multi-jours).")]
class MettreAJourStatutsReservations extends Command
{
    public function handle(): int
    {
        $now         = now();
        $today       = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // 1) Réservations dont la période est entièrement passée → terminé.
        //    COALESCE(date_fin, date_reservation) = date de fin effective.
        $vers_termine_passe = Reservation::whereIn('statut', ['deja_paye', 'en_cours'])
            ->whereRaw('COALESCE(date_fin, date_reservation) < ?', [$today])
            ->update([
                'statut'               => 'termine',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        // 2) Plages horaires du jour dont l'heure de fin est dépassée → terminé.
        $vers_termine_plage = Reservation::whereIn('statut', ['deja_paye', 'en_cours'])
            ->where('type_creneau', 'plage_horaire')
            ->where('date_reservation', $today)
            ->where('heure_fin', '<=', $currentTime)
            ->update([
                'statut'               => 'termine',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        // 3) Réservations journée / multi-jours actives aujourd'hui → en cours.
        $vers_en_cours_journee = Reservation::where('statut', 'deja_paye')
            ->where('type_creneau', 'journee')
            ->where('date_reservation', '<=', $today)
            ->whereRaw('COALESCE(date_fin, date_reservation) >= ?', [$today])
            ->update([
                'statut'               => 'en_cours',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        // 4) Plages horaires du jour dont le créneau a commencé et n'est pas fini → en cours.
        $vers_en_cours_plage = Reservation::where('statut', 'deja_paye')
            ->where('type_creneau', 'plage_horaire')
            ->where('date_reservation', $today)
            ->where('heure_debut', '<=', $currentTime)
            ->where('heure_fin', '>', $currentTime)
            ->update([
                'statut'               => 'en_cours',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        $vers_en_cours = $vers_en_cours_journee + $vers_en_cours_plage;
        $vers_termine  = $vers_termine_passe + $vers_termine_plage;
        $total         = $vers_en_cours + $vers_termine;

        $this->info(sprintf(
            'Bascule terminée : %d en_cours, %d terminé. Total : %d.',
            $vers_en_cours,
            $vers_termine,
            $total
        ));

        return Command::SUCCESS;
    }
}

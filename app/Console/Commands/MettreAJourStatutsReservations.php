<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reservations:auto-statuts')]
#[Description("Fait basculer les réservations « deja_paye » vers « en_cours » puis « termine » selon l'heure système.")]
class MettreAJourStatutsReservations extends Command
{
    public function handle(): int
    {
        $now         = now();
        $today       = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $vers_en_cours = Reservation::where('statut', 'deja_paye')
            ->where('date_reservation', $today)
            ->where(function ($q) use ($currentTime) {
                $q->where('type_creneau', 'journee')
                  ->orWhere(function ($qq) use ($currentTime) {
                      $qq->where('type_creneau', 'plage_horaire')
                         ->where('heure_debut', '<=', $currentTime);
                  });
            })
            ->update([
                'statut'               => 'en_cours',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        $vers_termine_hier = Reservation::whereIn('statut', ['deja_paye', 'en_cours'])
            ->where('date_reservation', '<', $today)
            ->update([
                'statut'               => 'termine',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        $vers_termine_plage = Reservation::whereIn('statut', ['deja_paye', 'en_cours'])
            ->where('date_reservation', $today)
            ->where('type_creneau', 'plage_horaire')
            ->where('heure_fin', '<=', $currentTime)
            ->update([
                'statut'               => 'termine',
                'modifie_manuellement' => false,
                'updated_at'           => $now,
            ]);

        $total = $vers_en_cours + $vers_termine_hier + $vers_termine_plage;

        $this->info(sprintf(
            'Bascule terminée : %d en_cours, %d terminé (dates passées), %d terminé (fin de plage). Total : %d.',
            $vers_en_cours,
            $vers_termine_hier,
            $vers_termine_plage,
            $total
        ));

        return Command::SUCCESS;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionReservationSeeder extends Seeder
{
    public function run(): void
    {
        $sportbarId  = DB::table('compartiments')->where('nom', 'VLounge-Sportbar')->value('id');
        $coworkingId = DB::table('compartiments')->where('nom', 'VCoworking')->value('id');

        if (! $sportbarId || ! $coworkingId) {
            throw new \RuntimeException(
                'CompartimentSeeder doit être exécuté avant OptionReservationSeeder.'
            );
        }

        $now = now();

        DB::table('options_reservation')->insert([
            // VLounge-Sportbar — 4 options
            [
                'compartiment_id'   => $sportbarId,
                'libelle'           => 'Option A — Table sans location',
                'tarif'             => 3000,
                'regle_description' => "Occupation d'au moins une table. Minimum 3000 FCFA/personne assise (plancher calculé automatiquement, consommation au-delà réglée sur place). Capacité totale 80 places partagée entre plusieurs réservations simultanées.",
                'capacite'          => 80,
                'type_calcul'       => 'par_personne',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'compartiment_id'   => $sportbarId,
                'libelle'           => "Option B — Location d'espace",
                'tarif'             => 0,
                'regle_description' => "Consommation libre, payée sur place. Capacité exclusive : 1 réservation à la fois sur cet espace.",
                'capacite'          => 1,
                'type_calcul'       => 'libre',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'compartiment_id'   => $sportbarId,
                'libelle'           => 'Option C — Table avec forfait 5000 FCFA/personne',
                'tarif'             => 5000,
                'regle_description' => "Forfait 5000 FCFA/personne (4000 FCFA frais d'occupation + 1000 FCFA consommation). Capacité partagée 80 places.",
                'capacite'          => 80,
                'type_calcul'       => 'par_personne',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'compartiment_id'   => $sportbarId,
                'libelle'           => 'Option D — Location de la salle du Lounge',
                'tarif'             => 0,
                'regle_description' => "Location de la salle complète du Lounge (~200 places). Tarif négocié au cas par cas par l'employé (saisi manuellement dans le montant de la réservation). Capacité exclusive : 1 réservation à la fois.",
                'capacite'          => 1,
                'type_calcul'       => 'libre',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],

            // VCoworking — 4 options
            [
                'compartiment_id'   => $coworkingId,
                'libelle'           => 'Bureau 1',
                'tarif'             => 4000,
                'regle_description' => 'Bureau individuel. Capacité exclusive : 1 réservation à la fois.',
                'capacite'          => 1,
                'type_calcul'       => 'fixe',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'compartiment_id'   => $coworkingId,
                'libelle'           => 'Bureau 2',
                'tarif'             => 4000,
                'regle_description' => 'Bureau individuel. Capacité exclusive : 1 réservation à la fois.',
                'capacite'          => 1,
                'type_calcul'       => 'fixe',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'compartiment_id'   => $coworkingId,
                'libelle'           => 'Bureau 3',
                'tarif'             => 10000,
                'regle_description' => 'Bureau individuel premium. Capacité exclusive : 1 réservation à la fois.',
                'capacite'          => 1,
                'type_calcul'       => 'fixe',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'compartiment_id'   => $coworkingId,
                'libelle'           => 'Salle de conférence',
                'tarif'             => 40000,
                'regle_description' => 'Salle de conférence à la journée. Capacité exclusive : 1 réservation à la fois.',
                'capacite'          => 1,
                'type_calcul'       => 'fixe',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);
    }
}

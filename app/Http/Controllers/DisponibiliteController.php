<?php

namespace App\Http\Controllers;

use App\Models\Compartiment;
use App\Models\Reservation;
use Illuminate\View\View;

class DisponibiliteController extends Controller
{
    public function index(): View
    {
        $now         = now();
        $today       = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $compartiments = Compartiment::with('optionsReservation')
            ->orderBy('nom')
            ->get();

        $etats = [];
        foreach ($compartiments as $compartiment) {
            foreach ($compartiment->optionsReservation as $option) {
                $actives = Reservation::query()
                    ->with(['client', 'utilisateur'])
                    ->where('option_id', $option->id)
                    ->whereIn('statut', ['deja_paye', 'en_cours'])
                    // La réservation couvre aujourd'hui (gère le multi-jours).
                    ->where('date_reservation', '<=', $today)
                    ->whereRaw('COALESCE(date_fin, date_reservation) >= ?', [$today])
                    ->where(function ($q) use ($currentTime) {
                        // Journée / multi-jours : occupe toute la journée.
                        $q->where('type_creneau', 'journee')
                          ->orWhere(function ($qq) use ($currentTime) {
                              // Plage horaire : occupe seulement pendant son créneau.
                              $qq->where('type_creneau', 'plage_horaire')
                                 ->where('heure_debut', '<=', $currentTime)
                                 ->where('heure_fin', '>', $currentTime);
                          });
                    })
                    ->get();

                $capacite      = (int) $option->capacite;
                $exclusive     = $capacite <= 1;
                $occupation    = $exclusive
                    ? ($actives->count() > 0 ? 1 : 0)
                    : (int) $actives->sum('nombre_personnes');
                $restant       = max(0, $capacite - $occupation);
                $disponible    = $exclusive ? ($actives->count() === 0) : ($restant > 0);

                $etats[] = [
                    'compartiment' => $compartiment->nom,
                    'option'       => $option,
                    'exclusive'    => $exclusive,
                    'capacite'     => $capacite,
                    'occupation'   => $occupation,
                    'restant'      => $restant,
                    'disponible'   => $disponible,
                    'actives'      => $actives,
                    'prochaine'    => $this->prochaineReservation($option->id, $today, $currentTime),
                ];
            }
        }

        return view('disponibilites.index', [
            'etats' => $etats,
            'now'   => $now,
        ]);
    }

    protected function prochaineReservation(int $optionId, string $today, string $currentTime): ?Reservation
    {
        return Reservation::query()
            ->with('client')
            ->where('option_id', $optionId)
            ->where('date_reservation', $today)
            ->whereIn('statut', ['deja_paye'])
            ->where('type_creneau', 'plage_horaire')
            ->where('heure_debut', '>', $currentTime)
            ->orderBy('heure_debut')
            ->first();
    }
}

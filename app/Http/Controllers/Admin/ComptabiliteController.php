<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compartiment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComptabiliteController extends Controller
{
    public function index(Request $request): View
    {
        // Double barrière : middleware role:administrateur sur la route + Policy ici.
        $this->authorize('voirComptabilite', Reservation::class);

        $compartimentId = $request->query('compartiment_id');
        $du             = $request->query('du');
        $au             = $request->query('au');

        // Base : uniquement les réservations payées (jamais les annulées ni la liste d'attente).
        $base = Reservation::query()
            ->payees()
            ->with(['client', 'utilisateur', 'option.compartiment'])
            ->when($compartimentId, function ($q) use ($compartimentId) {
                $q->whereHas('option', fn ($qq) => $qq->where('compartiment_id', $compartimentId));
            })
            ->when($du, fn ($q) => $q->whereDate('date_reservation', '>=', $du))
            ->when($au, fn ($q) => $q->whereDate('date_reservation', '<=', $au));

        // Historique détaillé (pagination).
        $historique = (clone $base)
            ->orderByDesc('date_reservation')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        // Total global de la sélection.
        $totalGains = (clone $base)->sum('montant');
        $nbReservations = (clone $base)->count();

        // Synthèses jour / semaine / mois en cours (indépendantes des filtres de période,
        // mais tiennent compte du filtre compartiment).
        $baseSansPeriode = Reservation::query()
            ->payees()
            ->when($compartimentId, function ($q) use ($compartimentId) {
                $q->whereHas('option', fn ($qq) => $qq->where('compartiment_id', $compartimentId));
            });

        $totalJour = (clone $baseSansPeriode)
            ->whereDate('date_reservation', today())
            ->sum('montant');

        $totalSemaine = (clone $baseSansPeriode)
            ->whereBetween('date_reservation', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->sum('montant');

        $totalMois = (clone $baseSansPeriode)
            ->whereBetween('date_reservation', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('montant');

        // Répartition mensuelle sur les 6 derniers mois (pour le mini-graphe).
        $repartitionMensuelle = (clone $baseSansPeriode)
            ->where('date_reservation', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
            ->get()
            ->groupBy(fn ($r) => $r->date_reservation->format('Y-m'))
            ->map(fn ($grp) => (int) $grp->sum('montant'))
            ->sortKeys();

        return view('admin.comptabilite.index', [
            'historique'           => $historique,
            'totalGains'           => (int) $totalGains,
            'nbReservations'       => $nbReservations,
            'totalJour'            => (int) $totalJour,
            'totalSemaine'         => (int) $totalSemaine,
            'totalMois'            => (int) $totalMois,
            'repartitionMensuelle' => $repartitionMensuelle,
            'compartiments'        => Compartiment::orderBy('nom')->get(),
            'compartimentSelected' => $compartimentId,
            'du'                   => $du,
            'au'                   => $au,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Client;
use App\Models\Compartiment;
use App\Models\OptionReservation;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $statut         = $request->query('statut');
        $compartimentId = $request->query('compartiment_id');

        $reservations = Reservation::query()
            ->with(['client', 'utilisateur', 'option.compartiment'])
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->when($compartimentId, function ($q) use ($compartimentId) {
                $q->whereHas('option', fn ($qq) => $qq->where('compartiment_id', $compartimentId));
            })
            ->orderByDesc('date_reservation')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('reservations.index', [
            'reservations'         => $reservations,
            'compartiments'        => Compartiment::orderBy('nom')->get(),
            'statutSelected'       => $statut,
            'compartimentSelected' => $compartimentId,
        ]);
    }

    public function create(): View
    {
        return view('reservations.create', [
            'clients'       => Client::orderBy('nom')->get(),
            'compartiments' => Compartiment::with('optionsReservation')->orderBy('nom')->get(),
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Une plage horaire est forcée sur un seul jour ; sinon date_fin par défaut = date de début.
        $dateFin = $data['type_creneau'] === 'plage_horaire'
            ? $data['date_reservation']
            : ($data['date_fin'] ?? $data['date_reservation']);

        $reservation = DB::transaction(function () use ($data, $dateFin, $request) {
            return Reservation::create([
                'client_id'            => $data['client_id'],
                'utilisateur_id'       => $request->user()->id,
                'option_id'            => $data['option_id'],
                'date_reservation'     => $data['date_reservation'],
                'date_fin'             => $dateFin,
                'type_creneau'         => $data['type_creneau'],
                'heure_debut'          => $data['heure_debut'] ?? null,
                'heure_fin'            => $data['heure_fin'] ?? null,
                'nombre_personnes'     => $data['nombre_personnes'],
                'montant'              => $data['montant'],
                'statut'               => 'liste_attente',
                'modifie_manuellement' => false,
            ]);
        });

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('status', "Réservation #{$reservation->id} créée (statut : liste d'attente). Téléversez le document justificatif pour valider.");
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['client', 'utilisateur', 'option.compartiment']);

        return view('reservations.show', compact('reservation'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validated();

        $updates          = ['modifie_manuellement' => true];
        $messagesModifies = [];

        if (! empty($data['statut'])) {
            $documentValide   = ! is_null($reservation->date_validation_preuve);
            $statutsAutorises = $documentValide
                ? ['liste_attente', 'en_cours', 'termine', 'annule']
                : ['liste_attente', 'annule'];

            if (! in_array($data['statut'], $statutsAutorises, true)) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'statut' => $documentValide
                            ? 'Statut non autorisé.'
                            : "Tant que le document justificatif n'est pas validé, seuls « Liste d'attente » et « Annulé » peuvent être choisis manuellement.",
                    ]);
            }

            if ($data['statut'] === 'annule') {
                $this->authorize('annuler', $reservation);
            }

            $updates['statut']  = $data['statut'];
            $messagesModifies[] = "statut → {$data['statut']}";
        }

        if (array_key_exists('montant', $data) && $data['montant'] !== null) {
            $updates['montant'] = (int) $data['montant'];
            $messagesModifies[] = 'montant → ' . number_format((int) $data['montant'], 0, ',', ' ') . ' FCFA';
        }

        if (count($updates) === 1) {
            return redirect()->back()->with('status', 'Rien à mettre à jour.');
        }

        DB::transaction(fn () => $reservation->update($updates));

        return redirect()
            ->back()
            ->with('status', "Réservation #{$reservation->id} mise à jour : " . implode(', ', $messagesModifies) . '.');
    }

    public function destroy(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('annuler', $reservation);

        DB::transaction(fn () => $reservation->update([
            'statut'               => 'annule',
            'modifie_manuellement' => true,
        ]));

        return redirect()
            ->route('reservations.index')
            ->with('status', "Réservation #{$reservation->id} annulée.");
    }

    public function uploadDocument(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('gererDocument', $reservation);

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Verrou transactionnel : empêche deux uploads concurrents de valider le même créneau.
        $erreur = DB::transaction(function () use ($request, $reservation) {
            if (! $this->verifierCapacite($reservation, lock: true)) {
                $option = $reservation->option ?? OptionReservation::find($reservation->option_id);

                return ((int) $option->capacite <= 1)
                    ? 'Ce créneau est déjà pris par une autre réservation confirmée (payée ou en cours). Impossible de téléverser une preuve : contactez le client pour proposer un autre horaire.'
                    : "La capacité de {$option->capacite} places est atteinte pour ce créneau (réservations confirmées). Impossible de téléverser une preuve : contactez le client pour proposer un autre horaire.";
            }

            if ($reservation->chemin_document_justificatif && Storage::disk('local')->exists($reservation->chemin_document_justificatif)) {
                Storage::disk('local')->delete($reservation->chemin_document_justificatif);
            }

            $ext  = $request->file('document')->getClientOriginalExtension();
            $path = "documents_justificatifs/res_{$reservation->id}_" . time() . ".{$ext}";
            Storage::disk('local')->put($path, file_get_contents($request->file('document')->getRealPath()));

            $reservation->update([
                'chemin_document_justificatif' => $path,
                'date_validation_preuve'       => null,
            ]);

            return null;
        });

        if ($erreur !== null) {
            return redirect()->route('reservations.show', $reservation)->withErrors(['document' => $erreur]);
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('status', 'Document téléversé. Il doit maintenant être validé pour faire passer la réservation en « Déjà payé ».');
    }

    public function validerDocument(Reservation $reservation): RedirectResponse
    {
        $this->authorize('gererDocument', $reservation);

        if (! $reservation->chemin_document_justificatif) {
            return redirect()
                ->route('reservations.show', $reservation)
                ->with('status', 'Aucun document téléversé — validation impossible.');
        }

        $message = DB::transaction(function () use ($reservation) {
            $capaciteOK = $this->verifierCapacite($reservation, lock: true);

            $reservation->update([
                'date_validation_preuve' => now(),
                'statut'                 => $capaciteOK ? 'deja_paye' : 'liste_attente',
                'modifie_manuellement'   => false,
            ]);

            return $capaciteOK
                ? "Document validé. Réservation #{$reservation->id} basculée en « Déjà payé »."
                : "Document validé mais capacité insuffisante pour ce créneau. Reste en liste d'attente. Contactez le client pour proposer un autre horaire.";
        });

        return redirect()->route('reservations.show', $reservation)->with('status', $message);
    }

    public function telechargerDocument(Reservation $reservation): StreamedResponse
    {
        abort_unless($reservation->chemin_document_justificatif, 404);
        abort_unless(Storage::disk('local')->exists($reservation->chemin_document_justificatif), 404);

        return Storage::disk('local')->download(
            $reservation->chemin_document_justificatif,
            "reservation_{$reservation->id}_justificatif." . pathinfo($reservation->chemin_document_justificatif, PATHINFO_EXTENSION)
        );
    }

    /**
     * Vérifie la disponibilité du créneau en tenant compte des dates (multi-jours),
     * des horaires (plage_horaire mono-jour) et de la capacité (exclusive vs partagée).
     * Ne comptent que les réservations confirmées (deja_paye / en_cours).
     *
     * @param bool $lock  Applique un verrou SELECT ... FOR UPDATE (à utiliser dans une transaction).
     */
    protected function verifierCapacite(Reservation $reservation, bool $lock = false): bool
    {
        $option = $reservation->option ?? OptionReservation::find($reservation->option_id);

        $debut = $reservation->date_reservation->toDateString();
        $fin   = $reservation->dateFinEffective()->toDateString();

        $query = Reservation::query()
            ->where('option_id', $option->id)
            ->where('id', '!=', $reservation->id)
            ->whereIn('statut', ['deja_paye', 'en_cours'])
            // Chevauchement de plages de dates : (debutA <= finB) ET (finA >= debutB)
            ->where('date_reservation', '<=', $fin)
            ->whereRaw('COALESCE(date_fin, date_reservation) >= ?', [$debut])
            ->where(function ($q) use ($reservation) {
                // Réservation à la journée (ou multi-jours) : tout chevauchement de dates compte.
                if ($reservation->type_creneau === 'journee') {
                    return;
                }

                // Réservation sur plage horaire (mono-jour garanti) :
                // bloquée par une réservation journée, ou par une plage qui chevauche l'horaire.
                $q->where('type_creneau', 'journee')
                  ->orWhere(function ($qq) use ($reservation) {
                      $qq->where('type_creneau', 'plage_horaire')
                         ->where('heure_debut', '<', $reservation->heure_fin)
                         ->where('heure_fin', '>', $reservation->heure_debut);
                  });
            });

        if ($lock) {
            $query->lockForUpdate();
        }

        $autres = $query->get();

        if ((int) $option->capacite <= 1) {
            return $autres->isEmpty();
        }

        $dejaOccupees = (int) $autres->sum('nombre_personnes');

        return ($dejaOccupees + (int) $reservation->nombre_personnes) <= (int) $option->capacite;
    }
}

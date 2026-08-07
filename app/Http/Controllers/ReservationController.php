<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Compartiment;
use App\Models\OptionReservation;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id'         => ['required', 'exists:clients,id'],
            'option_id'         => ['required', 'exists:options_reservation,id'],
            'date_reservation'  => ['required', 'date', 'after_or_equal:today'],
            'type_creneau'      => ['required', 'in:journee,plage_horaire'],
            'heure_debut'       => ['nullable', 'required_if:type_creneau,plage_horaire', 'date_format:H:i'],
            'heure_fin'         => ['nullable', 'required_if:type_creneau,plage_horaire', 'date_format:H:i', 'after:heure_debut'],
            'nombre_personnes'  => ['required', 'integer', 'min:1', 'max:500'],
            'montant'           => ['required', 'integer', 'min:0'],
        ]);

        $reservation = Reservation::create([
            'client_id'            => $data['client_id'],
            'utilisateur_id'       => $request->user()->id,
            'option_id'            => $data['option_id'],
            'date_reservation'     => $data['date_reservation'],
            'type_creneau'         => $data['type_creneau'],
            'heure_debut'          => $data['heure_debut'] ?? null,
            'heure_fin'            => $data['heure_fin'] ?? null,
            'nombre_personnes'     => $data['nombre_personnes'],
            'montant'              => $data['montant'],
            'statut'               => 'liste_attente',
            'modifie_manuellement' => false,
        ]);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('status', "Réservation #{$reservation->id} créée (statut : liste d'attente). Téléversez le document justificatif pour valider.");
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['client', 'utilisateur', 'option.compartiment']);

        return view('reservations.show', compact('reservation'));
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'statut'  => ['nullable', 'in:liste_attente,en_cours,termine,annule'],
            'montant' => ['nullable', 'integer', 'min:0'],
        ]);

        $updates          = ['modifie_manuellement' => true];
        $messagesModifies = [];

        if (! empty($data['statut'])) {
            $documentValide = ! is_null($reservation->date_validation_preuve);
            $statutsAutorises = $documentValide
                ? ['liste_attente', 'en_cours', 'termine', 'annule']
                : ['liste_attente', 'annule'];

            if (! in_array($data['statut'], $statutsAutorises, true)) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'statut' => $documentValide
                            ? "Statut non autorisé."
                            : "Tant que le document justificatif n'est pas validé, seuls « Liste d'attente » et « Annulé » peuvent être choisis manuellement.",
                    ]);
            }

            if ($data['statut'] === 'annule' && ! $request->user()->isAdministrateur()) {
                abort(403, 'Seul un administrateur peut annuler une réservation.');
            }

            $updates['statut']  = $data['statut'];
            $messagesModifies[] = "statut → {$data['statut']}";
        }

        if (array_key_exists('montant', $data) && $data['montant'] !== null) {
            $updates['montant'] = (int) $data['montant'];
            $messagesModifies[] = "montant → " . number_format($data['montant'], 0, ',', ' ') . " FCFA";
        }

        if (count($updates) === 1) {
            return redirect()->back()->with('status', 'Rien à mettre à jour.');
        }

        $reservation->update($updates);

        return redirect()
            ->back()
            ->with('status', "Réservation #{$reservation->id} mise à jour : " . implode(', ', $messagesModifies) . '.');
    }

    public function destroy(Request $request, Reservation $reservation): RedirectResponse
    {
        if (! $request->user()->isAdministrateur()) {
            abort(403, 'Seul un administrateur peut annuler une réservation.');
        }

        $reservation->update([
            'statut'               => 'annule',
            'modifie_manuellement' => true,
        ]);

        return redirect()
            ->route('reservations.index')
            ->with('status', "Réservation #{$reservation->id} annulée.");
    }

    public function uploadDocument(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($reservation->statut === 'annule') {
            return redirect()
                ->route('reservations.show', $reservation)
                ->withErrors(['document' => 'Impossible de téléverser un document sur une réservation annulée.']);
        }

        if (! $this->verifierCapacite($reservation)) {
            $option = $reservation->option ?? OptionReservation::find($reservation->option_id);
            $message = ((int) $option->capacite <= 1)
                ? "Ce créneau est déjà pris par une autre réservation confirmée (payée ou en cours). Impossible de téléverser une preuve : contactez le client pour proposer un autre horaire."
                : "La capacité de {$option->capacite} places est atteinte pour ce créneau (réservations confirmées). Impossible de téléverser une preuve : contactez le client pour proposer un autre horaire.";

            return redirect()
                ->route('reservations.show', $reservation)
                ->withErrors(['document' => $message]);
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

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('status', 'Document téléversé. Il doit maintenant être validé pour faire passer la réservation en « Déjà payé ».');
    }

    public function validerDocument(Reservation $reservation): RedirectResponse
    {
        if (! $reservation->chemin_document_justificatif) {
            return redirect()
                ->route('reservations.show', $reservation)
                ->with('status', 'Aucun document téléversé — validation impossible.');
        }

        $capaciteOK = $this->verifierCapacite($reservation);

        $reservation->update([
            'date_validation_preuve' => now(),
            'statut'                 => $capaciteOK ? 'deja_paye' : 'liste_attente',
            'modifie_manuellement'   => false,
        ]);

        $message = $capaciteOK
            ? "Document validé. Réservation #{$reservation->id} basculée en « Déjà payé »."
            : "Document validé mais capacité insuffisante pour ce créneau. Reste en liste d'attente. Contactez le client pour proposer un autre horaire.";

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('status', $message);
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

    protected function verifierCapacite(Reservation $reservation): bool
    {
        $option = $reservation->option ?? OptionReservation::find($reservation->option_id);

        $autresQuiChevauchent = Reservation::query()
            ->where('option_id', $option->id)
            ->where('date_reservation', $reservation->date_reservation)
            ->where('id', '!=', $reservation->id)
            ->whereIn('statut', ['deja_paye', 'en_cours'])
            ->where(function ($q) use ($reservation) {
                // Cas 1 : la réservation en cours est sur la journée entière → toute autre réservation du même jour compte
                if ($reservation->type_creneau === 'journee') {
                    return;
                }

                // Cas 2 : la réservation est sur plage horaire → on ne bloque que celles qui chevauchent
                $q->where(function ($qq) use ($reservation) {
                    // Une autre réservation en journee bloque toujours
                    $qq->where('type_creneau', 'journee')
                       ->orWhere(function ($qqq) use ($reservation) {
                           // Deux plages se chevauchent si (debutA < finB) ET (finA > debutB)
                           $qqq->where('type_creneau', 'plage_horaire')
                               ->where('heure_debut', '<', $reservation->heure_fin)
                               ->where('heure_fin', '>', $reservation->heure_debut);
                       });
                });
            });

        if ((int) $option->capacite <= 1) {
            return ! $autresQuiChevauchent->exists();
        }

        $dejaOccupees = (int) $autresQuiChevauchent->sum('nombre_personnes');

        return ($dejaOccupees + (int) $reservation->nombre_personnes) <= (int) $option->capacite;
    }
}

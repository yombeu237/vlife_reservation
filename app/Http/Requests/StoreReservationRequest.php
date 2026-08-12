<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tout utilisateur authentifié (employé ou admin) peut créer une réservation.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'client_id'        => ['required', 'integer', 'exists:clients,id'],
            'option_id'        => ['required', 'integer', 'exists:options_reservation,id'],
            'date_reservation' => ['required', 'date', 'after_or_equal:today'],
            'date_fin'         => ['nullable', 'date', 'after_or_equal:date_reservation'],
            'type_creneau'     => ['required', 'in:journee,plage_horaire'],
            'heure_debut'      => ['nullable', 'required_if:type_creneau,plage_horaire', 'date_format:H:i'],
            'heure_fin'        => ['nullable', 'required_if:type_creneau,plage_horaire', 'date_format:H:i', 'after:heure_debut'],
            'nombre_personnes' => ['required', 'integer', 'min:1', 'max:500'],
            'montant'          => ['required', 'integer', 'min:0', 'max:100000000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Règle métier : une plage horaire précise n'a de sens que sur un seul jour.
            $debut = $this->input('date_reservation');
            $fin   = $this->input('date_fin');

            if ($this->input('type_creneau') === 'plage_horaire'
                && $fin
                && $debut
                && $fin !== $debut) {
                $validator->errors()->add(
                    'type_creneau',
                    "Une plage horaire précise ne peut concerner qu'un seul jour. Pour plusieurs jours, choisissez « Journée entière »."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'date_reservation.after_or_equal' => 'La date de début ne peut pas être dans le passé.',
            'date_fin.after_or_equal'         => 'La date de fin doit être postérieure ou égale à la date de début.',
            'heure_fin.after'                 => "L'heure de fin doit être postérieure à l'heure de début.",
        ];
    }
}

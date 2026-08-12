<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'statut'  => ['nullable', 'in:liste_attente,en_cours,termine,annule'],
            'montant' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ];
    }
}

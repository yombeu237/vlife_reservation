<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id',
    'utilisateur_id',
    'option_id',
    'date_reservation',
    'type_creneau',
    'heure_debut',
    'heure_fin',
    'nombre_personnes',
    'montant',
    'statut',
    'modifie_manuellement',
    'chemin_document_justificatif',
    'date_validation_preuve',
])]
class Reservation extends Model
{
    protected function casts(): array
    {
        return [
            'date_reservation'       => 'date',
            'date_validation_preuve' => 'datetime',
            'modifie_manuellement'   => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(OptionReservation::class, 'option_id');
    }
}

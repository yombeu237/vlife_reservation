<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'utilisateur_id',
    'reservation_id',
    'type_attendu',
    'type_fourni',
    'code_raison',
    'raison',
    'date_document',
    'montant_document',
    'numero_facture',
])]
class DocumentRejection extends Model
{
    protected function casts(): array
    {
        return [
            'date_document' => 'date',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id',
    'utilisateur_id',
    'option_id',
    'date_reservation',
    'date_fin',
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
    /** Statuts considérés comme un gain effectif (client ayant payé). */
    public const STATUTS_PAYES = ['deja_paye', 'en_cours', 'termine'];

    /** Statuts comptabilisés dans le nombre total de réservations (tout sauf annulé). */
    public const STATUTS_COMPTABILISES = ['liste_attente', 'deja_paye', 'en_cours', 'termine'];

    protected function casts(): array
    {
        return [
            'date_reservation'       => 'date',
            'date_fin'               => 'date',
            'date_validation_preuve' => 'datetime',
            'modifie_manuellement'   => 'boolean',
        ];
    }

    // === Relations ===

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

    // === Scopes ===

    /** Réservations effectivement payées (comptent dans les gains). */
    public function scopePayees(Builder $query): Builder
    {
        return $query->whereIn('statut', self::STATUTS_PAYES);
    }

    /** Réservations comptabilisées (tout sauf annulé). */
    public function scopeNonAnnulees(Builder $query): Builder
    {
        return $query->whereIn('statut', self::STATUTS_COMPTABILISES);
    }

    // === Helpers ===

    /** Date de fin effective (= date de début si mono-jour). */
    public function dateFinEffective(): \Illuminate\Support\Carbon
    {
        return $this->date_fin ?? $this->date_reservation;
    }

    /** Nombre de jours couverts par la réservation (au moins 1). */
    public function nombreJours(): int
    {
        if (! $this->date_reservation) {
            return 1;
        }

        return max(1, $this->date_reservation->diffInDays($this->dateFinEffective()) + 1);
    }

    public function estMultiJours(): bool
    {
        return $this->nombreJours() > 1;
    }

    public function estPayee(): bool
    {
        return in_array($this->statut, self::STATUTS_PAYES, true);
    }
}

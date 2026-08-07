<?php

namespace App\Models;

use App\Services\Calculs\CalculateurTarif;
use App\Services\Calculs\CalculFixe;
use App\Services\Calculs\CalculLibre;
use App\Services\Calculs\CalculParPersonne;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['compartiment_id', 'libelle', 'tarif', 'regle_description', 'capacite', 'type_calcul'])]
class OptionReservation extends Model
{
    protected $table = 'options_reservation';

    public function compartiment(): BelongsTo
    {
        return $this->belongsTo(Compartiment::class, 'compartiment_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'option_id');
    }

    public function calculerMontant(int $nombrePersonnes): int
    {
        return $this->calculateur()->calculer($this, $nombrePersonnes);
    }

    protected function calculateur(): CalculateurTarif
    {
        return match ($this->type_calcul) {
            'par_personne' => new CalculParPersonne(),
            'fixe'         => new CalculFixe(),
            'libre'        => new CalculLibre(),
            default        => throw new \DomainException("type_calcul inconnu : {$this->type_calcul}"),
        };
    }
}

<?php

namespace App\Services\Calculs;

use App\Models\OptionReservation;

class CalculFixe implements CalculateurTarif
{
    public function calculer(OptionReservation $option, int $nombrePersonnes): int
    {
        return (int) $option->tarif;
    }
}

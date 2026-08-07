<?php

namespace App\Services\Calculs;

use App\Models\OptionReservation;

class CalculParPersonne implements CalculateurTarif
{
    public function calculer(OptionReservation $option, int $nombrePersonnes): int
    {
        return max(1, $nombrePersonnes) * (int) $option->tarif;
    }
}

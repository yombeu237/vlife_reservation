<?php

namespace App\Services\Calculs;

use App\Models\OptionReservation;

class CalculLibre implements CalculateurTarif
{
    public function calculer(OptionReservation $option, int $nombrePersonnes): int
    {
        return 0;
    }
}

<?php

namespace App\Services\Calculs;

use App\Models\OptionReservation;

interface CalculateurTarif
{
    public function calculer(OptionReservation $option, int $nombrePersonnes): int;
}

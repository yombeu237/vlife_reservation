<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nom'])]
class Compartiment extends Model
{
    public function optionsReservation(): HasMany
    {
        return $this->hasMany(OptionReservation::class, 'compartiment_id');
    }
}

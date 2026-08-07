<?php

namespace Tests\Unit;

use App\Models\OptionReservation;
use App\Services\Calculs\CalculFixe;
use App\Services\Calculs\CalculLibre;
use App\Services\Calculs\CalculParPersonne;
use PHPUnit\Framework\TestCase;

class CalculateurTarifTest extends TestCase
{
    private function fakeOption(int $tarif, string $typeCalcul): OptionReservation
    {
        $option = new OptionReservation();
        $option->tarif = $tarif;
        $option->type_calcul = $typeCalcul;

        return $option;
    }

    public function test_calcul_par_personne_multiplie_le_tarif_par_le_nombre(): void
    {
        $calc   = new CalculParPersonne();
        $option = $this->fakeOption(3000, 'par_personne');

        $this->assertSame(9000, $calc->calculer($option, 3));
        $this->assertSame(3000, $calc->calculer($option, 1));
        $this->assertSame(15000, $calc->calculer($option, 5));
    }

    public function test_calcul_par_personne_traite_zero_comme_une_personne(): void
    {
        $calc   = new CalculParPersonne();
        $option = $this->fakeOption(5000, 'par_personne');

        $this->assertSame(5000, $calc->calculer($option, 0));
    }

    public function test_calcul_fixe_ignore_le_nombre_de_personnes(): void
    {
        $calc   = new CalculFixe();
        $option = $this->fakeOption(10000, 'fixe');

        $this->assertSame(10000, $calc->calculer($option, 1));
        $this->assertSame(10000, $calc->calculer($option, 50));
        $this->assertSame(10000, $calc->calculer($option, 200));
    }

    public function test_calcul_libre_retourne_toujours_zero(): void
    {
        $calc   = new CalculLibre();
        $option = $this->fakeOption(999999, 'libre');

        $this->assertSame(0, $calc->calculer($option, 1));
        $this->assertSame(0, $calc->calculer($option, 100));
    }
}

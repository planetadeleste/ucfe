<?php

namespace PlanetaDelEste\Ucfe\Cfe\Referencia;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $FechaCFEref
 * @property string $IndGlobal
 * @property string $NroCFERef
 * @property string $NroLinRef
 * @property string $RazonRef
 * @property string $Serie
 * @property string $TpoDocRef
 */
class Referencia
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroLinRef',
            'IndGlobal',
            'TpoDocRef',
            'Serie',
            'NroCFERef',
            'RazonRef',
            'FechaCFEref',
        ];
    }
}

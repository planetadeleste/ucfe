<?php

namespace PlanetaDelEste\Ucfe\Cfe\MediosPago;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $CodMP
 * @property string $GlosaMP
 * @property string $NroLinMP
 * @property string $OrdenMP
 * @property string $ValorPago
 */
class MedioPago extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroLinMP',
            'CodMP',
            'GlosaMP',
            'OrdenMP',
            'ValorPago',
        ];
    }
}

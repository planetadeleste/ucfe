<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $CodRet
 * @property string $ValRetPerc
 */
class RetencPercep
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['CodRet', 'ValRetPerc'];
    }
}

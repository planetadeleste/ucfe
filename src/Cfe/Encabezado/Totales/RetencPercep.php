<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $CodRet
 * @property string $ValRetPerc
 */
class RetencPercep extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['CodRet', 'ValRetPerc'];
    }
}

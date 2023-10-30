<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $DescTipo Indica si esta en 1 = $ o 2 = %
 * @property string $DescVal
 */
class SubDescuento extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['DescTipo', 'DescVal'];
    }
}

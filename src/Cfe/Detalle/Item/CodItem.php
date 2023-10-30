<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $TpoCod
 * @property string $Cod
 */
class CodItem extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['TpoCod', 'Cod'];
    }
}

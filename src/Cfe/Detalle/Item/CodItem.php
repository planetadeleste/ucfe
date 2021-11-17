<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $TpoCod
 * @property string $Cod
 */
class CodItem
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['TpoCod', 'Cod'];
    }
}

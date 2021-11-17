<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $DescTipo
 * @property string $DescVal
 */
class SubDescuento
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['DescTipo', 'DescVal'];
    }
}

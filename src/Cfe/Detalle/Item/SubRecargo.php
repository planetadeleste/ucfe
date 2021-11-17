<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $RecargoTipo
 * @property string $RecargoVal
 */
class SubRecargo
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['RecargoTipo', 'RecargoVal'];
    }
}

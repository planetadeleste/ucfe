<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $RecargoTipo
 * @property string $RecargoVal
 */
class SubRecargo extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['RecargoTipo', 'RecargoVal'];
    }
}

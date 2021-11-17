<?php

namespace PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $CodDR
 * @property string $GlosaDR
 * @property string $IndFactDR
 * @property string $NroLinDR
 * @property string $TpoDR
 * @property string $TpoMovDR
 * @property string $ValorDR
 */
class DRG_Item
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroLinDR',
            'TpoMovDR',
            'TpoDR',
            'CodDR',
            'GlosaDR',
            'ValorDR',
            'IndFactDR',
        ];
    }
}

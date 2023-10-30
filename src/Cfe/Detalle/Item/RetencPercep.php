<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $CodRet
 * @property string $InfoAdicionalRet
 * @property string $MntSujetoaRet
 * @property string $Tasa
 * @property string $ValRetPerc
 */
class RetencPercep extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'CodRet',
            'Tasa',
            'MntSujetoaRet',
            'InfoAdicionalRet',
            'ValRetPerc',
        ];
    }
}

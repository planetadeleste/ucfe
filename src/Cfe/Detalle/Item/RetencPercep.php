<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $CodRet
 * @property string $InfoAdicionalRet
 * @property string $MntSujetoaRet
 * @property string $Tasa
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
        return [
            'CodRet',
            'Tasa',
            'MntSujetoaRet',
            'InfoAdicionalRet',
            'ValRetPerc',
        ];
    }
}

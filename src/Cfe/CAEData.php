<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $CAEEspecial
 * @property string $CAE_ID
 * @property string $CausalCAEEsp
 * @property string $DNro
 * @property string $FecVenc
 * @property string $HNro
 */
class CAEData
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'CAE_ID',
            'DNro',
            'HNro',
            'FecVenc',
            'CAEEspecial',
            'CausalCAEEsp',
        ];
    }
}

<?php

namespace PlanetaDelEste\Ucfe\Cfe\SubTotInfo;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $GlosaSTI
 * @property string $NroSTI
 * @property string $OrdenSTI
 * @property string $ValSubtotSTI
 */
class STI_Item
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroSTI',
            'GlosaSTI',
            'OrdenSTI',
            'ValSubtotSTI',
        ];
    }
}

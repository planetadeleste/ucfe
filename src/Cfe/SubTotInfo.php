<?php namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property \PlanetaDelEste\Ucfe\Cfe\SubTotInfo\STI_Item $STI_Item
 */
class SubTotInfo
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['STI_Item'];
    }
}

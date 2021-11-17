<?php namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property \PlanetaDelEste\Ucfe\Cfe\Detalle\Item[] $item
 */
class Detalle
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['Item'];
    }
}

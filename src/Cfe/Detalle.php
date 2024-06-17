<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

/**
 * @property array<Item> $Item
 */
class Detalle extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['Item'];
    }

    public function setItemAttribute(array $sValue = []): void
    {
        $this->setAttributeList($sValue, 'Item', Item::class);
    }
}

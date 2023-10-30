<?php namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\Detalle\Item;

/**
 * @property Item[] $item
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

    public function setItemAttribute(array $sValue = [])
    {
        $this->setAttributeList($sValue, 'Item', Item::class);
    }
}

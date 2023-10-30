<?php namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\SubTotInfo\STI_Item;

/**
 * @property STI_Item $STI_Item
 */
class SubTotInfo extends CfeItemBase
{
    protected array $arRelationList = [
        'STI_Item' => STI_Item::class,
    ];

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['STI_Item'];
    }
}

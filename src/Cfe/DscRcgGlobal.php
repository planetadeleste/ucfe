<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal\DRG_Item;

/**
 * @property DRG_Item[] $DRG_Item Descuentos y/o Recargos que aumentan o
 *           disminuyen la base del impuesto
 */
class DscRcgGlobal extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['DRG_Item'];
    }

    public function setDRGItemAttribute(array $sValue = [])
    {
        $this->setAttributeList($sValue, 'DRG_Item', DRG_Item::class);
    }
}

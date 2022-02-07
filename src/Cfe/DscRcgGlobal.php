<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property \PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal\DRG_Item[] $DRG_Item Descuentos y/o Recargos que aumentan o disminuyen la base del impuesto
 */
class DscRcgGlobal
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['DRG_Item'];
    }
}

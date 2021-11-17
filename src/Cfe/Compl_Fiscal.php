<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property \PlanetaDelEste\Ucfe\Cfe\Compl_Fiscal\Compl_Fiscal_Data $Compl_Fiscal_Data
 * @property string                                                  $EncryptedData
 */
class Compl_Fiscal
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['Compl_Fiscal_Data', 'EncryptedData'];
    }
}

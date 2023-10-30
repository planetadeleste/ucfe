<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\Compl_Fiscal\Compl_Fiscal_Data;

/**
 * @property Compl_Fiscal_Data $Compl_Fiscal_Data
 * @property string            $EncryptedData
 */
class Compl_Fiscal extends CfeItemBase
{
    protected array $arRelationList = [
        'Compl_Fiscal_Data' => Compl_Fiscal_Data::class,
    ];

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['Compl_Fiscal_Data', 'EncryptedData'];
    }
}

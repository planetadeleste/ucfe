<?php

namespace PlanetaDelEste\Ucfe\Cfe\Compl_Fiscal;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $DocMdte
 * @property string $NombreMdte
 * @property string $Pais
 * @property string $RUCEmisor
 * @property string $TipoDocMdte
 */
class Compl_Fiscal_Data
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'RUCEmisor',
            'TipoDocMdte',
            'Pais',
            'DocMdte',
            'NombreMdte',
        ];
    }
}

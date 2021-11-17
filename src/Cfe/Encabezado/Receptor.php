<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $CP
 * @property string $CiudadRecep
 * @property string $CodPaisRecep
 * @property string $CompraID
 * @property string $DeptoRecep
 * @property string $DirRecep
 * @property string $DocRecep
 * @property string $DocRecepExt
 * @property string $InfoAdicional
 * @property string $LugarDestEnt
 * @property string $PaisRecep
 * @property string $RznSocRecep
 * @property string $TipoDocRecep
 */
class Receptor
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'TipoDocRecep',
            'CodPaisRecep',
            'DocRecepExt',
            'DocRecep',
            'RznSocRecep',
            'DirRecep',
            'CiudadRecep',
            'DeptoRecep',
            'PaisRecep',
            'CP',
            'InfoAdicional',
            'LugarDestEnt',
            'CompraID',
        ];
    }
}

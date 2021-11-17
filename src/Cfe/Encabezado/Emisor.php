<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $CdgDGISucur
 * @property string $Ciudad
 * @property string $CorreoEmisor
 * @property string $Departamento
 * @property string $DomFiscal
 * @property string $EmiSucursal
 * @property string $GiroEmis
 * @property string $InfoAdicionalEmisor
 * @property string $NomComercial
 * @property string $RUCEmisor
 * @property string $RznSoc
 * @property string $Telefono
 */
class Emisor
{
    use HasAttributeTrait;


    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'RUCEmisor',
            'RznSoc',
            'NomComercial',
            'GiroEmis',
            'Telefono',
            'CorreoEmisor',
            'EmiSucursal',
            'CdgDGISucur',
            'DomFiscal',
            'Ciudad',
            'Departamento',
            'InfoAdicionalEmisor',
        ];
    }
}

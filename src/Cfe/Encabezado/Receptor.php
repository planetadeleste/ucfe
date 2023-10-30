<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $CP           Codigo Postal
 * @property string $CiudadRecep  Ciudad
 * @property string $CodPaisRecep Código de Pais según ISO 3166-1 alfa-2
 * @property string $CompraID
 * @property string $DeptoRecep   Departamento
 * @property string $DirRecep     Domicilio del Receptor
 * @property string $DocRecep     Nro de Doc. del Receptor
 * @property string $DocRecepExt
 * @property string $InfoAdicional
 * @property string $LugarDestEnt
 * @property string $PaisRecep    País
 * @property string $RznSocRecep  Nombre o Razon Social del Recepto
 * @property string $TipoDocRecep Tipo de Documento del Receptor (1: NIE, 2: RUC, 3: CI, 4:otro, 5 pasaporte, 6 DNI, 7
 *           NIFE)
 */
class Receptor extends CfeItemBase
{
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

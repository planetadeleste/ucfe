<?php

namespace PlanetaDelEste\Ucfe\Cfe\Referencia;

use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property int    $NroLinRef Numero Secuencial de Linea
 * @property int    $IndGlobal Indica que se esta Referenciando un Conjunto de Documentos (1)
 * @property int    $TpoDocRef Tipo de Documento de Referencia ( CFE o CFC)
 * @property string $Serie     Serie asignada al CFE
 * @property string $NroCFERef Nro del CFE de Referencia
 * @property string $RazonRef  Razon Explicita por la que se Referencia el Documento
 * @property string $FechaCFEref
 */
class Referencia extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroLinRef',
            'IndGlobal',
            'TpoDocRef',
            'Serie',
            'NroCFERef',
            'RazonRef',
            'FechaCFEref',
        ];
    }
}

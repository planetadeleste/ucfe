<?php namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\Encabezado\Emisor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\IdDoc;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Receptor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales;

/**
 * @property Emisor   $Emisor
 * @property IdDoc    $IdDoc
 * @property Receptor $Receptor
 * @property Totales  $Totales
 */
class Encabezado extends CfeItemBase
{
    protected array $arRelationList = [
        'Emisor'   => Emisor::class,
        'IdDoc'    => IdDoc::class,
        'Receptor' => Receptor::class,
        'Totales'  => Totales::class,
    ];

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'IdDoc',
            'Emisor',
            'Receptor',
            'Totales',
        ];
    }
}

<?php namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property \PlanetaDelEste\Ucfe\Cfe\Encabezado\Emisor   $Emisor
 * @property \PlanetaDelEste\Ucfe\Cfe\Encabezado\IdDoc    $IdDoc
 * @property \PlanetaDelEste\Ucfe\Cfe\Encabezado\Receptor $Receptor
 * @property \PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales  $Totales
 */
class Encabezado
{
    use HasAttributeTrait;

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

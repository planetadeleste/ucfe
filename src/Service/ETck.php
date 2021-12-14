<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Traits\CfeTrait;

class ETck extends CfeClient
{
    use CfeTrait;

    /**
     * Get CFE definition type
     *
     * @return string eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     */
    public function getType(): string
    {
        return 'eTck';
    }

    public function getTipoCFE(): int
    {
        return 101;
    }

    public function getRules(): array
    {
        return [
            'Encabezado.IdDoc'   => 'required',
            'Encabezado.Emisor'  => 'required',
            'Encabezado.Totales' => 'required',
        ];
    }
}

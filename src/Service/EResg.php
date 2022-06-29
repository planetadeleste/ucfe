<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Traits\CfeTrait;

class EResg extends CfeClient
{
    use CfeTrait;

    /**
     * Get CFE definition type
     *
     * @return string eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     */
    public function getType(): string
    {
        return 'eResg';
    }

    public function getTipoCFE(): int
    {
        return 182;
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

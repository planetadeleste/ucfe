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
        return Factory::TYPE_ETCK;
    }

    public function getTipoCFE(): int
    {
        if ($this->noteType === self::CFE_CREDIT_NOTE) {
            return 102;
        }

        if ($this->noteType === self::CFE_DEBIT_NOTE) {
            return 103;
        }

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

<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Traits\CfeTrait;

class EFactExp extends CfeClient
{
    use CfeTrait;

    /**
     * Get CFE definition type
     *
     * @return string eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     */
    public function getType(): string
    {
        return 'eFact_Exp';
    }

    /**
     * @inheritDoc
     */
    public function getTipoCFE(): int
    {
        return 121;
    }
}

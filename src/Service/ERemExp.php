<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Traits\CfeTrait;

class ERemExp extends CfeClient
{
    use CfeTrait;

    /**
     * Get CFE definition type
     *
     * @return string eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     */
    public function getType(): string
    {
        return Factory::TYPE_EREM_EXP;
    }

    /**
     * @inheritDoc
     */
    public function getTipoCFE(): int
    {
        return 124;
    }
}

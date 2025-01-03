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
        return Factory::TYPE_EFACT_EXP;
    }

    /**
     * @inheritDoc
     */
    public function getTipoCFE(): int
    {
        if ($this->noteType === self::CFE_CREDIT_NOTE) {
            return 122;
        }

        if ($this->noteType === self::CFE_DEBIT_NOTE) {
            return 123;
        }

        return 121;
    }
}

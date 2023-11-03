<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Traits\CfeTrait;

class EFact extends CfeClient
{
    use CfeTrait;

    /**
     * Get CFE definition type
     *
     * @return string eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     */
    public function getType(): string
    {
        return Factory::TYPE_EFACT;
    }

    /**
     * @inheritDoc
     */
    public function getTipoCFE(): int
    {
        if ($this->noteType === self::CFE_CREDIT_NOTE) {
            return 112;
        }

        if ($this->noteType === self::CFE_DEBIT_NOTE) {
            return 113;
        }

        return 111;
    }
}

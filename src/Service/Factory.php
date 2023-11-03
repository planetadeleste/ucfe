<?php

namespace PlanetaDelEste\Ucfe\Service;

use Str;

class Factory
{
    public const TYPE_ETCK      = 'eTck';
    public const TYPE_EFACT     = 'eFact';
    public const TYPE_EFACT_EXP = 'eFact_Exp';
    public const TYPE_EREM      = 'eRem';
    public const TYPE_EREM_EXP  = 'eRem_Exp';
    public const TYPE_ERESG     = 'eResg';
    public const TYPE_EBOLETA   = 'eBoleta';

    /**
     * @param string $sType eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     *
     * @return EBoleta|EFact|EFactExp|ERem|ERemExp|ETck|EResg
     */
    public static function make(string $sType)
    {
        $sClass = __NAMESPACE__ . '\\' . Str::studly($sType);
        gwclock($sType, $sClass);

        return new $sClass();
    }
}

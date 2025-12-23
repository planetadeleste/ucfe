<?php

namespace PlanetaDelEste\Ucfe\Service;

use Str;

class Factory
{
    public const string TYPE_ETCK      = 'eTck';
    public const string TYPE_EFACT     = 'eFact';
    public const string TYPE_EFACT_EXP = 'eFact_Exp';
    public const string TYPE_EREM      = 'eRem';
    public const string TYPE_EREM_EXP  = 'eRem_Exp';
    public const string TYPE_ERESG     = 'eResg';
    public const string TYPE_EBOLETA   = 'eBoleta';

    /**
     * @param string $sType eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     *
     * @return CfeClient|mixed
     */
    public static function make(string $sType)
    {
        $sClass = __NAMESPACE__.'\\'.Str::studly($sType);

        return new $sClass();
    }
}

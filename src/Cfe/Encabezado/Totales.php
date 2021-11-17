<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string                                                   $CantLinDet
 * @property string                                                   $IVATasaBasica
 * @property string                                                   $IVATasaMin
 * @property string                                                   $MntExpoyAsim
 * @property string                                                   $MntIVAOtra
 * @property string                                                   $MntIVATasaBasica
 * @property string                                                   $MntIVATasaMin
 * @property string                                                   $MntIVaenSusp
 * @property string                                                   $MntImpuestoPerc
 * @property string                                                   $MntNetoIVAOtra
 * @property string                                                   $MntNetoIVATasaBasica
 * @property string                                                   $MntNetoIvaTasaMin
 * @property string                                                   $MntNoGrv
 * @property string                                                   $MntPagar
 * @property string                                                   $MntTotCredFisc
 * @property string                                                   $MntTotRetenido
 * @property string                                                   $MntTotal
 * @property string                                                   $MontoNF
 * @property \PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales\RetencPercep $RetencPercep
 * @property string                                                   $TpoCambio
 * @property string                                                   $TpoMoneda
 */
class Totales
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'TpoMoneda',
            'TpoCambio',
            'MntNoGrv',
            'MntExpoyAsim',
            'MntImpuestoPerc',
            'MntIVaenSusp',
            'MntNetoIvaTasaMin',
            'MntNetoIVATasaBasica',
            'MntNetoIVAOtra',
            'IVATasaMin',
            'IVATasaBasica',
            'MntIVATasaMin',
            'MntIVATasaBasica',
            'MntIVAOtra',
            'MntTotal',
            'MntTotRetenido',
            'MntTotCredFisc',
            'CantLinDet',
            'RetencPercep',
            'MontoNF',
            'MntPagar',
        ];
    }
}

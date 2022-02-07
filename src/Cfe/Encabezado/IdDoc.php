<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado;

use Illuminate\Validation\Rule;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $ClauVenta
 * @property string $CodPaisProp
 * @property string $DocProp
 * @property string $DocPropExt
 * @property string $FchEmis      Date format YYYY-MM-DD
 * @property string $FchValor
 * @property string $FchVenc      Date format YYYY-MM-DD
 * @property int    $FmaPago      Values: 1 (Contado) | 2 (Credito)
 * @property string $IVAalDia
 * @property string $IndCobPropia
 * @property string $IndCompraMEReventa
 * @property string $IndPagCta3ros
 * @property string $IndPropiedad
 * @property string $InfoAdicionalDoc
 * @property int    $MntBruto     Values: 1 | 2 | 3
 * @property string $ModVenta
 * @property int    $Nro
 * @property string $NroInterno
 * @property string $PeriodoDesde Date format YYYY-MM-DD
 * @property string $PeriodoHasta Date format YYYY-MM-DD
 * @property string $RznSocProp
 * @property string $SecProf
 * @property string $Serie
 * @property int    $TipoCFE      Values: 101 | 102 | 103 | 131 | 132 | 133 | 201 | 202 | 203 | 231 | 232 | 233
 * @property string $TipoDocProp
 * @property string $TipoTraslado
 * @property string $ViaTransp
 */
class IdDoc
{
    use HasAttributeTrait;

    protected $arCfeTypes = [
        101,
        102,
        103,
        111,
        112,
        113,
        181,
        182,
        121,
        122,
        123,
        124,
        131,
        132,
        133,
        141,
        142,
        143,
        151,
        152,
        153
    ];

    public function getSortKeys(): array
    {
        return [
            'TipoCFE',
            'Serie',
            'Nro',
            'NroInterno',
            'FchEmis',
            'PeriodoDesde',
            'PeriodoHasta',
            'MntBruto',
            'FmaPago',
            'FchVenc',
            'InfoAdicionalDoc',
            'IVAalDia',
            'SecProf',
            'IndPagCta3ros',
            'IndCobPropia',
        ];
    }

    public function getRules(): array
    {
        return [
            'TipoCFE' => ['required', Rule::in($this->arCfeTypes)]
        ];
    }
}

<?php

namespace PlanetaDelEste\Ucfe\Cfe\Encabezado;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use PlanetaDelEste\Ucfe\Cfe\CfeItemBase;

/**
 * @property string $ClauVenta
 * @property string $CodPaisProp
 * @property string $DocProp
 * @property string $DocPropExt
 * @property Carbon $FchEmis       Date format YYYY-MM-DD
 * @property string $FchValor
 * @property Carbon $FchVenc       Date format YYYY-MM-DD
 * @property int    $FmaPago       Values: 1 (Contado) | 2 (Credito)
 * @property string $IVAalDia
 * @property string $IndCobPropia
 * @property string $IndCompraMEReventa
 * @property string $IndPagCta3ros
 * @property string $IndPropiedad
 * @property string $InfoAdicionalDoc
 * @property int    $MntBruto      Values: 1 | 2 | 3
 * @property string $ModVenta
 * @property int    $Nro
 * @property string $NroInterno
 * @property string $PeriodoDesde  Date format YYYY-MM-DD
 * @property string $PeriodoHasta  Date format YYYY-MM-DD
 * @property string $RznSocProp
 * @property string $SecProf
 * @property string $Serie
 * @property int    $TipoCFE       Values: 101 | 102 | 103 | 131 | 132 | 133 | 201 | 202 | 203 | 231 | 232 | 233
 * @property string $TipoDocProp
 * @property string $TipoTraslado  Value: 1 (Venta) | 2 (Traslados internos)
 * @property string $ViaTransp
 */
class IdDoc extends CfeItemBase
{
    protected array $arCfeTypes = [
        101, // e-Ticket
        102, // Nota de Crédito de e-Ticket
        103, // Nota de Débito de e-Ticket
        111, // e-Factura
        112, // Nota de Crédito de e-Factura
        113, // Nota de Débito de e-Factura
        181, // e-Remito
        182, // e-Resguardo
        121, // e-Factura Exportación
        122, // Nota de Crédito de e-Factura Exportación
        123, // Nota de Débito de e-Factura Exportación
        124, // e-Remito de Exportación
        131, // e-Ticket Venta por Cuenta Ajena
        132, // Nota de Crédito de e-Ticket Venta por Cuenta Ajena
        133, // Nota de Débito de e-Ticket Venta por Cuenta Ajena
        141, // e-Factura Venta por Cuenta Ajena
        142, // Nota de Crédito de e-Factura Venta por Cuenta Ajena
        143, // Nota de Débito de e-Factura Venta por Cuenta Ajena
        151, // e-Boleta de entrada
        152, // Nota de crédito de e-Boleta de entrada
        153, // Nota de débito de e-Boleta de entrada

        // CONTINGENCY
        201,
        202,
        203,
        211,
        212,
        213,
        281,
        282,
        221,
        222,
        223,
        224,
        231,
        232,
        233,
        241,
        242,
        243,
        251,
        252,
        253,
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

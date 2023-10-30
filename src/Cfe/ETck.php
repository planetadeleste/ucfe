<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use Carbon\Carbon;

/**
 * @property Carbon       $TmstFirma
 * @property Encabezado   $Encabezado
 * @property Detalle      $Detalle
 * @property SubTotInfo   $SubTotInfo
 * @property DscRcgGlobal $DscRcgGlobal
 * @property MediosPago   $MediosPago
 * @property Referencia   $Referencia
 * @property CAEData      $CAEData
 * @property Compl_Fiscal $Compl_Fiscal
 */
class ETck extends CfeItemBase
{
    protected array $arRelationList = [
        'Encabezado'   => Encabezado::class,
        'Detalle'      => Detalle::class,
        'SubTotInfo'   => SubTotInfo::class,
        'DscRcgGlobal' => DscRcgGlobal::class,
        'MediosPago'   => MediosPago::class,
        'Referencia'   => Referencia::class,
        'CAEData'      => CAEData::class,
        'Compl_Fiscal' => Compl_Fiscal::class,
    ];

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'TmstFirma',
            'Encabezado',
            'Detalle',
            'SubTotInfo',
            'DscRcgGlobal',
            'MediosPago',
            'Referencia',
            'CAEData',
            'Compl_Fiscal',
        ];
    }
}

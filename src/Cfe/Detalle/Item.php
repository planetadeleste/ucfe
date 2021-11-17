<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle;

use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\CodItem;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\RetencPercep;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\SubDescuento;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\SubRecargo;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property int          $Cantidad
 * @property CodItem      $CodItem
 * @property float        $DescuentoMonto
 * @property float        $DescuentoPct
 * @property string       $DscItem
 * @property string       $IndAgenteResp
 * @property int          $IndFact
 * @property float        $MontoItem
 * @property string       $NCM
 * @property string       $NomItem
 * @property int          $NroLinDet
 * @property float        $PrecioUnitario
 * @property float        $RecargoMnt
 * @property float        $RecargoPct
 * @property RetencPercep $RetencPercep
 * @property SubDescuento $SubDescuento
 * @property SubRecargo   $SubRecargo
 * @property string       $UniMed
 */
class Item
{
    use HasAttributeTrait;

    public function getUniMedAttribute(): string
    {
        return 'N/A';
    }

    public function getCantidadAttribute($sValue = 1)
    {
        return $sValue > 0 ? $sValue : 1;
    }

    public function setCantidadAttribute($sValue): void
    {
        if (!$sValue || !is_numeric($sValue) || $sValue <= 0) {
            $sValue = 1;
        }

        if ($this->PrecioUnitario) {
            $this->MontoItem = $this->PrecioUnitario * $sValue;
        }
    }

    public function setPrecioUnitarioAttribute($sValue)
    {
        if ($sValue > 0) {
            $this->MontoItem = $sValue * $this->Cantidad;
        }

        $this->arAttributes['PrecioUnitario'] = $sValue;
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroLinDet',
            'CodItem',
            'IndFact',
            'IndAgenteResp',
            'NomItem',
            'NCM',
            'DscItem',
            'Cantidad',
            'UniMed',
            'PrecioUnitario',
            'DescuentoPct',
            'DescuentoMonto',
            'SubDescuento',
            'RecargoPct',
            'RecargoMnt',
            'SubRecargo',
            'RetencPercep',
            'MontoItem',
        ];
    }
}

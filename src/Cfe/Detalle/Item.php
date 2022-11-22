<?php

namespace PlanetaDelEste\Ucfe\Cfe\Detalle;

use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\CodItem;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\RetencPercep;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\SubDescuento;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item\SubRecargo;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property int          $NroLinDet Numero Secuencial de Linea
 * @property CodItem      $CodItem   Codificacion del Item
 * @property int          $IndFact   Indicador de Facturación (Item_Det_Fact)
 *                                 <pre>
 *                                 1:  Exento de IVA
 *                                 2:  Gravado a Tasa Mínima
 *                                 3:  Gravado a Tasa Básica
 *                                 4.  Gravado a "Otra Tasa"/IVA sobre fictos
 *                                 5:  Entrega Gratuita. Por ejemplo docenas de trece
 *                                 6:  Producto o servicio    no facturable
 *                                 7:  Producto o servicio no facturable negativo
 *                                 10: Exportación y asimiladas
 *                                 11: Impuesto percibido
 *                                 12: IVA en suspenso
 *                                 16: Sólo para ítems vendidos por contribuyentes con obligación IVA mínimo,
 *                                     Monotributo o Monotributo MIDES</pre>
 * @property string       $IndAgenteResp Indicador Agente Retenedor
 * @property string       $NomItem Nombre del Item
 * @property string       $NCM
 * @property string       $DscItem Descripcion adicional del Item
 * @property int          $Cantidad Cantidad del ítem. Se admite negativo sólo para eliminar un ítem del propio CFE.
 * @property string       $UniMed Unidad de Medida
 * @property float        $PrecioUnitario Precio Unitario
 * @property float        $DescuentoPct Porcentaje de Descuento
 * @property float        $DescuentoMonto Totaliza los descuentos otorgados al item
 * @property SubDescuento $SubDescuento Distribucion del Descuento
 * @property float        $RecargoPct Recargo en % al item
 * @property float        $RecargoMnt Totaliza los recargos otorgados al item
 * @property SubRecargo   $SubRecargo Tabla de Distribución del Recargo
 * @property RetencPercep $RetencPercep Codigo de Retencion / Percepcion
 * @property float        $MontoItem Monto por Linea de Detalle. Corresponde al Monto Neto, a menos que MntBruto Indique lo Contrario
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
        if (!$sValue || !is_numeric($sValue)) {
            $sValue = 1;
        }

        if ($fUnitValue = $this->PrecioUnitario) {
            $fTotal = $fUnitValue * $sValue;

            if ($this->DescuentoMonto) {
                $fTotal -= $this->DescuentoMonto;
            }

            if ($this->RecargoMnt) {
                $fTotal += $this->RecargoMnt;
            }

            $this->MontoItem = round($fTotal, 2);
        }

        $this->arAttributes['Cantidad'] = $sValue;
    }

    public function setPrecioUnitarioAttribute($sValue): void
    {
        // PrecioUnitario always be positive
        $sValue = abs($sValue);

        if ($sValue > 0) {
            $this->MontoItem = round($sValue * $this->Cantidad, 2);
        }

        $this->arAttributes['PrecioUnitario'] = $sValue;
    }

    /**
     * Agrega un elemento SubDescuento
     *
     * @param \PlanetaDelEste\Ucfe\Cfe\Detalle\Item\SubDescuento $obDiscount
     *
     * @return void
     */
    public function addSubDescuento(SubDescuento $obDiscount): void
    {
        if (!$this->hasAttribute('SubDescuento')) {
            $this->arAttributes['SubDescuento'] = [];
        }

        $this->arAttributes['SubDescuento'][] = $obDiscount->toArray();
    }

    /**
     * Agrega multiple elementos SubDescuento
     *
     * @param SubDescuento[] $arDiscounts
     *
     * @return void
     */
    public function addSubDescuentos(array $arDiscounts): void
    {
        if (empty($arDiscounts)) {
            return;
        }

        foreach ($arDiscounts as $obDiscount) {
            $this->addSubDescuento($obDiscount);
        }
    }

    /**
     * Agrega un elemento SubRecargo
     *
     * @param \PlanetaDelEste\Ucfe\Cfe\Detalle\Item\SubRecargo $obCharge
     *
     * @return void
     */
    public function addSubRecargo(SubRecargo $obCharge): void
    {
        if (!$this->hasAttribute('SubRecargo')) {
            $this->arAttributes['SubRecargo'] = [];
        }

        $this->arAttributes['SubRecargo'][] = $obCharge->toArray();
    }

    /**
     * Agrega multiples elementos SubRecargo
     *
     * @param SubRecargo[] $arCharges
     *
     * @return void
     */
    public function addSubRecargos(array $arCharges): void
    {
        if (empty($arCharges)) {
            return;
        }

        foreach ($arCharges as $obCharge) {
            $this->addSubRecargo($obCharge);
        }
    }

    /**
     * @param RetencPercep $obData
     *
     * @return void
     */
    public function addRetencPercep(RetencPercep $obData): void
    {
        if (!$this->hasAttribute('RetencPercep')) {
            $this->arAttributes['RetencPercep'] = [];
        }

        $this->arAttributes['RetencPercep'][] = $obData->toArray();
    }

    /**
     * @param RetencPercep[] $arItems
     *
     * @return void
     */
    public function addRetencPerceps(array $arItems): void
    {
        if (empty($arItems)) {
            return;
        }

        foreach ($arItems as $arItem) {
            $this->addRetencPercep($arItem);
        }
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

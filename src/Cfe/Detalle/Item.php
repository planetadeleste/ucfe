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
        if (!$sValue || !is_numeric($sValue) || $sValue <= 0) {
            $sValue = 1;
        }

        if ($this->PrecioUnitario) {
            $this->MontoItem = $this->PrecioUnitario * $sValue;
        }

        $this->arAttributes['Cantidad'] = $sValue;
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

<?php

namespace PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property string $NroLinDR  Numero Secuencial de Linea
 * @property string $TpoMovDR  Tipo de Movimiento (valores: D/R) Descuento / Recargo
 * @property string $TpoDR     Tipo Descuento o Recargo (valores: 1 - $, 2 - %)
 * @property string $CodDR     Código del Descuento/Recargo
 * @property string $GlosaDR   Descripcion del Descuento o Recargo
 * @property string $ValorDR   Monto del Descuento o Recargo
 * @property string $IndFactDR Indicador de facturación
 *                             <pre>
 *                             1:  Exento de IVA
 *                             2:  Gravado a Tasa Mínima
 *                             3:  Gravado a Tasa Básica
 *                             4.  Gravado a "Otra Tasa"/IVA sobre fictos
 *                             6:  Producto o servicio no facturable
 *                             7:  Producto o servicio no facturable negativo
 *                             10: Exportación y asimiladas
 *                             11: Impuesto percibido
 *                             12: IVA en suspenso
 *                             13: Sólo para e-Boleta de entrada y sus notas de corrección Ítem vendido por un no
 *                                 contribuyente (valida que A-C60≠2)
 *                             14: Sólo para e-Boleta de entrada y sus notas de corrección: Ítem vendido por un
 *                                 contribuyente IVA Mínimo, Monotributo o Monotributo MIDES (valida que A-C60=2)
 *                             15: Sólo para e-Boleta de entrada y sus notas de corrección: Ítem vendido por un
 *                                 contribuyente (valida que A-C60≠2)
 *                             16: Sólo para ítems vendidos por contribuyentes con obligación IVA mínimo, Monotributo o
 *                                 Monotributo MIDES
 *                             17: Sólo para e-Boleta de entrada y sus notas de corrección si se trata de compra de
 *                                 moneda extranjera para su reventa y la contrapartida es en moneda local (valida
 *                                 A-C21=1)</pre>
 */
class DRG_Item
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'NroLinDR',
            'TpoMovDR',
            'TpoDR',
            'CodDR',
            'GlosaDR',
            'ValorDR',
            'IndFactDR',
        ];
    }
}

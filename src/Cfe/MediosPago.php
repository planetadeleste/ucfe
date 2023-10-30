<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\MediosPago\MedioPago;

/**
 * @property MedioPago[] $MedioPago
 */
class MediosPago extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['MedioPago'];
    }

    public function setMedioPagoAttribute(array $sValue = [])
    {
        $this->setAttributeList($sValue, 'MedioPago', MedioPago::class);
    }
}

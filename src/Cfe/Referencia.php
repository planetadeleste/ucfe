<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\Referencia\Referencia as ReferenciaItem;

/**
 * @property ReferenciaItem[] $Referencia Identificacion de otros documentos referenciados por Documento
 */
class Referencia extends CfeItemBase
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['Referencia'];
    }

    public function setReferenciaAttribute(array $sValue = [])
    {
        $this->setAttributeList($sValue, 'Referencia', ReferenciaItem::class);
    }
}

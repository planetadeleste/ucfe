<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use PlanetaDelEste\Ucfe\Cfe\Referencia\Referencia as ReferenciaItem;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property ReferenciaItem[] $Referencia Identificacion de otros documentos referenciados por Documento
 */
class Referencia
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['Referencia'];
    }
}

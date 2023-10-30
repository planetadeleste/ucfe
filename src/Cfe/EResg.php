<?php

namespace PlanetaDelEste\Ucfe\Cfe;

class EResg extends ETck
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [
            'TmstFirma',
            'Encabezado',
            'Detalle',
            'Referencia',
            'CAEData',
        ];
    }
}

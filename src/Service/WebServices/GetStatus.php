<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * @property integer $rut
 * @property integer $rutRecibido
 * @property integer $tipoCfe
 * @property string  $serieCfe
 * @property integer $numeroCfe
 *
 * @method GetStatusResponse send()
 */
class GetStatus extends WebServicesFE
{

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return GetStatusResponse::class;
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['rut', 'rutRecibido', 'tipoCfe', 'serieCfe', 'numeroCfe'];
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'ObtenerEstadoCfeRecibido';
    }
}

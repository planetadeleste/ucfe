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
 * @method GetReceivedPdfResponse send()
 */
class GetReceivedPdf extends WebServicesFE
{
    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'ObtenerPdfCfeRecibido';
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return GetReceivedPdfResponse::class;
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['rut', 'rutRecibido', 'tipoCfe', 'serieCfe', 'numeroCfe'];
    }
}

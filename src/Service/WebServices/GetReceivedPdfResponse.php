<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFEResponse;

class GetReceivedPdfResponse extends WebServicesFEResponse
{

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'ObtenerPdfCfeRecibido';
    }
}

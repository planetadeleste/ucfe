<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\Service\WebServices\GetReceivedPdfResponse;

class GetReceivedPdfWithParametersResponse extends GetReceivedPdfResponse
{
    public function getServiceName() : string
    {
        return 'ObtenerPdfCfeRecibidoConParametros';
    }
}

<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\Result\ObtenerCfeRecibidos;
use PlanetaDelEste\Ucfe\WebServicesFEResponse;

/**
 * @method ObtenerCfeRecibidos getResult()
 */
class GetReceivedInvoicesResponse extends WebServicesFEResponse
{
    public function getServiceName(): string
    {
        return 'ObtenerCfeRecibidosPagina';
    }

    /**
     * @inheritDoc
     */
    protected function parseResult()
    {
        return $this->obResponse;
    }
}

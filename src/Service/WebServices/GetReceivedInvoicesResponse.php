<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFEResponse;

class GetReceivedInvoicesResponse extends WebServicesFEResponse
{

    /**
     * @inheritDoc
     */
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

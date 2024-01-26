<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFEResponse;

class GetCfeInvoiceResponse extends WebServicesFEResponse
{

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'ObtenerCfeEmitido';
    }

    /**
     * @inheritDoc
     */
    protected function parseResult()
    {
        return $this->obResponse;
    }
}

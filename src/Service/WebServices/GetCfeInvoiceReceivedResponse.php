<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\Result\CfeRecibido;
use PlanetaDelEste\Ucfe\WebServicesFEResponse;

/**
 * Class GetCfeInvoiceReceivedResponse
 *
 * @method CfeRecibido getResult()
 */
class GetCfeInvoiceReceivedResponse extends WebServicesFEResponse
{
    public function getServiceName(): string
    {
        return 'ObtenerCfeRecibido';
    }

    /**
     * @inheritDoc
     */
    protected function parseResult()
    {
        return $this->obResponse;
    }
}

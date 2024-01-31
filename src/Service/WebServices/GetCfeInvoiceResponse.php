<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\Result\ObtenerCfeEmitido;
use PlanetaDelEste\Ucfe\WebServicesFEResponse;

/**
 * Class GetCfeInvoiceResponse
 *
 * @package PlanetaDelEste\Ucfe\Service\WebServices
 * @method ObtenerCfeEmitido getResult()
 */
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

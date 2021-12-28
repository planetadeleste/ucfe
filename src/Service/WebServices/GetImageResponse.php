<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFEResponse;

/**
 * @method string getResult()
 */
class GetImageResponse extends WebServicesFEResponse
{

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'GenerarImagen';
    }

    /**
     * @inheritDoc
     */
    protected function parseResult()
    {
        return $this->obResponse->base64Binary;
    }
}

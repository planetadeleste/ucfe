<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFEResponse;

class GetReceivedImageResponse extends WebServicesFEResponse
{

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'GenerarImagenCfeRecibido';
    }

    /**
     * @inheritDoc
     */
    protected function parseResult()
    {
        return $this->obResponse;
    }
}

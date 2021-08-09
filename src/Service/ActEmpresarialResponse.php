<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Response;

class ActEmpresarialResponse extends Response
{
    /**
     * @return array
     */
    protected function parseResult(): array
    {
        if (!$this->obResponse || !isset($this->obResponse->XmlCfeFirmado)) {
            return [];
        }

        $sXml = str_replace('xmlns="DGI_Modernizacion_Consolidado"', '', $this->obResponse->XmlCfeFirmado);
        $sXml = simplexml_load_string($sXml);
        $obJson = json_encode($sXml);
        return json_decode($obJson, true);
    }
}

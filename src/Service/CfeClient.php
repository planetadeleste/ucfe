<?php

namespace PlanetaDelEste\Ucfe\Service;

use Illuminate\Support\Arr;
use PlanetaDelEste\Ucfe\Client;
use Ramsey\Uuid\Uuid;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * @method string getType()
 * @method array getData(bool $bForce = false)
 */
class CfeClient extends Client
{
    protected $iTipoMensaje = 820;

    /**
     * @return \PlanetaDelEste\Ucfe\Service\CfeResponse
     * @throws \Exception
     */
    public function signAndSend(): CfeResponse
    {
        $this->iTipoMensaje = 310;
        return $this->send();
    }

    /**
     * @return \PlanetaDelEste\Ucfe\Service\CfeResponse
     * @throws \Exception
     */
    public function cert(): CfeResponse
    {
        $this->iTipoMensaje = 210;
        return $this->send();
    }

    /**
     * @return \PlanetaDelEste\Ucfe\Service\CfeResponse
     * @throws \Exception
     */
    public function send(): CfeResponse
    {
        $arData = [
            'CfeXmlOTexto' => $this->xml(),
            'TipoCfe'      => Arr::get($this->getData(), 'Encabezado.IdDoc.TipoCFE'),
            'IdReq'        => 1,
            'Uuid'         => Uuid::uuid4()->toString()
        ];
        return $this->exec($arData);
    }

    public function xml(): string
    {
        $arXmlData = [$this->getType() => $this->getData(true)];
        $arRoot = [
            'rootElementName' => 'CFE',
            '_attributes'     => [
                'xmlns'     => 'http://cfe.dgi.gub.uy',
                'version'   => '1.0',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
            ]
        ];
        return ArrayToXml::convert($arXmlData, $arRoot, true, 'UTF-8');
    }

    /**
     * @return \PlanetaDelEste\Ucfe\Service\CfeResponse
     * @throws \Exception
     */
    public function range(): CfeResponse
    {
        $this->iTipoMensaje = 220;
        return $this->send();
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return $this->iTipoMensaje;
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return CfeResponse::class;
    }
}

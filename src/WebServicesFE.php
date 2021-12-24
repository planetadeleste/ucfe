<?php

namespace PlanetaDelEste\Ucfe;

use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

abstract class WebServicesFE extends Client
{
    use HasAttributeTrait;

    /**
     * @return mixed
     * @throws \Exception
     */
    public function send()
    {
        return $this->exec($this->toArray());
    }

    /**
     * @param array $arParams
     *
     * @return mixed
     * @throws \Exception
     */
    public function exec(array $arParams = [])
    {
        $this->validateAuth();

        $this->inbox = false;
        $sResponseClass = $this->getResponseClass();
        $sServiceName = $this->getServiceName();
        $obResponse = $this->soap()->$sServiceName($arParams);

        return new $sResponseClass($obResponse);
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return null;
    }

    protected function getWsdlUrl(): string
    {
        return 'Query/WebServicesFE.svc?wsdl';
    }

    /**
     * Get soap service name
     *
     * @return string
     */
    abstract public function getServiceName(): string;

}

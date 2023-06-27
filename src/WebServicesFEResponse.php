<?php

namespace PlanetaDelEste\Ucfe;

abstract class WebServicesFEResponse
{
    /** @var \stdClass */
    protected $obResponse;

    /** @var mixed */
    protected $arResult;

    /**
     * @param \stdClass $obResponse
     */
    public function __construct($obResponse)
    {
        if (is_object($obResponse)) {
            $sServiceName = $this->getServiceName();
            $sServiceResult = $sServiceName.'Result';
            $this->obResponse = $obResponse->$sServiceResult;
            $this->arResult = $this->parseResult();
        }
    }

    /**
     * Get soap service name
     *
     * @return string
     */
    abstract public function getServiceName(): string;

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->arResult;
    }

    /**
     * @inheritDoc
     */
    protected function parseResult()
    {
        return $this->obResponse;
    }
}

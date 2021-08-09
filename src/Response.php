<?php

namespace PlanetaDelEste\Ucfe;

abstract class Response
{
    /** @var \PlanetaDelEste\Ucfe\Result\Resp */
    protected $obResponse;

    /** @var array */
    protected $arResult = [];

    /**
     * @param \PlanetaDelEste\Ucfe\Result\Base|\stdClass $obResponse
     */
    public function __construct($obResponse)
    {
        if (is_object($obResponse)) {
            $this->obResponse = $obResponse->InvokeResult->Resp;
            $this->arResult = $this->parseResult();
        }
    }

    public function getResult(): array
    {
        return $this->arResult;
    }

    /**
     * @return array
     */
    abstract protected function parseResult(): array;
}

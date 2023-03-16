<?php

namespace PlanetaDelEste\Ucfe;

use PlanetaDelEste\Ucfe\Result\Base;
use PlanetaDelEste\Ucfe\Result\Resp;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;
use stdClass;

abstract class Response
{
    use HasAttributeTrait;

    /** @var Resp */
    protected $obResponse;

    /** @var array */
    protected $arResult = [];

    /**
     * @param Base|stdClass $obResponse
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

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
    protected array $arResult = [];

    /** @var float */
    protected float $elapsed = 0;

    protected string $url = '';

    /**
     * @param Base|stdClass $obResponse
     */
    public function __construct($obResponse)
    {
        if (is_object($obResponse)) {
            $this->obResponse = $obResponse->InvokeResult->Resp;
            $this->elapsed    = $obResponse->elapsed;
            $this->url        = $obResponse->url;
            $this->arResult   = $this->parseResult();
        }
    }

    public function getResult(): array
    {
        return $this->arResult;
    }

    public function getElapsed(): float
    {
        return $this->elapsed;
    }

    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return array
     */
    abstract protected function parseResult(): array;
}

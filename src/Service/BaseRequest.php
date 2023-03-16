<?php

namespace PlanetaDelEste\Ucfe\Service;

use Exception;
use PlanetaDelEste\Ucfe\Client;

abstract class BaseRequest extends Client
{
    protected array $arData = [];
    protected array $arKeys = [];


    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return BaseResponse::class;
    }

    public function __call($name, $arguments = [])
    {
        if (!in_array($name, $this->arKeys)) {
            return null;
        }

        $sValue = is_array($arguments) && !empty($arguments) ? $arguments[0] : $arguments;

        if (!is_null($sValue) && (!empty($sValue) || is_bool($sValue) || is_numeric($sValue))) {
            array_set($this->arData, $name, $sValue);
        }

        return array_get($this->arData, $name);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function send()
    {
        return $this->exec($this->getSendData());
    }

    /**
     * @return array
     */
    abstract protected function getSendData(): array;
}

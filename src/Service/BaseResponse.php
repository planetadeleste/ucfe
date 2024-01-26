<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Response;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property-read string|null $CodRta
 * @property-read string|null $MensajeRta
 */
class BaseResponse extends Response
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    protected function parseResult(): array
    {
        $arData = $this->obResponse ? get_object_vars($this->obResponse) : [];
        $this->setAttributes($arData);

        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [];
    }
}

<?php

namespace PlanetaDelEste\Ucfe\Service;

use Carbon\Carbon;
use PlanetaDelEste\Ucfe\Service\BaseRequest;

/**
 * @method string|null date(string $sValue = null)
 * @method StatusResponse send()
 */
class EcoRequest extends BaseRequest
{
    /**
     * @var array|string[]
     */
    protected array $arKeys = ['date'];

    /**
     * @inheritDoc
     */
    protected function getSendData(): array
    {
        $obDate = Carbon::parse($this->date());

        return [
            'FechaReq' => $obDate->format('Ymd'),
            'HoraReq'  => $obDate->format('His'),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 820;
    }

    /**
     * @return string
     */
    protected function getResponseClass(): string
    {
        return StatusResponse::class;
    }
}

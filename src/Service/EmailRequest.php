<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Client;

/**
 * @method string|null email(string $sValue = null)
 * @method string|null uuid(string $sValue = null)
 * @method string|null type(string $sValue = null)
 * @method string|null serie(string $sValue = null)
 * @method string|null number(string $sValue = null)
 */
class EmailRequest extends BaseRequest
{
    protected array $arKeys = ['email', 'uuid', 'type', 'serie', 'number'];

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 390;
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return EmailResponse::class;
    }

    protected function getSendData(): array
    {
        return [
            'Uuid'                  => $this->uuid(),
            'TipoCfe'               => $this->type(),
            'Serie'                 => $this->serie(),
            'NumeroCfe'             => $this->number(),
            'EmailEnvioPdfReceptor' => $this->email(),
        ];
    }
}

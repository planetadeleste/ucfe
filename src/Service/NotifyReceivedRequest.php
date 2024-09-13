<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Service\BaseRequest;

/**
 * @method int TipoNotificacion(int $sValue = null)
 * @method string IdReq(string $sValue = null)
 * @method NotifyReceivedResponse send()
 */
class NotifyReceivedRequest extends BaseRequest
{
    /**
     * @var array<string>
     */
    protected array $arKeys = ['IdReq', 'TipoNotificacion'];

    /**
     * @return string
     */
    public function getInbox(): string
    {
        return 'Inbox115';
    }

    /**
     * @inheritDoc
     */
    protected function getSendData(): array
    {
        return [
            'IdReq'            => $this->IdReq(),
            'TipoNotificacion' => $this->TipoNotificacion(),
        ];
    }

    /**
     * @return string
     */
    protected function getResponseClass(): string
    {
        return NotifyReceivedResponse::class;
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 620;
    }
}

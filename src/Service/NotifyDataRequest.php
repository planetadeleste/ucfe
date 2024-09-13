<?php

namespace PlanetaDelEste\Ucfe\Service;

/**
 * @method int TipoNotificacion(int $sValue = null)
 * @method string IdReq(string $sValue = null)
 * @method NotifyDataResponse send()
 */
class NotifyDataRequest extends BaseRequest
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
        return 'Inbox';
    }

    /**
     * @return string
     */
    protected function getResponseClass(): string
    {
        return NotifyDataResponse::class;
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
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 610;
    }
}

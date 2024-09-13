<?php

namespace PlanetaDelEste\Ucfe\Service;

use Carbon\Carbon;

/**
 * @method int TipoNotificacion(int $sValue = null)  5 Aviso de CFE emitido rechazado por DGI
 *                                                   6 Aviso de CFE emitido rechazado por el receptor electrónico
 *                                                   7 Aviso de CFE recibido
 *                                                   8 Aviso de anulación de CFE recibido
 *                                                   9 Aviso de aceptación comercial de un CFE recibido
 *                                                  10 Aviso de aceptación comercial de un CFE recibido en la gestión UCFE
 *                                                  11 Aviso de que se ha emitido un CFE
 *                                                  12 Aviso de que se ha emitido un CFE en la gestión UCFE
 *                                                  13 Aviso de rechazo comercial de un CFE recibido
 *                                                  14 Aviso de rechazo comercial de un CFE recibido en la gestión UCFE
 *                                                  15 Aviso de CFE emitido aceptado por DGI
 *                                                  16 Aviso de CFE emitido aceptado por el receptor electrónico
 *                                                  17 Aviso que a un CFE emitido se lo ha etiquetado
 *                                                  18 Aviso que a un CFE emitido se le removió una etiqueta
 *                                                  19 Aviso que a un CFE recibido se lo ha etiquetado
 *                                                  20 Aviso que a un CFE recibido se le removió una etiqueta
 * @method string|null date(string $sValue = null)
 * @method NotifyAvailableResponse send()
 */
class NotifyAvailableRequest extends BaseRequest
{
    /**
     * @var array<string>
     */
    protected array $arKeys = ['TipoNotificacion', 'date'];

    /**
     * @return $this
     */
    public function notifyCfeReceived(): NotifyAvailableRequest
    {
        $this->TipoNotificacion(7);

        return $this;
    }

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
        return NotifyAvailableResponse::class;
    }

    /**
     * @inheritDoc
     */
    protected function getSendData(): array
    {
        $obDate = $this->date() ? Carbon::parse($this->date()) : now();

        return [
            'FechaReq'         => $obDate->format('Ymd'),
            'HoraReq'          => $obDate->format('His'),
            'TipoNotificacion' => $this->TipoNotificacion(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 600;
    }
}

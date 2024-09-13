<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Service\BaseResponse;

/**
 * @property-read int|null $IdReq
 * @property-read string   $TipoNotificacion
 * @property-read string   $CodRta 00 Petición aceptada y procesada.
 *                                 01 Petición denegada.
 *                                 03 Comercio inválido.
 *                                 05 CFE rechazado por DGI.
 *                                 06 CFE observado por DGI.
 *                                 11 CFE aceptado por UCFE, en espera de respuesta de DGI.
 *                                 12 Requerimiento inválido.
 *                                 30 Error en formato.
 *                                 31 Error en formato de CFE.
 *                                 89 Terminal inválida.
 *                                 96 Error en sistema.
 *                                 99 Sesión no iniciada
 */
class NotifyAvailableResponse extends BaseResponse
{
    /**
     * @var array<string>
     */
    public $arCast = ['IdReq' => 'int'];

    /**
     * @return string|null
     */
    public function getMensajeRtaAttribute(): ?string
    {
        if (!$this->CodRta) {
            return null;
        }

        $sMessage = $this->getCodRtaMessage($this->CodRta);

        return $this->getOriginalAttr('MensajeRta') ?? $sMessage;
    }
}

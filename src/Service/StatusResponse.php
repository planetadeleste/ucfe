<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Response;

/**
 * @property-read string|null $CodRta 00 Petición aceptada y procesada.
 *                                    01 Petición denegada.
 *                                    03 Comercio inválido.
 *                                    05 CFE rechazado por DGI.
 *                                    06 CFE observado por DGI.
 *                                    11 CFE aceptado por UCFE, en espera de respuesta de DGI.
 *                                    12 Requerimiento inválido.
 *                                    30 Error en formato.
 *                                    31 Error en formato de CFE.
 *                                    89 Terminal inválida.
 *                                    96 Error en sistema.
 *                                    99 Sesión no iniciada
 */
class StatusResponse extends BaseResponse
{
}

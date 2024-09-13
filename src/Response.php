<?php

namespace PlanetaDelEste\Ucfe;

use PlanetaDelEste\Ucfe\Result\Base;
use PlanetaDelEste\Ucfe\Result\Resp;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;
use stdClass;

/**
 * @property string|null $errorMessage
 * @property int|null    $errorCode
 */
abstract class Response
{
    use HasAttributeTrait;

    /**
     * @var Resp
     */
    protected $obResponse;

    /**
     * @var array
     */
    protected array $arResult = [];

    /**
     * @var float
     */
    protected float $elapsed = 0;

    /**
     * @var string
     */
    protected string $url = '';

    /**
     * @param Base|stdClass $obResponse
     */
    public function __construct($obResponse)
    {
        if (!is_object($obResponse)) {
            return;
        }

        if ($sError = $obResponse->InvokeResult->ErrorMessage) {
            $this->errorMessage = $sError;
            $this->errorCode    = $obResponse->InvokeResult->ErrorCode;
        }

        $this->obResponse = $obResponse->InvokeResult->Resp;
        $this->elapsed    = $obResponse->elapsed;
        $this->url        = $obResponse->url;
        $this->arResult   = $this->parseResult();
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->arResult;
    }

    /**
     * @return float
     */
    public function getElapsed(): float
    {
        return $this->elapsed;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $sCode
     *
     * @return string|null
     */
    public function getCodRtaMessage(string $sCode): ?string
    {
        switch ($sCode) {
            case '01':
                return 'Petición denegada.';
            case '03':
                return 'Comercio inválido.';
            case '05':
                return 'CFE rechazado por DGI.';
            case '06':
                return 'CFE observado por DGI.';
            case '11':
                return 'CFE aceptado por UCFE, en espera de respuesta de DGI.';
            case '12':
                return 'Requerimiento inválido.';
            case '30':
                return 'Error en formato.';
            case '31':
                return 'Error en formato de CFE.';
            case '89':
                return 'Terminal inválida.';
            case '96':
                return 'Error en sistema.';
            case '99':
                return 'Sesión no iniciada.';
        }

        return null;
    }

    /**
     * @return array
     */
    abstract protected function parseResult(): array;
}

<?php

namespace PlanetaDelEste\Ucfe\Service;

use Spatie\ArrayToXml\ArrayToXml;

/**
 * @method string|null uuid(string $sValue = null)
 * @method string|null type(string $sValue = null)
 * @method string|null serie(string $sValue = null)
 * @method string|null number(string $sValue = null)
 * @method string|null rut(string $sValue = null)
 * @method bool|null xml(bool $sValue = null)
 * @method StatusResponse send()
 */
class StatusRequest extends BaseRequest
{
    protected array $arKeys = ['uuid', 'type', 'serie', 'number', 'rut', 'xml'];

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 360;
    }

    protected function getResponseClass(): string
    {
        return StatusResponse::class;
    }

    /**
     * @return array
     * @throws \DOMException
     */
    protected function getSendData(): array
    {
        $arData = [
            'TipoCfe'   => $this->type(),
            'Serie'     => $this->serie(),
            'NumeroCfe' => $this->number(),
            'RutEmisor' => $this->rut(),
        ];

        if ($this->xml() && $this->xml() === true) {
            $obArray = new ArrayToXml(['DevolverXml' => 'true'], 'Consulta');
            array_set($arData, 'CfeXmlOTexto', $obArray->dropXmlDeclaration()->toXml());
        }

        return $arData;
    }
}

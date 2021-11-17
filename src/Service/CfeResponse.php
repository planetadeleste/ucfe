<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Response;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

/**
 * @property-read string|null Adenda
 * @property-read string|null CaeNroDesde
 * @property-read string|null CaeNroHasta
 * @property-read string|null Certificado
 * @property-read string|null CertificadoParaFirmarCfe
 * @property-read string|null ClaveCertificadoFirma
 * @property-read string|null CodComercio
 * @property-read string|null CodRta
 * @property-read string|null CodTerminal
 * @property-read string|null CodigoSeguridad
 * @property-read string|null DatosQr
 * @property-read string|null EstadoEnDgiCfeRecibido
 * @property-read string|null EstadoSituacion
 * @property-read string|null Etiquetas
 * @property-read string|null FechaFirma
 * @property-read string|null FechaReq
 * @property-read string|null HoraReq
 * @property-read string|null IdCae
 * @property-read string|null IdReq
 * @property-read string|null ImagenQr
 * @property-read string|null MensajeRta
 * @property-read string|null NumeroCfe
 * @property-read string|null RangoDesde
 * @property-read string|null RangoHasta
 * @property-read string|null RutEmisor
 * @property-read string|null Serie
 * @property-read string|null TipoCfe
 * @property-read string|null TipoMensaje
 * @property-read string|null Uuid
 * @property-read string|null VencimientoCae
 * @property-read string|null XmlCfeFirmado
 */
class CfeResponse extends Response
{
    use HasAttributeTrait;

    /**
     * @inheritDoc
     */
    protected function parseResult(): array
    {
        $arData = get_object_vars($this->obResponse);
        $this->setAttributes($arData);

        return $arData;
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return [];
    }
}

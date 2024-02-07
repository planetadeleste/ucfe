<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

/**
 * @property integer $rut
 * @property integer $rutRecibido
 * @property integer $tipoCfe
 * @property string  $serieCfe
 * @property integer $numeroCfe
 * @property array   $nombreParametros
 * @property array   $valoresParametros
 *
 * @method GetReceivedPdfResponse send()
 */
class GetReceivedPdf extends GetPdf
{
    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return $this->roll || $this->addenda ? 'ObtenerPdfCfeRecibidoConParametros' : 'ObtenerPdfCfeRecibido';
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return $this->roll || $this->addenda ? GetReceivedPdfWithParametersResponse::class : GetReceivedPdfResponse::class;
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        $arKeys = ['rut', 'rutRecibido', 'tipoCfe', 'serieCfe', 'numeroCfe'];

        if ($this->roll || $this->addenda) {
            $arKeys[] = 'nombreParametros';
            $arKeys[] = 'valoresParametros';
        }

        return $arKeys;
    }
}

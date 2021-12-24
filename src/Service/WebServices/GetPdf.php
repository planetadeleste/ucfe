<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * @property integer $rut
 * @property integer $tipoCfe
 * @property string  $serieCfe
 * @property integer $numeroCfe
 *
 * @method GetPdfResponse send()
 */
class GetPdf extends WebServicesFE
{
    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'ObtenerPdf';
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['rut', 'tipoCfe', 'serieCfe', 'numeroCfe'];
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return GetPdfResponse::class;
    }
}

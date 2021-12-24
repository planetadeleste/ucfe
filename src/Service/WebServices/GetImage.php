<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * @property integer $rut
 * @property integer $tipoCfe
 * @property string  $serieCfe
 * @property integer $numeroCfe
 * @property string  $formato
 * @property integer $dpiX
 * @property integer $dpiY
 *
 * @method GetImageResponse send()
 */
class GetImage extends WebServicesFE
{
    const FORMAT_PNG = 'PNG';
    const FORMAT_JPEG = 'JPEG';
    const FORMAT_BMP = 'BMP';
    const FORMAT_GIF = 'GIF';
    const FORMAT_TIFF = 'TIFF';

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['rut', 'tipoCfe', 'serieCfe', 'numeroCfe', 'formato', 'dpiX', 'dpiY'];
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'GenerarImagen';
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return GetImageResponse::class;
    }
}

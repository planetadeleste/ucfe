<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

/**
 * @property string $rutRecibido
 */
class GetReceivedImage extends GetImage
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['rut', 'rutRecibido', 'tipoCfe', 'serieCfe', 'numeroCfe', 'formato', 'dpiX', 'dpiY'];
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'GenerarImagenCfeRecibido';
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return GetReceivedImageResponse::class;
    }
}

<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * Class GetCfeInvoice
 *
 * @property int    $rut
 * @property int    $rutRecibido
 * @property int    $tipoCfe
 * @property string $serieCfe
 * @property int    $numeroCfe
 *
 * @method GetCfeInvoiceReceivedResponse send()
 */
class GetCfeInvoiceReceived extends WebServicesFE
{
    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        return ['rut', 'rutRecibido', 'tipoCfe', 'serieCfe', 'numeroCfe'];
    }

    public function getServiceName(): string
    {
        return 'ObtenerCfeRecibido';
    }

    protected function getResponseClass(): string
    {
        return GetCfeInvoiceReceivedResponse::class;
    }

    protected function getWsdlUrl(): string
    {
        return 'Query116/WebServicesFE.svc?wsdl';
    }
}

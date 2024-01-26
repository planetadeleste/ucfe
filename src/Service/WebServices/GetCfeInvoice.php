<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * Class GetCfeInvoice
 *
 * @property integer $rut
 * @property integer $tipoCfe
 * @property string  $serieCfe
 * @property integer $numeroCfe
 */
class GetCfeInvoice extends WebServicesFE
{
    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return GetCfeInvoiceResponse::class;
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
    public function getServiceName(): string
    {
        return 'ObtenerCfeEmitido';
    }

    protected function getWsdlUrl(): string
    {
        return 'Query/WebServicesListadosFE.svc?wsdl';
    }
}

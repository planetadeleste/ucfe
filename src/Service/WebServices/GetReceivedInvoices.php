<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * @property string $rut       RUT de la empresa que recibió los CFE
 * @property string $rutEmisor RUT de la empresa que emitió los CFE
 * @property string $fechaDesde
 * @property string $fechaHasta
 * @property int    $tipoCfe   Número del tipo de comprobante que se quiere listar. En caso de querer traerlos todos se
 *           deberá enviar cero
 * @property int    $pagina      Número de página que se quiere listar
 * @property int    $pageSize  Cantidad de comprobantes que se quiere listar en la primera página
 *
 * @method GetReceivedInvoicesResponse send();
 */
class GetReceivedInvoices extends WebServicesFE
{
    /**
     * GetReceivedInvoices constructor.
     */
    public function __construct()
    {
// $this->rutEmisor = '0';
        $this->fechaHasta = now();
        $this->tipoCfe    = '0';
        $this->pagina     = 1;
        $this->pageSize   = 10;
    }

    /**
     * @return array
     */
    public function getSortKeys(): array
    {
        return ['rut', 'rutEmisor', 'fechaDesde', 'fechaHasta', 'tipoCfe', 'pagina', 'pageSize'];
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return 'ObtenerCfeRecibidosPagina';
    }

    /**
     * @return string DateTime format
     */
    public function getDateFormat(): string
    {
        return 'Ymd';
    }

    /**
     * @return string
     */
    protected function getResponseClass(): string
    {
        return GetReceivedInvoicesResponse::class;
    }

    /**
     * @return string
     */
    protected function getWsdlUrl(): string
    {
        return 'Query116/WebServicesListadosFE.svc?wsdl';
    }
}

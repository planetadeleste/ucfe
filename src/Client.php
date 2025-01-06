<?php

namespace PlanetaDelEste\Ucfe;

use Illuminate\Support\Arr;
use PlanetaDelEste\Ucfe\Result\Base;
use SoapClient;

abstract class Client
{
    /**
     * @var SoapClient
     */
    protected $client;

    /**
     * @var bool Add "Inbox" part to ws url
     */
    protected bool $inbox = true;

    /**
     * @var WsseAuthHeader
     */
    protected $auth;

    protected string $url = '';

    public static function __callStatic(string $name, array $arArgs = [])
    {
        if (method_exists(static::class, $name)) {
            return call_user_func_array([new static(), $name], $arArgs);
        }
    }

    /**
     * @param array $arParams array{
     *                        req: {
     *                        Req: {
     *                        Adenda: '',
     *                        Certificado: '',
     *                        CfeXmlOTexto: '',
     *                        CifrarComplementoFiscal: '',
     *                        CodComercio: '',
     *                        CodRta: '',
     *                        CodTerminal: '',
     *                        DatosQr: '',
     *                        EmailEnvioPdfReceptor: '',
     *                        EstadoSituacion: '',
     *                        FechaReq: '',
     *                        HoraReq: '',
     *                        IdReq: '',
     *                        Impresora: '',
     *                        NumeroCfe: '',
     *                        RechCom: '',
     *                        RutEmisor: '',
     *                        Serie: '',
     *                        TipoCfe: '',
     *                        TipoMensaje: '',
     *                        Uuid: '',
     *                        },
     *                        RequestDate:  '',
     *                        Tout:  '3000',
     *                        ReqEnc:  '',
     *                        CodComercio:  '',
     *                        CodTerminal:  '',
     *                        }
     *                        }
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function exec(array $arParams = [])
    {
        $this->validateAuth();

        $arReqData = [
            'CodComercio' => Auth::getCodComercio(),
            'CodTerminal' => Auth::getCodTerminal(),
        ];

        // Wrap primary key as req.Req
        if (!Arr::has($arParams, 'req') && !Arr::has($arParams, 'Req')) {
            $arParams = ['req' => ['Req' => $arParams]];
        }

        // Wrap primary key as req
        if (!Arr::has($arParams, 'req')) {
            $arParams = ['req' => $arParams];
        }

        // Merge CodComercio and CodTerminal
        Arr::set($arParams, 'req', $arReqData + Arr::get($arParams, 'req', []));
        Arr::set($arParams, 'req.Req', $arReqData + Arr::get($arParams, 'req.Req', []));

        // Set default timeout to 3000
        if (!Arr::has($arParams, 'req.Tout')) {
            Arr::set($arParams, 'req.Tout', 3000);
        }

        // Set default request date to now on main req key
        if (!Arr::has($arParams, 'req.RequestDate')) {
            Arr::set($arParams, 'req.RequestDate', date('s'));
        }

        // Set default request time
        if (!Arr::has($arParams, 'req.Req.HoraReq')) {
            Arr::set($arParams, 'req.Req.HoraReq', date('His'));
        }

        // Set default request date on req.Req key
        if (!Arr::has($arParams, 'req.Req.FechaReq')) {
            Arr::set($arParams, 'req.Req.FechaReq', date('Ymd'));
        }

        // Set TipoMensaje
        Arr::set($arParams, 'req.Req.TipoMensaje', $this->getTipoMensaje());

        /** @var Base $obResponse */
        $now            = microtime(true);
        $obResponse     = $this->soap()->Invoke($arParams);
        $sResponseClass = $this->getResponseClass();
        $elapsed        = microtime(true) - $now;

        $obResponse->elapsed = $elapsed;
        $obResponse->url     = $this->url;

        return new $sResponseClass($obResponse);
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    protected function validateAuth(): bool
    {
        if (!Auth::getUser()) {
            throw new \Exception('Username is required');
        }

        if (!Auth::getPassword()) {
            throw new \Exception('Password is required');
        }

        if (!Auth::getCodTerminal()) {
            throw new \Exception('The field CodTerminal is required');
        }

        if (!Auth::getCodComercio()) {
            throw new \Exception('The field CodComercio is required');
        }

        if (!Auth::getUrl()) {
            throw new \Exception('The field Url is required');
        }

        return true;
    }

    /**
     * @return mixed
     */
    abstract protected function getTipoMensaje();

    /**
     * @param array $arOptions
     *
     * @return SoapClient
     *
     * @throws \Exception
     */
    protected function soap(array $arOptions = []): SoapClient
    {
        $arOptions  = array_merge(
            [
                'trace'          => true,
                'cache_wsdl'     => WSDL_CACHE_BOTH,
                'keep_alive'     => false,
                'compression'    => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'user_agent'     => 'Apache-HttpClient/4.5.5 (Java/16.0.1)',
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'ciphers'           => 'AES256-SHA',
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true
                    ]
                ])
            ],
            $arOptions
        );
        $sSoapClass = $this->getSoapClass();
        $sUrl       = sprintf('https://%s.ucfe.com.uy/', Auth::getUrl());

        if ($this->inbox) {
            $sUrl .= $this->getInbox().'/';
        }

        $sUrl .= $this->getWsdlUrl();

        $this->url    = $sUrl;
        $this->client = new $sSoapClass($sUrl, $arOptions);
        $authHeader   = new WsseAuthHeader(Auth::getUser(), Auth::getPassword());
        $this->client->__setSoapHeaders([$authHeader]);

        return $this->client;
    }

    /**
     * @return string Use custom SoapClient class
     */
    public function getSoapClass(): string
    {
        return SoapClient::class;
    }

    /**
     * @return string
     */
    public function getInbox(): string
    {
        return 'Inbox';
    }

    /**
     * @return string
     */
    protected function getWsdlUrl(): string
    {
        return 'CfeService.svc?wsdl';
    }

    /**
     * @return string
     */
    abstract protected function getResponseClass(): string;

    /**
     * Get most recent XML Request sent to SOAP server
     *
     * @return string
     */
    public function getLastRequestXml(): ?string
    {
        return $this->client->__getLastRequest();
    }

    /**
     * Get headers of last request
     *
     * @return string|null
     */
    public function getLastRequestHeaders(): ?string
    {
        return $this->client->__getLastRequestHeaders();
    }

    /**
     * Get most recent XML Response returned from SOAP server
     *
     * @return string
     */
    public function getLastResponseXml(): ?string
    {
        return $this->client->__getLastResponse();
    }
}

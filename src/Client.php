<?php

namespace PlanetaDelEste\Ucfe;

use Illuminate\Support\Arr;
use SoapClient;

abstract class Client
{
    const URL = 'CfeService.svc?wsdl';

    /** @var SoapClient */
    protected $client;

    /** @var bool Add "Inbox" part to ws url */
    protected $inbox = true;

    /** @var \PlanetaDelEste\Ucfe\WsseAuthHeader */
    protected $auth;

    public static function __callStatic(string $name, array $arArgs = [])
    {
        if (method_exists(get_called_class(), $name)) {
            return call_user_func_array([new static(), $name], $arArgs);
        }
    }

    /**
     * @param array $arParams   = [
     *                          'req' => [
     *                          'Req' => [
     *                          'CodComercio' => '',
     *                          'CodTerminal' => '',
     *                          'FechaReq' => '',
     *                          'HoraReq' => '',
     *                          'RutEmisor' => '',
     *                          'TipoMensaje' => '',
     *                          'CfeXmlOTexto' => '',
     *                          'Adenda' => '',
     *                          'Certificado' => '',
     *                          'CifrarComplementoFiscal' => '',
     *                          'CodRta' => '',
     *                          'DatosQr' => '',
     *                          'EmailEnvioPdfReceptor' => '',
     *                          'EstadoSituacion' => '',
     *                          'IdReq' => '',
     *                          'Impresora' => '',
     *                          'NumeroCfe' => '',
     *                          'RechCom' => '',
     *                          'Serie' => '',
     *                          'TipoCfe' => '',
     *                          'TipoMensaje' => '',
     *                          'Uuid' => '',
     *                          ],
     *                          'RequestDate' => '',
     *                          'Tout' => '3000',
     *                          'ReqEnc' => '',
     *                          'CodComercio' => '',
     *                          'CodTerminal' => '',
     *                          ]]
     *
     * @return mixed
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

        $obResponse = $this->soap()->Invoke($arParams);
        $sResponseClass = $this->getResponseClass();

        return new $sResponseClass($obResponse);
    }

    /**
     * @return bool
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
     * @throws \Exception
     */
    protected function soap(array $arOptions = []): SoapClient
    {
        $arOptions = array_merge(['trace' => 1, "cache_wsdl" => WSDL_CACHE_NONE], $arOptions);
        $sSoapClass = $this->getSoapClass();
        $sUrl = sprintf('https://%s.ucfe.com.uy/', Auth::getUrl());
        if ($this->inbox) {
            $sUrl .= 'Inbox/';
        }

        $this->client = new $sSoapClass($sUrl.self::URL, $arOptions);
        $authHeader = new WsseAuthHeader(Auth::getUser(), Auth::getPassword());
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
    abstract protected function getResponseClass(): string;

}

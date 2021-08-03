<?php

namespace PlanetaDelEste\Ucfe;

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

    protected $sUsername;
    protected $sPassword;
    protected $sCodComercio;
    protected $sCodTerminal;

    /**
     * Set UCFE username
     * @param string $sValue
     *
     * @return $this
     */
    public function setUser(string $sValue): self
    {
        $this->sUsername = $sValue;

        return $this;
    }

    /**
     * Set UCFE password
     * @param string $sValue
     *
     * @return $this
     */
    public function setPassword(string $sValue): self
    {
        $this->sPassword = $sValue;

        return $this;
    }

    /**
     * Set UCFE CodComercio value
     * @param string $sValue
     *
     * @return $this
     */
    public function setCodComercio(string $sValue): self
    {
        $this->sCodComercio = $sValue;

        return $this;
    }

    /**
     * Set UCFE CodTerminal value
     * @param string $sValue
     *
     * @return $this
     */
    public function setCodTerminal(string $sValue): self
    {
        $this->sCodTerminal = $sValue;

        return $this;
    }

    /**
     * @return string
     */
    abstract public function getResponseClass(): string;

    /**
     * @param array $arOptions
     *
     * @return SoapClient
     * @throws \Exception
     */
    protected function soap(array $arOptions = []): SoapClient
    {
        $this->validateAuth();

        $arOptions = array_merge(['trace' => 1, "cache_wsdl" => WSDL_CACHE_NONE], $arOptions);
        $sService = $this->getService();
        $sSoapClass = $this->getSoapClass();
        $sUrl = 'https://'.$sService.'/';
        if ($this->inbox) {
            $sUrl .= 'Inbox/';
        }

        $this->client = new $sSoapClass($sUrl.self::URL, $arOptions);
        $authHeader = new WsseAuthHeader($this->sUsername, $this->sPassword);
        $this->client->__setSoapHeaders([$authHeader]);

        return $this->client;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function validateAuth(): bool
    {
        if (!$this->sUsername) {
            throw new \Exception('Username is required');
        }

        if (!$this->sPassword) {
            throw new \Exception('Password is required');
        }

        if (!$this->sCodTerminal) {
            throw new \Exception('The field CodTerminal is required');
        }

        if (!$this->sCodComercio) {
            throw new \Exception('The field CodComercio is required');
        }

        return true;
    }

    /**
     * @return string Name of service
     */
    abstract public function getService(): string;

    /**
     * @return string Use custom SoapClient class
     */
    public function getSoapClass(): string
    {
        return SoapClient::class;
    }
}

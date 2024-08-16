<?php

namespace PlanetaDelEste\Ucfe\Service;

use Illuminate\Support\Arr;
use PlanetaDelEste\Ucfe\Client;
use PlanetaDelEste\Ucfe\Service\CfeResponse;
use Ramsey\Uuid\Uuid;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * @method string getType()
 * @method array getData(bool $bForce = false)
 */
class CfeClient extends Client
{
    public const CFE_CREDIT_NOTE = 'nc';
    public const CFE_DEBIT_NOTE  = 'nd';

    protected int $iTipoMensaje = 820;

    /**
     * @var string|null Customer email to be used on field EmailEnvioPdfReceptor
     */
    protected ?string $sCustomerEmail = null;

    /**
     * @var string|null Adenda
     */
    protected ?string $sAdenda = null;

    protected bool $bContingency = false;

    /**
     * @return CfeResponse
     *
     * @throws \Exception
     */
    public function signAndSend(): CfeResponse
    {
        $this->iTipoMensaje = 310;

        return $this->send();
    }

    /**
     * @return CfeResponse
     *
     * @throws \Exception
     */
    public function validate(): CfeResponse
    {
        $this->iTipoMensaje = 350;

        return $this->send();
    }

    /**
     * @return CfeResponse
     *
     * @throws \Exception
     */
    public function cert(): CfeResponse
    {
        $this->iTipoMensaje = 210;

        return $this->send();
    }

    /**
     * @return CfeResponse
     *
     * @throws \Exception
     */
    public function send(): CfeResponse
    {
        $arData = [
            'CfeXmlOTexto' => $this->xml(),
            'TipoCfe'      => Arr::get($this->getData(), 'Encabezado.IdDoc.TipoCFE'),
            'IdReq'        => 1,
            'Uuid'         => Uuid::uuid4()->toString(),
        ];

        if ($sCustomerEmail = $this->getCustomerEmail()) {
            $arData['EmailEnvioPdfReceptor'] = $sCustomerEmail;
        }

        if ($sAdenda = $this->getAdenda()) {
            $arData['Adenda'] = $sAdenda;
        }

        return $this->exec($arData);
    }

    public function xml(): string
    {
        $arXmlData = [$this->getType() => $this->getData(true)];
        $arRoot    = [
            'rootElementName' => 'CFE',
            '_attributes'     => [
                'xmlns'     => 'http://cfe.dgi.gub.uy',
                'version'   => '1.0',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            ],
        ];

        return ArrayToXml::convert($arXmlData, $arRoot, true, 'UTF-8');
    }

    /**
     * @return CfeResponse
     *
     * @throws \Exception
     */
    public function range(): CfeResponse
    {
        $this->iTipoMensaje = 220;

        return $this->send();
    }

    /**
     * @param string $sValue
     *
     * @return $this
     */
    public function setCustomerEmail(string $sValue): self
    {
        $this->sCustomerEmail = $sValue;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return !empty($this->sCustomerEmail) ? $this->sCustomerEmail : null;
    }

    /**
     * Set invoice as contingency. CFE code starts with 2
     * @param bool $bValue
     *
     * @return $this
     */
    public function setContingency(bool $bValue): self
    {
        $this->bContingency = $bValue;

        return $this;
    }

    /**
     * Get invoice as contingency. CFE code starts with 2
     * @return bool
     */
    public function getContingency(): bool
    {
        return $this->bContingency;
    }

    /**
     * @param string $sValue
     *
     * @return $this
     */
    public function setAdenda(string $sValue): self
    {
        $this->sAdenda = trim($sValue);

        return $this;
    }

    /**
     * @param string $sValue
     *
     * @return $this
     */
    public function addAdenda(string $sValue): self
    {
        if (str_contains($this->getAdenda(), $sValue)) {
            return $this;
        }

        $sPrefix       = $this->getAdenda() ? "\n\r" : '';
        $sValue        = $sPrefix.trim($sValue);
        $this->sAdenda = $this->getAdenda() ? $this->getAdenda().$sValue : $sValue;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdenda(): ?string
    {
        return $this->sAdenda ?? null;
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return $this->iTipoMensaje;
    }

    protected function getResponseClass(): string
    {
        return CfeResponse::class;
    }
}

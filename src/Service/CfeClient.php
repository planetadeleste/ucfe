<?php

namespace PlanetaDelEste\Ucfe\Service;

use Illuminate\Support\Arr;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Emisor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\IdDoc;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Receptor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales;
use PlanetaDelEste\Ucfe\Cfe\MediosPago\MedioPago;
use PlanetaDelEste\Ucfe\Client;
use PlanetaDelEste\Ucfe\Service\CfeResponse;
use Ramsey\Uuid\Uuid;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * @method void setData()
 * @method void setTotals()
 * @method Totales getTotals()
 * @method void removeItem(string $sVal = 'Redondeo', string $sKey = 'NomItem')
 * @method float|int calculateTotal(bool $withNf = true)
 * @method array getItems()
 * @method void setRoundedTotal(bool $force = false)
 * @method IdDoc idDoc()
 * @method Emisor emisor()
 * @method Receptor receptor()
 * @method Totales totales()
 * @method static setEmisor(Emisor $obEmisor)
 * @method static setReceptor(Receptor $obReceptor)
 * @method static addItem(Item $obItem)
 * @method static addAmount(float $fValue, string $sMntKey = 'MntIVATasaBasica', bool $decrease = false, ?float $fTax = null)
 * @method static addAmount(float $fValue, string $sMntKey = 'MntIVATasaBasica', bool $decrease = false, ?float $fTax = null)
 * @method static addSubTotInfo(SubTotInfo $obSubTotInfo)
 * @method static addDscRcgGlobal(DscRcgGlobal $obDscRcgGlobal)
 * @method static addGlobalDiscount(DRG_Item $obItem)
 * @method static addMediosPago(MediosPago $obMediosPago)
 * @method static addMedioPago(MedioPago $obMedioPago)
 * @method static addReferencia(Referencia\Referencia $obReferencia)
 * @method static addCAEData(CAEData $obCAEData)
 * @method static addComplFiscal(Compl_Fiscal $obComplFiscal)
 * @method static addMntIVAOtra(float $fValue, bool $decrease = false, float $fTax = null)
 * @method static addMntIVATasaBasica(float $fValue, bool $decrease = false, float $fTax = null)
 * @method static addMntIVATasaMin(float $fValue, bool $decrease = false, float $fTax = null)
 * @method static addMntIVAenSusp(float $fValue, bool $decrease = false, float $fTax = null)
 * @method static addMntTotRetenido(float $fValue, bool $decrease = false, float $fTax = null)
 * @method static addMntTotCredFisc(float $fValue, bool $decrease = false, float $fTax = null)
 * @method static setCreditNote()
 * @method static setDebitNote()
 * @method bool isDisableRounding()
 * @method void setDisableRounding(bool $disableRounding)
 * @method int getTipoCFE()
 * @method string getType()
 * @method void setResgTotals(Totales $obTotales)
 * @method void setExportTotals(Totales $obTotales, float $fTotal)
 * @method void sort()
 */
class CfeClient extends Client
{
    public const string CFE_CREDIT_NOTE = 'nc';
    public const string CFE_DEBIT_NOTE  = 'nd';

    /**
     * @var int
     */
    protected int $iTipoMensaje = 820;

    /**
     * @var string|null Customer email to be used on field EmailEnvioPdfReceptor
     */
    protected ?string $sCustomerEmail = null;

    /**
     * @var string|null Adenda
     */
    protected ?string $sAdenda = null;

    /**
     * @var bool
     */
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

    /**
     * @return string
     */
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
     *
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
     *
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
     * @return int
     */
    protected function getTipoMensaje(): int
    {
        return $this->iTipoMensaje;
    }

    /**
     * @return string
     */
    protected function getResponseClass(): string
    {
        return CfeResponse::class;
    }
}

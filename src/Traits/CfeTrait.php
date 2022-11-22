<?php

namespace PlanetaDelEste\Ucfe\Traits;

use Illuminate\Support\Collection;
use PlanetaDelEste\Ucfe\Cfe\CAEData;
use PlanetaDelEste\Ucfe\Cfe\Compl_Fiscal;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item;
use PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Emisor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\IdDoc;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Receptor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales;
use PlanetaDelEste\Ucfe\Cfe\MediosPago;
use PlanetaDelEste\Ucfe\Cfe\Referencia;
use PlanetaDelEste\Ucfe\Cfe\SubTotInfo;
use PlanetaDelEste\Ucfe\Service\CfeClient;

/**
 * @method self addMntIVAOtra(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntIVATasaBasica(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntIVATasaMin(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntIVAenSusp(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntTotRetenido(float $fValue, bool $decrease = false, float $fTax = null)
 */
trait CfeTrait
{
    /** @var array Final XML data */
    protected $arData = [];

    /** @var array */
    protected $arEncabezado = [
        'IdDoc'    => null,
        'Emisor'   => null,
        'Receptor' => null,
        'Totales'  => null,
    ];

    /** @var array[] */
    protected $arDetalle = [
        'Item' => []
    ];

    /** @var array */
    protected $arExtraData = [];

    /** @var float[] Set totals */
    protected $arTotals = [
        'MntIVAOtra'           => 0,
        'MntIVATasaBasica'     => 0,
        'MntIVATasaMin'        => 0,
        'MntIVAenSusp'         => 0,
        'MntNetoIVAOtra'       => 0,
        'MntNetoIVATasaBasica' => 0,
        'MntNetoIvaTasaMin'    => 0,
        'MntTotRetenido'       => 0,
        'MntNoGrv'             => 0,
    ];

    protected $rules = [
        'Encabezado.IdDoc'    => 'required',
        'Encabezado.Emisor'   => 'required',
        'Encabezado.Receptor' => 'required',
        'Encabezado.Totales'  => 'required',
    ];

    protected $arSortKeys = [
        'Encabezado',
        'Detalle',
        'SubTotInfo',
        'DscRcgGlobal',
        'MediosPago',
        'Referencia',
        'CAEData',
        'Compl_Fiscal',
    ];

    /** @var string Nota de Crédito|Débito [nc|nb] */
    protected $noteType = null;

    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param bool $bForce
     *
     * @return array
     * @throws \ValidationException
     */
    public function getData(bool $bForce = false): array
    {
        if (empty($this->arData) || $bForce) {
            $this->setData();
        }

        return $this->arData;
    }

    /**
     * @return void
     * @throws \ValidationException
     */
    public function setData(): void
    {
        $this->arData = [];
        $this->setTotals();

        /** @var IdDoc $obIdDoc */
        $obIdDoc = $this->arEncabezado['IdDoc'];
        if ($obIdDoc) {
            $obIdDoc->TipoCFE = $this->getTipoCFE();
        }

        $this->arData['Encabezado'] = Collection::make($this->arEncabezado)
            ->filter(function ($obVal) {
                return !empty($obVal) && (is_object($obVal) || is_array($obVal));
            })
            ->map(function ($obVal) {
                return is_object($obVal) ? $obVal->toArray() : $obVal;
            })
            ->all();

        $this->arData['Detalle'] = $this->arDetalle;

        if (!empty($this->arExtraData)) {
            foreach ($this->arExtraData as $sKey => $arData) {
                if (empty($arData)) {
                    continue;
                }
                $this->arData[$sKey] = $arData;
            }
        }

        $this->sort();

        $obValidator = \Validator::make($this->arData, $this->getRules());
        if (!$obValidator->passes()) {
            throw new \ValidationException($obValidator);
        }
    }

    public function calculateTotal()
    {
        $fTotal = 0;

        /** @var Totales $obTotales */
        if (!$obTotales = $this->getTotals()) {
            return $fTotal;
        }

        // Sum items value
        foreach ($this->getItems() as $arItem) {
            if (!isset($arItem['IndFact']) || !in_array((int)$arItem['IndFact'], [1, 16])) {
                continue;
            }

            $fTotal += $arItem['MontoItem'];
        }

        if ($this->arTotals['MntIVATasaMin']) {
            $fTotal += $this->arTotals['MntNetoIvaTasaMin'] + $this->arTotals['MntIVATasaMin'];
        }

        if ($this->arTotals['MntIVATasaBasica']) {
            $fTotal += $this->arTotals['MntNetoIVATasaBasica'] + $this->arTotals['MntIVATasaBasica'];
        }

        if ($this->arTotals['MntNetoIVAOtra']) {
            $fTotal += $this->arTotals['MntNetoIVAOtra'] + $this->arTotals['MntIVAOtra'];
        }

        // Find and apply discounts
        $arDiscounts = array_get($this->arExtraData, 'DscRcgGlobal.DRG_Item', []);
        if (!empty($arDiscounts)) {
            foreach ($arDiscounts as $arDiscountItem) {
                if ((int)$arDiscountItem['IndFactDR'] !== 1) {
                    continue;
                }

                $fValue = (float)$arDiscountItem['ValorDR'];
                if ($arDiscountItem['TpoMovDR'] === 'D') {
                    $fTotal -= $fValue;
                } else {
                    $fTotal += $fValue;
                }
            }
        }

        /** @var IdDoc $obIdDoc */
        if (($obIdDoc = $this->arEncabezado['IdDoc']) && $obIdDoc->IndCobPropia) {
            foreach ($this->getItems() as $arItem) {
                if (isset($arItem['IndFact']) && (int)$arItem['IndFact'] === 7) {
                    $fTotal -= $arItem['MontoItem'];
                } else {
                    $fTotal += $arItem['MontoItem'];
                }
            }
        }

        return $fTotal;
    }

    /**
     * @param bool $force
     *
     * @return void
     */
    public function setRoundedTotal(bool $force = false): void
    {
        /** @var Totales $obTotales */
        if ((!$obTotales = $this->getTotals()) || !$obTotales->MntTotal) {
            return;
        }

        // Calculate rounded price
        $fPriceRounded = round($obTotales->MntTotal);

        // Add final price and rounded difference
        if (!$obTotales->hasAttribute('MntPagar')) {
            $obTotales->MntPagar = $fPriceRounded;
        }

        if ($force === true || !$obTotales->hasAttribute('MontoNF')) {
            $obTotales->MontoNF = round($obTotales->MntPagar - $obTotales->MntTotal, 2);
        }

        // Add Item with round value
        if ($obTotales->MontoNF) {
            $obItem = new Item();
            // Indicador de Facturación (Item_Det_Fact)
            // 6: Producto o servicio	no facturable
            // 7: Producto o servicio no facturable negativo
            $obItem->IndFact = $obTotales->MontoNF > 0 ? 6 : 7;
            $obItem->NomItem = 'Redondeo';
            $obItem->Cantidad = 1;
            $obItem->PrecioUnitario = abs($obTotales->MontoNF);
            $this->addItem($obItem);
        }
    }

    public function setTotals(): void
    {
        /** @var Totales $obTotales */
        if (!$obTotales = $this->getTotals()) {
            return;
        }

        if (!$obTotales->hasAttribute('MontoNF')) {
            $this->removeItem();
        }

        // Tasa Minima
        $fTotal = $this->calculateTotal();

        // Resguardo
        if ($this->getTipoCFE() === 182) {
            if ($this->arTotals['MntTotRetenido']) {
                $obTotales->MntTotRetenido = $this->arTotals['MntTotRetenido'];
                $obTotales->CantLinDet = count($this->getItems());
            }

            $arRetenc = [];
            foreach ($this->getItems() as $arItem) {
                if (!isset($arItem['RetencPercep'])) {
                    continue;
                }

                foreach ($arItem['RetencPercep'] as $arItemRetenc) {
                    $sCode = $arItemRetenc['CodRet'];
                    $sValue = $arItemRetenc['ValRetPerc'];

                    if (!isset($arRetenc[$sCode])) {
                        $arRetenc[$sCode] = new Totales\RetencPercep();
                        $arRetenc[$sCode]->CodRet = $sCode;
                        $arRetenc[$sCode]->ValRetPerc = $sValue;
                    } else {
                        $arRetenc[$sCode]->ValRetPerc += $sValue;
                    }
                }
            }

            $obTotales->RetencPercep = array_map(function (Totales\RetencPercep $obReten) {
                return $obReten->toArray();
            }, array_values($arRetenc));
            return;
        }

        // Set initial value
        $obTotales->MntNoGrv = 0;

        // Find items with IndFact = 1|16
        foreach ($this->getItems() as $arItem) {
            if (!isset($arItem['IndFact']) || !in_array((int)$arItem['IndFact'], [1, 16])) {
                continue;
            }

            $obTotales->MntNoGrv += $arItem['MontoItem'];
        }

        // Apply discounts over MntNoGrv
        $arDiscounts = array_get($this->arExtraData, 'DscRcgGlobal.DRG_Item', []);
        if (!empty($arDiscounts)) {
            foreach ($arDiscounts as $arDiscountItem) {
                if ((int)$arDiscountItem['IndFactDR'] !== 1) {
                    continue;
                }

                $fValue = (float)$arDiscountItem['ValorDR'];
                if ($arDiscountItem['TpoMovDR'] === 'D') {
                    $obTotales->MntNoGrv -= $fValue;
                } else {
                    $obTotales->MntNoGrv += $fValue;
                }
            }
        }

        if ($this->arTotals['MntIVATasaMin']) {
            $obTotales->MntIVATasaMin = $this->arTotals['MntIVATasaMin'];
            $obTotales->MntNetoIvaTasaMin = $this->arTotals['MntNetoIvaTasaMin'];
        }

        if ($this->arTotals['MntIVATasaBasica']) {
            $obTotales->MntIVATasaBasica = $this->arTotals['MntIVATasaBasica'];
            $obTotales->MntNetoIVATasaBasica = $this->arTotals['MntNetoIVATasaBasica'];
        }

        if ($this->arTotals['MntNetoIVAOtra']) {
            $obTotales->MntIVAOtra = $this->arTotals['MntIVAOtra'];
            $obTotales->MntNetoIVAOtra = $this->arTotals['MntNetoIVAOtra'];
        }

        /** @var IdDoc $obIdDoc */
        $obIdDoc = $this->arEncabezado['IdDoc'];
        if ($obIdDoc->IndCobPropia) {
            $obTotales->MntTotal = 0;
            $obTotales->MntPagar = $fTotal;
            $obTotales->MontoNF = $fTotal;
        } else {
            // Add total amount
            if (!$obTotales->hasAttribute('MntTotal')) {
                $obTotales->MntTotal = $fTotal > 0 ? round($fTotal, 2) : 0;
            }

            // Check total amount to add rounded value
            if (!$obTotales->hasAttribute('MntPagar') && !$obTotales->hasAttribute('MontoNF')) {
                // Calculate rounded price
                $this->setRoundedTotal();
            }
        }


        $obTotales->CantLinDet = count($this->arDetalle['Item']);
    }

    /**
     * @return Totales|null
     */
    public function getTotals(): ?Totales
    {
        return $this->arEncabezado['Totales'];
    }

    public function removeItem($sVal = 'Redondeo', $sKey = 'NomItem'): void
    {
        foreach ($this->arDetalle['Item'] as $iKey => $arItem) {
            if (isset($arItem[$sKey]) && $arItem[$sKey] == $sVal) {
                unset($this->arDetalle['Item'][$iKey]);
            }
        }
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\Detalle\Item $obItem
     *
     * @return $this
     */
    public function addItem(Item $obItem): self
    {
        $obItem->NroLinDet = count($this->arDetalle['Item']) + 1;
        $arItem = $obItem->toArray();

        // Resguardo
        if ($this->getTipoCFE() === 182) {
            if (isset($arItem['Cantidad'])) {
                unset($arItem['Cantidad']);
            }

            if (isset($arItem['UniMed'])) {
                unset ($arItem['UniMed']);
            }
        }

        $this->arDetalle['Item'][] = $arItem;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->arDetalle['Item'];
    }

    /**
     * @param string $sName
     * @param array  $arguments
     *
     * @return self
     * @throws \Exception
     */
    public function __call(string $sName, array $arguments)
    {
        if (substr($sName, 0, 3) == 'add' && !empty($arguments)) {
            $sMntKey = substr($sName, 3);
            if (array_keys($this->arTotals, $sMntKey)) {
                $decrease = isset($arguments[1]) && (bool)$arguments[1];
                $fTax = isset($arguments[2]) ? (float)$arguments[2] : null;
                return $this->addAmount($arguments[0], $sMntKey, $decrease, $fTax);
            }
        }

        throw new \Exception('Method '.$sName.' does not exits');
    }

    public function addAmount(
        float  $fValue,
        string $sMntKey = 'MntIVATasaBasica',
        bool   $decrease = false,
        float  $fTax = null
    ): self {
        $obTotals = $this->getTotals();
        $sMntNetoKey = 'MntNeto'.substr($sMntKey, 3);

        if ($sMntKey === 'MntIVATasaBasica' && $obTotals) {
            $fTax = (float)$obTotals->IVATasaBasica;
        } elseif ($sMntKey === 'MntIVATasaMin' && $obTotals) {
            $fTax = (float)$obTotals->IVATasaMin;
            $sMntNetoKey = 'MntNetoIvaTasaMin';
        }

        if (isset($fTax) && array_key_exists($sMntNetoKey, $this->arTotals)) {
            $fMntValue = round($fValue, 2);

            if ($decrease) {
                $this->arTotals[$sMntNetoKey] -= $fMntValue;
            } else {
                $this->arTotals[$sMntNetoKey] += $fMntValue;
            }
            $fValue = round($fValue * ($fTax / 100), 2);
        }

        if ($decrease) {
            $this->arTotals[$sMntKey] -= $fValue;
        } else {
            $this->arTotals[$sMntKey] += $fValue;
        }

        return $this;
    }

    /**
     * Get CFE definition type
     *
     * @return string eTck|eFact|eFact_Exp|eRem|eRem_Exp|eResg|eBoleta
     */
    abstract public function getType(): string;

    /**
     * CFE type
     * 101 e-Ticket
     * 102 Nota de Crédito de e-Ticket
     * 103 Nota de Débito de e-Ticket
     * 111 e-Factura
     * 112 Nota de Crédito de e-Factura
     * 113 Nota de Débito de e-Factura
     * 181 e-Remito
     * 182 e-Resguardo
     * 121 e-Factura Exportación
     * 122 Nota de Crédito de e-Factura Exportación
     * 123 Nota de Débito de e-Factura Exportación
     * 124 e-Remito de Exportación
     * 131 e-Ticket Venta por Cuenta Ajena
     * 132 Nota de Crédito de e-Ticket Venta por Cuenta Ajena
     * 133 Nota de Débito de e-Ticket Venta por Cuenta Ajena
     * 141 e-Factura Venta por Cuenta Ajena
     * 142 Nota de Crédito de e-Factura Venta por Cuenta Ajena
     * 143 Nota de Débito de e-Factura Venta por Cuenta Ajena
     * 151 e-Boleta de entrada
     * 152 Nota de crédito de e-Boleta de entrada
     * 153 Nota de débito de e-Boleta de entrada
     *
     * @return int
     */
    abstract public function getTipoCFE(): int;

    public function idDoc(): IdDoc
    {
        $obIdDoc = new IdDoc();
        $obIdDoc->TipoCFE = $this->getTipoCFE();
        return $this->arEncabezado['IdDoc'] = $obIdDoc;
    }

    public function emisor(): Emisor
    {
        return $this->arEncabezado['Emisor'] = new Emisor();
    }

    /**
     * Set Emisor object
     *
     * @param \PlanetaDelEste\Ucfe\Cfe\Encabezado\Emisor $obEmisor
     *
     * @return $this
     */
    public function setEmisor(Emisor $obEmisor): self
    {
        $this->arEncabezado['Emisor'] = $obEmisor;

        return $this;
    }

    public function receptor(): Receptor
    {
        return $this->arEncabezado['Receptor'] = new Receptor();
    }

    /**
     * Set Receptor object
     *
     * @param \PlanetaDelEste\Ucfe\Cfe\Encabezado\Receptor $obReceptor
     *
     * @return $this
     */
    public function setReceptor(Receptor $obReceptor): self
    {
        $this->arEncabezado['Receptor'] = $obReceptor;

        return $this;
    }

    public function totales(): Totales
    {
        return $this->arEncabezado['Totales'] = new Totales();
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\SubTotInfo $obSubTotInfo
     *
     * @return $this
     */
    public function addSubTotInfo(SubTotInfo $obSubTotInfo): self
    {
        $this->arExtraData['SubTotInfo'] = $obSubTotInfo->toArray();

        return $this;
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal $obDscRcgGlobal
     *
     * @return $this
     */
    public function addDscRcgGlobal(DscRcgGlobal $obDscRcgGlobal): self
    {
        $this->arExtraData['DscRcgGlobal'] = $obDscRcgGlobal->toArray();

        return $this;
    }

    /**
     * Add new discount
     *
     * @param \PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal\DRG_Item $obItem
     *
     * @return $this
     */
    public function addGlobalDiscount(DscRcgGlobal\DRG_Item $obItem): self
    {
        if (!isset($this->arExtraData['DscRcgGlobal'])) {
            $this->arExtraData['DscRcgGlobal'] = ['DRG_Item' => []];
        }

        $sMntKey = 'MntIVAOtra';
        if ($obItem->IndFactDR == '3') {
            $sMntKey = 'MntIVATasaBasica';
        } elseif ($obItem->IndFactDR == '2') {
            $sMntKey = 'MntIVATasaMin';
        }

        $this->addAmount($obItem->ValorDR, $sMntKey, $obItem->TpoMovDR === 'D');

        $obItem->NroLinDR = count($this->arExtraData['DscRcgGlobal']['DRG_Item']) + 1;
        $this->arExtraData['DscRcgGlobal']['DRG_Item'][] = $obItem->toArray();

        return $this;
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\MediosPago $obMediosPago
     *
     * @return $this
     */
    public function addMediosPago(MediosPago $obMediosPago): self
    {
        $this->arExtraData['MediosPago'] = $obMediosPago->toArray();

        return $this;
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\MediosPago\MedioPago $obMedioPago
     *
     * @return $this
     */
    public function addMedioPago(MediosPago\MedioPago $obMedioPago): self
    {
        if (!isset($this->arExtraData['MediosPago'])) {
            $this->arExtraData['MediosPago'] = ['MedioPago' => []];
        }
        $obMedioPago->NroLinMP = count($this->arExtraData['MediosPago']['MedioPago']) + 1;
        $arMedioPago = $obMedioPago->toArray();
        $this->arExtraData['MediosPago']['MedioPago'][] = $arMedioPago;

        return $this;
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\Referencia\Referencia $obReferencia
     *
     * @return $this
     */
    public function addReferencia(Referencia\Referencia $obReferencia): self
    {
        if (!isset($this->arExtraData['Referencia'])) {
            $this->arExtraData['Referencia'] = ['Referencia' => []];
        }

        $obReferencia->NroLinRef = count($this->arExtraData['Referencia']['Referencia']) + 1;
        $this->arExtraData['Referencia']['Referencia'][] = $obReferencia->toArray();

        return $this;
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\CAEData $obCAEData
     *
     * @return $this
     */
    public function addCAEData(CAEData $obCAEData): self
    {
        $this->arExtraData['CAEData'] = $obCAEData;

        return $this;
    }

    /**
     * @param \PlanetaDelEste\Ucfe\Cfe\Compl_Fiscal $obComplFiscal
     *
     * @return $this
     */
    public function addComplFiscal(Compl_Fiscal $obComplFiscal): self
    {
        $this->arExtraData['Compl_Fiscal'] = $obComplFiscal->toArray();

        return $this;
    }

    /**
     * @return $this
     */
    public function setCreditNote(): self
    {
        $this->noteType = CfeClient::CFE_CREDIT_NOTE;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDebitNote(): self
    {
        $this->noteType = CfeClient::CFE_DEBIT_NOTE;

        return $this;
    }

    protected function sort(): void
    {
        $arData = $this->arData;
        $arKeys = array_flip($this->arSortKeys);
        $result = array_replace($arKeys, $arData);

        $this->arData = array_intersect_key($result, $arData);
    }
}

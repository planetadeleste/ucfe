<?php

namespace PlanetaDelEste\Ucfe\Traits;

use Illuminate\Support\Collection;
use PlanetaDelEste\GW\Classes\Helper\PriceHelper;
use PlanetaDelEste\Ucfe\Cfe\CAEData;
use PlanetaDelEste\Ucfe\Cfe\Compl_Fiscal;
use PlanetaDelEste\Ucfe\Cfe\Detalle\Item;
use PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal;
use PlanetaDelEste\Ucfe\Cfe\DscRcgGlobal\DRG_Item;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Emisor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\IdDoc;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Receptor;
use PlanetaDelEste\Ucfe\Cfe\Encabezado\Totales;
use PlanetaDelEste\Ucfe\Cfe\MediosPago;
use PlanetaDelEste\Ucfe\Cfe\MediosPago\MedioPago;
use PlanetaDelEste\Ucfe\Cfe\Referencia;
use PlanetaDelEste\Ucfe\Cfe\SubTotInfo;
use PlanetaDelEste\Ucfe\Service\CfeClient;
use PlanetaDelEste\Ucfe\Service\EBoleta;
use PlanetaDelEste\Ucfe\Service\EFact;
use PlanetaDelEste\Ucfe\Service\EFactExp;
use PlanetaDelEste\Ucfe\Service\ERem;
use PlanetaDelEste\Ucfe\Service\ERemExp;
use PlanetaDelEste\Ucfe\Service\EResg;
use PlanetaDelEste\Ucfe\Service\ETck;

/**
 * @method self addMntIVAOtra(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntIVATasaBasica(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntIVATasaMin(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntIVAenSusp(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntTotRetenido(float $fValue, bool $decrease = false, float $fTax = null)
 * @method self addMntTotCredFisc(float $fValue, bool $decrease = false, float $fTax = null)
 */
trait CfeTrait
{
    /** @var array Final XML data */
    protected array $arData = [];

    /**
     * @var bool Force to skip round totals
     */
    protected bool $disableRounding = false;

    /** @var array */
    protected array $arEncabezado = [
        'IdDoc'    => null,
        'Emisor'   => null,
        'Receptor' => null,
        'Totales'  => null,
    ];

    /** @var array<array> */
    protected array $arDetalle = [
        'Item' => []
    ];

    /** @var array */
    protected array $arExtraData = [];

    /** @var array<float> Set totals */
    protected array $arTotals = [
        'MntIVAOtra'           => 0,
        'MntIVATasaBasica'     => 0,
        'MntIVATasaMin'        => 0,
        'MntIVAenSusp'         => 0,
        'MntNetoIVAOtra'       => 0,
        'MntNetoIVATasaBasica' => 0,
        'MntNetoIvaTasaMin'    => 0,
        'MntTotRetenido'       => 0,
        'MntTotCredFisc'       => 0,
        'MntNoGrv'             => 0,
    ];

    protected array $rules = [
        'Encabezado.IdDoc'    => 'required',
        'Encabezado.Emisor'   => 'required',
        'Encabezado.Receptor' => 'required',
        'Encabezado.Totales'  => 'required',
    ];

    protected array $arSortKeys = [
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
    protected ?string $noteType = null;

    /**
     * @param bool $bForce
     *
     * @return array
     *
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
     *
     * @throws \ValidationException
     */
    public function setData(): void
    {
        $this->arData = [];
        $this->setTotals();

        /** @var IdDoc $obIdDoc */
        $obIdDoc = $this->arEncabezado['IdDoc'];

        if ($obIdDoc) {
            $obIdDoc->TipoCFE = $this->getFinalCFECode();
        }

        $this->arData['Encabezado'] = Collection::make($this->arEncabezado)
            ->filter(static function ($obVal) {
                return !empty($obVal) && (is_object($obVal) || is_array($obVal));
            })
            ->map(static function ($obVal) {
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
            $this->setResgTotals($obTotales);

            return;
        }

        // Factura de Exportación
        if (in_array($this->getTipoCFE(), [121, 122, 123])) {
            $this->setExportTotals($obTotales, $fTotal);

            return;
        }

        // Eremito y Eremito de Exportación
        if (in_array($this->getTipoCFE(), [181, 124])) {
            $obTotales->CantLinDet = count($this->arDetalle['Item']);

            return;
        }

        // Set initial value
        $obTotales->MntNoGrv = 0;

        // Find items with IndFact = 1|16
        foreach ($this->getItems() as $arItem) {
            if (!isset($arItem['IndFact']) || !in_array((int) $arItem['IndFact'], [1, 16])) {
                continue;
            }

            $obTotales->MntNoGrv += $arItem['MontoItem'];
        }

        // Apply discounts over MntNoGrv
        $arDiscounts = array_get($this->arExtraData, 'DscRcgGlobal.DRG_Item', []);

        if (!empty($arDiscounts)) {
            foreach ($arDiscounts as $arDiscountItem) {
                if ((int) $arDiscountItem['IndFactDR'] !== 1) {
                    continue;
                }

                $fValue = (float) $arDiscountItem['ValorDR'];

                if ($arDiscountItem['TpoMovDR'] === 'D') {
                    $obTotales->MntNoGrv -= $fValue;
                } else {
                    $obTotales->MntNoGrv += $fValue;
                }
            }
        }

        if ($this->arTotals['MntIVATasaMin']) {
            $obTotales->MntIVATasaMin     = $this->arTotals['MntIVATasaMin'];
            $obTotales->MntNetoIvaTasaMin = $this->arTotals['MntNetoIvaTasaMin'];
        }

        if ($this->arTotals['MntIVATasaBasica']) {
            $obTotales->MntIVATasaBasica     = $this->arTotals['MntIVATasaBasica'];
            $obTotales->MntNetoIVATasaBasica = $this->arTotals['MntNetoIVATasaBasica'];
        }

        if ($this->arTotals['MntNetoIVAOtra']) {
            $obTotales->MntIVAOtra     = $this->arTotals['MntIVAOtra'];
            $obTotales->MntNetoIVAOtra = $this->arTotals['MntNetoIVAOtra'];
        }

        /** @var IdDoc $obIdDoc */
        $obIdDoc = $this->arEncabezado['IdDoc'];

        if ($obIdDoc->IndCobPropia) {
            $obTotales->MntTotal = 0;
            $obTotales->MntPagar = $fTotal;
            $obTotales->MontoNF  = $fTotal;
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

    /**
     * @param string $sVal
     * @param string $sKey
     *
     * @return void
     */
    public function removeItem(string $sVal = 'Redondeo', string $sKey = 'NomItem'): void
    {
        foreach ($this->arDetalle['Item'] as $iKey => $arItem) {
            if (!isset($arItem[$sKey]) || $arItem[$sKey] !== $sVal) {
                continue;
            }

            unset($this->arDetalle['Item'][$iKey]);
        }
    }

    /**
     * @param bool $withNf
     *
     * @return float|int|mixed
     */
    public function calculateTotal(bool $withNf = true)
    {
        $fTotal = 0;

        if (!$this->getTotals()) {
            return $fTotal;
        }

        // Sum items value
        $arIndFact = [1, 16, 10];

        if ($withNf) {
            $arIndFact = array_merge($arIndFact, [6, 7]);
        }

        foreach ($this->getItems() as $arItem) {
            if (!isset($arItem['IndFact']) || !in_array((int) $arItem['IndFact'], $arIndFact)) {
                continue;
            }

            if (isset($arItem['IndFact']) && (int) $arItem['IndFact'] === 7) {
                $fTotal -= $arItem['MontoItem'];
            } else {
                $fTotal += $arItem['MontoItem'];
            }
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
                if ((int) $arDiscountItem['IndFactDR'] !== 1) {
                    continue;
                }

                $fValue = (float) $arDiscountItem['ValorDR'];

                if ($arDiscountItem['TpoMovDR'] === 'D') {
                    $fTotal -= $fValue;
                } else {
                    $fTotal += $fValue;
                }
            }
        }

        /** @var IdDoc $obIdDoc */
        if (!$withNf && ($obIdDoc = $this->arEncabezado['IdDoc']) && $obIdDoc->IndCobPropia) {
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
     * @return array
     */
    public function getItems(): array
    {
        return $this->arDetalle['Item'];
    }

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

    protected function setResgTotals(Totales $obTotales): void
    {
        if ($this->getTipoCFE() !== 182) {
            return;
        }

        $obTotales->MntTotRetenido = $this->arTotals['MntTotRetenido'];

        if ($this->arTotals['MntTotCredFisc']) {
            $obTotales->MntTotCredFisc = $this->arTotals['MntTotCredFisc'];
        }

        $obTotales->CantLinDet = count($this->getItems());
        $arRetenc              = [];

        foreach ($this->getItems() as $arItem) {
            if (!isset($arItem['RetencPercep'])) {
                continue;
            }

            foreach ($arItem['RetencPercep'] as $arItemRetenc) {
                $sCode  = $arItemRetenc['CodRet'];
                $sValue = $arItemRetenc['ValRetPerc'];

                if (isset($arItem['IndFact']) && (int) $arItem['IndFact'] === 9 && $sValue > 0) {
                    $sValue = PriceHelper::negative($sValue);
                }

                if (!isset($arRetenc[$sCode])) {
                    $arRetenc[$sCode]             = new Totales\RetencPercep();
                    $arRetenc[$sCode]->CodRet     = $sCode;
                    $arRetenc[$sCode]->ValRetPerc = $sValue;
                } else {
                    $arRetenc[$sCode]->ValRetPerc += $sValue;
                }
            }
        }

        $obTotales->RetencPercep = array_map(static function (Totales\RetencPercep $obReten) {
            return $obReten->toArray();
        }, array_values($arRetenc));
    }

    /**
     * @param Totales $obTotales
     * @param float   $fTotal
     *
     * @return void
     */
    protected function setExportTotals(Totales $obTotales, float $fTotal): void
    {
        if (!in_array($this->getTipoCFE(), [121, 122, 123])) {
            return;
        }

        $obTotales->MntTotal     = $fTotal;
        $obTotales->MntPagar     = $fTotal;
        $obTotales->MntExpoyAsim = $fTotal;
        $obTotales->CantLinDet   = count($this->arDetalle['Item']);
    }

    /**
     * @param bool $force
     *
     * @return void
     */
    public function setRoundedTotal(bool $force = false): void
    {
        if ((!$obTotales = $this->getTotals()) || !$obTotales->MntTotal) {
            return;
        }

        // Calculate rounded price
        $fPriceRounded = $this->isDisableRounding() ? $obTotales->MntTotal : (int) round($obTotales->MntTotal);

        // Add final price and rounded difference
        if (!$obTotales->hasAttribute('MntPagar')) {
            $obTotales->MntPagar = $fPriceRounded;
        }

        if ($this->isDisableRounding()) {
            return;
        }

        if ($force || !$obTotales->hasAttribute('MontoNF')) {
            $fTotalNF = round($obTotales->MntPagar - $obTotales->MntTotal, 2);

            if ($obTotales->MontoNF) {
                $obTotales->MontoNF += $fTotalNF;
            } else {
                $obTotales->MontoNF = $fTotalNF;
            }
        }

        // Add Item with round value
        if (!isset($fTotalNF)) {
            return;
        }

        $obItem = new Item();
        // Indicador de Facturación (Item_Det_Fact)
        // 6: Producto o servicio   no facturable
        // 7: Producto o servicio no facturable negativo
        $obItem->IndFact        = $fTotalNF > 0 ? 6 : 7;
        $obItem->NomItem        = 'Redondeo';
        $obItem->Cantidad       = 1;
        $obItem->PrecioUnitario = abs($fTotalNF);
        $this->addItem($obItem);
    }

    /**
     * @param Item $obItem
     *
     * @return $this
     */
    public function addItem(Item $obItem): self
    {
        $obItem->NroLinDet = count($this->arDetalle['Item']) + 1;
        $arItem            = $obItem->toArray();

        // Resguardo
        if ($this->getTipoCFE() === 182) {
            if (isset($arItem['Cantidad'])) {
                unset($arItem['Cantidad']);
            }

            if (isset($arItem['UniMed'])) {
                unset($arItem['UniMed']);
            }
        }

        $this->arDetalle['Item'][] = $arItem;

        return $this;
    }

    /**
     * @return int
     */
    public function getFinalCFECode(): int
    {
        return $this->getContingency() ? $this->getTipoCFE() + 100 : $this->getTipoCFE();
    }

    /**
     * @return void
     */
    protected function sort(): void
    {
        $arData = $this->arData;
        $arKeys = array_flip($this->arSortKeys);
        $result = array_replace($arKeys, $arData);

        $this->arData = array_intersect_key($result, $arData);
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param string $sName
     * @param array  $arguments
     *
     * @return self
     *
     * @throws \Exception
     */
    public function __call(string $sName, array $arguments): self
    {
        if (str_starts_with($sName, 'add') && !empty($arguments)) {
            $sMntKey = substr($sName, 3);

            if (array_key_exists($sMntKey, $this->arTotals)) {
                $decrease = isset($arguments[1]) && (bool) $arguments[1];
                $fTax     = isset($arguments[2]) ? (float) $arguments[2] : null;

                return $this->addAmount($arguments[0], $sMntKey, $decrease, $fTax);
            }
        }

        throw new \Exception("Method {$sName} does not exits");
    }

    /**
     * @param float      $fValue
     * @param string     $sMntKey
     * @param bool       $decrease
     * @param float|null $fTax
     *
     * @return ETck|EBoleta|EFact|EFactExp|ERem|ERemExp|EResg|CfeTrait
     */
    public function addAmount(
        float $fValue,
        string $sMntKey = 'MntIVATasaBasica',
        bool $decrease = false,
        ?float $fTax = null
    ): self {
        $obTotals    = $this->getTotals();
        $sMntNetoKey = 'MntNeto'.substr($sMntKey, 3);

        if ('MntIVATasaBasica' === $sMntKey && $obTotals) {
            $fTax = (float) $obTotals->IVATasaBasica;
        } elseif ('MntIVATasaMin' === $sMntKey && $obTotals) {
            $fTax        = (float) $obTotals->IVATasaMin;
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
     * @return IdDoc
     */
    public function idDoc(): IdDoc
    {
        $obIdDoc                     = new IdDoc();
        $obIdDoc->TipoCFE            = $this->getFinalCFECode();
        $this->arEncabezado['IdDoc'] = $obIdDoc;

        return $this->arEncabezado['IdDoc'];
    }

    /**
     * @return Emisor
     */
    public function emisor(): Emisor
    {
        $this->arEncabezado['Emisor'] = new Emisor();

        return $this->arEncabezado['Emisor'];
    }

    /**
     * Set Emisor object
     *
     * @param Emisor $obEmisor
     *
     * @return $this
     */
    public function setEmisor(Emisor $obEmisor): self
    {
        $this->arEncabezado['Emisor'] = $obEmisor;

        return $this;
    }

    /**
     * @return Receptor
     */
    public function receptor(): Receptor
    {
        $this->arEncabezado['Receptor'] = new Receptor();

        return $this->arEncabezado['Receptor'];
    }

    /**
     * Set Receptor object
     *
     * @param Receptor $obReceptor
     *
     * @return $this
     */
    public function setReceptor(Receptor $obReceptor): self
    {
        $this->arEncabezado['Receptor'] = $obReceptor;

        return $this;
    }

    /**
     * @return Totales
     */
    public function totales(): Totales
    {
        $this->arEncabezado['Totales'] = new Totales();

        return $this->arEncabezado['Totales'];
    }

    /**
     * @param SubTotInfo $obSubTotInfo
     *
     * @return $this
     */
    public function addSubTotInfo(SubTotInfo $obSubTotInfo): self
    {
        $this->arExtraData['SubTotInfo'] = $obSubTotInfo->toArray();

        return $this;
    }

    /**
     * @param DscRcgGlobal $obDscRcgGlobal
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
     * @param DRG_Item $obItem
     *
     * @return $this
     */
    public function addGlobalDiscount(DRG_Item $obItem): self
    {
        if (!isset($this->arExtraData['DscRcgGlobal'])) {
            $this->arExtraData['DscRcgGlobal'] = ['DRG_Item' => []];
        }

        $sMntKey = 'MntIVAOtra';

        if ('3' === $obItem->IndFactDR) {
            $sMntKey = 'MntIVATasaBasica';
        } elseif ('2' === $obItem->IndFactDR) {
            $sMntKey = 'MntIVATasaMin';
        }

        $this->addAmount($obItem->ValorDR, $sMntKey, $obItem->TpoMovDR === 'D');

        $obItem->NroLinDR                                = count($this->arExtraData['DscRcgGlobal']['DRG_Item']) + 1;
        $this->arExtraData['DscRcgGlobal']['DRG_Item'][] = $obItem->toArray();

        return $this;
    }

    /**
     * @param MediosPago $obMediosPago
     *
     * @return $this
     */
    public function addMediosPago(MediosPago $obMediosPago): self
    {
        $this->arExtraData['MediosPago'] = $obMediosPago->toArray();

        return $this;
    }

    /**
     * @param MedioPago $obMedioPago
     *
     * @return $this
     */
    public function addMedioPago(MedioPago $obMedioPago): self
    {
        if (!isset($this->arExtraData['MediosPago'])) {
            $this->arExtraData['MediosPago'] = ['MedioPago' => []];
        }

        $obMedioPago->NroLinMP                          = count($this->arExtraData['MediosPago']['MedioPago']) + 1;
        $arMedioPago                                    = $obMedioPago->toArray();
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

        $obReferencia->NroLinRef                         = count($this->arExtraData['Referencia']['Referencia']) + 1;
        $this->arExtraData['Referencia']['Referencia'][] = $obReferencia->toArray();

        return $this;
    }

    /**
     * @param CAEData $obCAEData
     *
     * @return $this
     */
    public function addCAEData(CAEData $obCAEData): self
    {
        $this->arExtraData['CAEData'] = $obCAEData;

        return $this;
    }

    /**
     * @param Compl_Fiscal $obComplFiscal
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

    /**
     * @return bool
     */
    public function isDisableRounding(): bool
    {
        return $this->disableRounding;
    }

    /**
     * @param bool $disableRounding
     *
     * @return void
     */
    public function setDisableRounding(bool $disableRounding): void
    {
        $this->disableRounding = $disableRounding;
    }
}

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

    protected $rules = [
        'Encabezado.IdDoc'    => 'required',
        'Encabezado.Emisor'   => 'required',
        'Encabezado.Receptor' => 'required',
        'Encabezado.Totales'  => 'required',
    ];

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
    public function setData()
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

        $obValidator = \Validator::make($this->arData, $this->rules);
        if (!$obValidator->passes()) {
            throw new \ValidationException($obValidator);
        }
    }

    public function setTotals()
    {
        /** @var Totales $obTotales */
        $obTotales = $this->arEncabezado['Totales'];
        if (!$obTotales) {
            return;
        }

        $this->removeItem();
        $fMontoItems = Collection::make($this->arDetalle['Item'])->sum('MontoItem');

        if ($fTax = $obTotales->IVATasaBasica) {
            $obTotales->MntNetoIVATasaBasica = $fMontoItems;

            // Calculate tax
            $fPriceTax = $fMontoItems * ($fTax / 100);
            $obTotales->MntIVATasaBasica = round($fPriceTax, 2);

            // Add total amount
            $obTotales->MntTotal = round($fMontoItems + $fPriceTax, 2);

            // Calculate rounded price
            $fPriceRounded = round($obTotales->MntTotal);
            $fPriceRound = round($fPriceRounded - $obTotales->MntTotal, 2);

            // Add final price and rounded difference
            $obTotales->MntPagar = $fPriceRounded;
            $obTotales->MontoNF = $fPriceRound;

            // Add Item with round value
            if ($fPriceRound) {
                $obItem = new Item();
                $obItem->IndFact = 6;
                $obItem->NomItem = 'Redondeo';
                $obItem->Cantidad = 1;
                $obItem->PrecioUnitario = $fPriceRound;
                $this->addItem($obItem);
            }

            $obTotales->CantLinDet = count($this->arDetalle['Item']);
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
        $this->arDetalle['Item'][] = $arItem;

        return $this;
    }

    public function removeItem($sVal = 'Redondeo', $sKey = 'NomItem')
    {
        foreach ($this->arDetalle['Item'] as $iKey => $arItem) {
            if (isset($arItem[$sKey]) && $arItem[$sKey] == $sVal) {
                unset($this->arDetalle['Item'][$iKey]);
            }
        }
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

    public function receptor(): Receptor
    {
        return $this->arEncabezado['Receptor'] = new Receptor();
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
     * @param \PlanetaDelEste\Ucfe\Cfe\Referencia $obReferencia
     *
     * @return $this
     */
    public function addReferencia(Referencia $obReferencia): self
    {
        $this->arExtraData['Referencia'] = $obReferencia;

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
}

<?php

namespace PlanetaDelEste\Ucfe\Cfe;

use Arr;
use Carbon\Carbon;
use PlanetaDelEste\Ucfe\Traits\HasAttributeTrait;

abstract class CfeItemBase
{
    use HasAttributeTrait;

    protected array $arRelationList = [];

    protected $arCast = [
        'CantLinDet'           => 'int',
        'NroLinDet'            => 'int',
        'IndFact'              => 'int',
        'Cantidad'             => 'decimal:3',
        'PrecioUnitario'       => 'decimal',
        'MontoItem'            => 'decimal',
        'IVATasaBasica'        => 'decimal',
        'IVATasaMin'           => 'decimal',
        'MntExpoyAsim'         => 'decimal',
        'MntIVAOtra'           => 'decimal',
        'MntIVATasaBasica'     => 'decimal',
        'MntIVATasaMin'        => 'decimal',
        'MntIVaenSusp'         => 'decimal',
        'MntImpuestoPerc'      => 'decimal',
        'MntNetoIVAOtra'       => 'decimal',
        'MntNetoIVATasaBasica' => 'decimal',
        'MntNetoIvaTasaMin'    => 'decimal',
        'MntNoGrv'             => 'decimal',
        'MntPagar'             => 'decimal',
        'MntTotCredFisc'       => 'decimal',
        'MntTotRetenido'       => 'decimal',
        'MntTotal'             => 'decimal',
        'MontoNF'              => 'decimal',
        'TpoCambio'            => 'float',
        'FecVenc'              => 'date',
        'FchVenc'              => 'date',
        'FchEmis'              => 'date',
        'TmstFirma'            => 'date',
    ];

    protected array $arDateList = ['TmstFirma', 'FchEmis', 'FchVenc'];

    public function __construct(array $arData = [])
    {
        $this->setAttributes($arData);
        $this->setRelations();
// $this->setDates();
    }

    protected function setRelations(): void
    {
        if (empty($this->arRelationList)) {
            return;
        }

        foreach ($this->arRelationList as $sAttribute => $sClass) {
            $arData = $this->getAttribute($sAttribute, []);

            if (empty($arData) || !is_array($arData)) {
                continue;
            }

            if (Arr::isAssoc($arData)) {
                $this->setAttribute($sAttribute, new $sClass($arData));
            } else {
                $arAttributeItem = [];

                foreach ($arData as $arItem) {
                    if (empty($arItem) || !is_array($arItem)) {
                        continue;
                    }

                    $arAttributeItem[] = new $sClass($arItem);
                }

                $this->setAttribute($sAttribute, $arAttributeItem);
            }
        }//end foreach
    }

    protected function setDates(): void
    {
        if (empty($this->arDateList)) {
            return;
        }

        foreach ($this->arDateList as $sAttribute) {
            if ((!$sValue = $this->getAttribute($sAttribute)) || empty($sValue)) {
                continue;
            }

            $this->setAttribute($sAttribute, Carbon::parse($sValue));
        }
    }

    protected function setAttributeList(array $arValue, string $sKey, string $sClass): void
    {
        $arLineList = [];

        if (Arr::isAssoc($arValue)) {
            $arLineList[] = new $sClass($arValue);
        } else {
            foreach ($arValue as $arItem) {
                if (is_array($arItem)) {
                    $arLineList[] = new $sClass($arItem);
                } elseif (is_object($arItem) && get_class($arItem) === $sClass) {
                    $arLineList[] = $arItem;
                }
            }
        }

        $this->setAttribute($sKey, $arLineList, true);
    }
}

<?php

namespace PlanetaDelEste\Ucfe\Service\WebServices;

use PlanetaDelEste\Ucfe\WebServicesFE;

/**
 * @property integer $rut
 * @property integer $tipoCfe
 * @property string  $serieCfe
 * @property integer $numeroCfe
 * @property array   $nombreParametros
 * @property array   $valoresParametros
 *
 * @method GetPdfResponse send()
 */
class GetPdf extends WebServicesFE
{
    protected bool $roll    = false;
    protected bool $addenda = false;

    public function setRoll(bool $roll = true): void
    {
        $this->roll = $roll;

        if ($this->roll) {
            $this->setParameter('formato', 'rollo');
        }
    }

    public function setAddenda(bool $addenda = true): void
    {
        $this->addenda = $addenda;

        if ($this->addenda) {
            $this->setParameter('adenda', 'true');
        }
    }

    protected function setParameter(string $sKey, $sValue)
    {
        if (!$this->nombreParametros) {
            $this->nombreParametros  = [];
            $this->valoresParametros = [];
        }

        $this->nombreParametros  = [...$this->nombreParametros, $sKey];
        $this->valoresParametros = [...$this->valoresParametros, $sValue];
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return $this->roll || $this->addenda ? 'ObtenerPdfConParametros' : 'ObtenerPdf';
    }

    /**
     * @inheritDoc
     */
    public function getSortKeys(): array
    {
        $arReturn = ['rut', 'tipoCfe', 'serieCfe', 'numeroCfe'];

        if ($this->roll || $this->addenda) {
            $arReturn[] = 'nombreParametros';
            $arReturn[] = 'valoresParametros';
        }

        return $arReturn;
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return $this->roll || $this->addenda ? GetPdfWithParametersResponse::class : GetPdfResponse::class;
    }
}

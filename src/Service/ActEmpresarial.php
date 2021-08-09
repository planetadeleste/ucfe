<?php

namespace PlanetaDelEste\Ucfe\Service;

use PlanetaDelEste\Ucfe\Client;

/**
 * @method ActEmpresarialResponse exec(array $arParams = [])
 */
class ActEmpresarial extends Client
{

    /**
     * @param string $sRut
     *
     * @return ActEmpresarialResponse
     * @throws \Exception
     */
    public function get(string $sRut): ActEmpresarialResponse
    {
        return $this->exec(['RutEmisor' => $sRut]);
    }

    /**
     * @inheritDoc
     */
    protected function getResponseClass(): string
    {
        return ActEmpresarialResponse::class;
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 640;
    }
}

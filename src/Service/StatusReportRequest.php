<?php

namespace PlanetaDelEste\Ucfe\Service;

use Carbon\Carbon;

/**
 * @method string|null date(string $sValue = null)
 */
class StatusReportRequest extends BaseRequest
{
    protected array $arKeys = ['date'];

    /**
     * @inheritDoc
     */
    protected function getSendData(): array
    {
        $obDate = Carbon::parse($this->date());

        return [
          'IdReq'           => 1,
          'FechaReq'        => $obDate->format('Ymd'),
          'HoraReq'         => $obDate->format('His'),
          'EstadoSituacion' => '00000000',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getTipoMensaje()
    {
        return 800;
    }
}

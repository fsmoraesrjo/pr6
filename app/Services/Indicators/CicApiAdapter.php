<?php

namespace App\Services\Indicators;

use App\Models\Indicator;

/** Adapter placeholder para o CIC (Controle Interno de Contratos) — implementar quando a API estiver disponível. */
class CicApiAdapter implements IndicatorSourceAdapter
{
    public function fetch(Indicator $indicator): array
    {
        return [];
    }
}

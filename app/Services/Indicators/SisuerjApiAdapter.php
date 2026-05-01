<?php

namespace App\Services\Indicators;

use App\Models\Indicator;

/** Adapter placeholder para o SISUERJ — implementar quando a API estiver disponível. */
class SisuerjApiAdapter implements IndicatorSourceAdapter
{
    public function fetch(Indicator $indicator): array
    {
        return [];
    }
}

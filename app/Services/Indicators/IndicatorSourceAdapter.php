<?php

namespace App\Services\Indicators;

use App\Models\Indicator;

interface IndicatorSourceAdapter
{
    /**
     * Retorna array no formato:
     * ['2026-04' => ['value' => 78.5, 'goal' => 95, 'recorded_at' => Carbon, 'notes' => null], ...]
     */
    public function fetch(Indicator $indicator): array;
}

<?php

namespace App\Services\Indicators;

use App\Models\Indicator;

class IndicatorSyncService
{
    /** @var array<string, class-string<IndicatorSourceAdapter>> */
    protected array $adapters = [
        'help_api' => HelpApiAdapter::class,
        'cic_api' => CicApiAdapter::class,
        'sisuerj_api' => SisuerjApiAdapter::class,
    ];

    public function syncAll(): array
    {
        $report = ['ok' => 0, 'fail' => 0, 'skip' => 0, 'errors' => []];

        $indicators = Indicator::query()->acrossTenants()
            ->whereIn('source', array_keys($this->adapters))
            ->get();

        foreach ($indicators as $indicator) {
            try {
                $adapter = app($this->adapters[$indicator->source]);
                $values = $adapter->fetch($indicator);

                foreach ($values as $period => $value) {
                    \App\Models\IndicatorValue::updateOrCreate(
                        ['indicator_id' => $indicator->id, 'period' => $period],
                        [
                            'value' => $value['value'],
                            'goal_value' => $value['goal'] ?? $indicator->goal_value,
                            'recorded_at' => $value['recorded_at'] ?? now(),
                            'notes' => $value['notes'] ?? null,
                        ]
                    );
                }

                $indicator->update(['last_synced_at' => now()]);
                $report['ok']++;
            } catch (\Throwable $e) {
                $report['fail']++;
                $report['errors'][] = "Indicador #{$indicator->id} ({$indicator->slug}): " . $e->getMessage();
            }
        }

        return $report;
    }
}

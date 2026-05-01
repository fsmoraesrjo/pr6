<?php

namespace App\Services\Indicators;

use App\Models\Indicator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Adapter para o sistema HELP (chamados da PR-6).
 *
 * Configuração esperada em $indicator->source_config:
 * - endpoint: URL completa (ex.: https://help.pr6.uerj.br/api/stats/sla)
 * - metric: campo a extrair do retorno (ex.: 'sla_percent')
 * - token (opcional): bearer token
 */
class HelpApiAdapter implements IndicatorSourceAdapter
{
    public function fetch(Indicator $indicator): array
    {
        $config = $indicator->source_config ?? [];
        $endpoint = $config['endpoint'] ?? config('services.help.url') . '/api/stats';
        $metric = $config['metric'] ?? 'value';
        $token = $config['token'] ?? config('services.help.token');

        if (! $endpoint) {
            Log::warning('HelpApiAdapter sem endpoint para o indicador #' . $indicator->id);
            return [];
        }

        $request = Http::timeout(15)->retry(2, 500)
            ->acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get($endpoint, [
            'tenant' => $indicator->tenant?->slug,
            'period' => now()->format('Y-m'),
        ]);

        if (! $response->ok()) {
            Log::warning("HelpApiAdapter retornou {$response->status()} para indicador #{$indicator->id}");
            return [];
        }

        $payload = $response->json();
        // Espera-se um array de períodos, ou um único valor para o período atual
        if (isset($payload['series']) && is_array($payload['series'])) {
            $out = [];
            foreach ($payload['series'] as $row) {
                $out[$row['period']] = [
                    'value' => $row[$metric] ?? 0,
                    'goal' => $row['goal'] ?? null,
                    'recorded_at' => isset($row['recorded_at']) ? \Carbon\Carbon::parse($row['recorded_at']) : now(),
                    'notes' => $row['notes'] ?? null,
                ];
            }
            return $out;
        }

        $current = now()->format('Y-m');
        return [$current => ['value' => $payload[$metric] ?? 0, 'recorded_at' => now()]];
    }
}

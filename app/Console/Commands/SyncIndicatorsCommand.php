<?php

namespace App\Console\Commands;

use App\Services\Indicators\IndicatorSyncService;
use Illuminate\Console\Command;

class SyncIndicatorsCommand extends Command
{
    protected $signature = 'indicators:sync';
    protected $description = 'Sincroniza indicadores com fontes externas (HELP, CIC, SISUERJ)';

    public function handle(IndicatorSyncService $service): int
    {
        $this->info('Sincronizando indicadores...');
        $report = $service->syncAll();
        $this->info("OK: {$report['ok']} | Falhas: {$report['fail']} | Pulados: {$report['skip']}");
        foreach ($report['errors'] as $error) {
            $this->warn("  - {$error}");
        }
        return $report['fail'] === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}

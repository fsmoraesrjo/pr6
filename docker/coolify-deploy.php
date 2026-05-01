<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Inicia o banco
$db = App\Models\StandaloneMariadb::where('name', 'pr6-db')->firstOrFail();
echo "DB status atual: {$db->status}\n";

if ($db->status !== 'running') {
    echo "Iniciando MariaDB pr6-db...\n";
    App\Actions\Database\StartDatabase::run($db);
    echo "DB start dispatched\n";
} else {
    echo "DB ja esta rodando\n";
}

// Dispara deploy do app
$appModel = App\Models\Application::find(21);
echo "App: {$appModel->name} fqdn={$appModel->fqdn}\n";

$deploymentUuid = (string) new Visus\Cuid2\Cuid2();
App\Jobs\ApplicationDeploymentJob::dispatch(
    application_deployment_queue_id: App\Models\ApplicationDeploymentQueue::create([
        'application_id' => $appModel->id,
        'application_name' => $appModel->name,
        'deployment_uuid' => $deploymentUuid,
        'force_rebuild' => false,
        'is_webhook' => false,
        'is_new_deployment' => true,
        'restart_only' => false,
        'pull_request_id' => 0,
        'commit' => 'HEAD',
        'rollback' => false,
        'status' => 'queued',
        'server_id' => 0,
        'destination_id' => $appModel->destination_id,
    ])->id,
);

echo "Deploy enfileirado: $deploymentUuid\n";

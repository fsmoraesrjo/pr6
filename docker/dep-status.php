<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$d = App\Models\ApplicationDeploymentQueue::where('application_id', 21)->latest()->first();
if (!$d) { echo "Nenhum deploy do PR-6 ainda.\n"; exit; }
echo "Deploy {$d->deployment_uuid}\n";
echo "App: {$d->application_name}\n";
echo "Status: {$d->status}\n";
echo "Created: {$d->created_at}\n";
echo "Logs (last 30 lines):\n";

$logs = is_array($d->logs) ? $d->logs : (json_decode($d->logs, true) ?? []);
$last = array_slice($logs, -30);
foreach ($last as $l) {
    echo "[" . ($l['type'] ?? '') . "] " . ($l['output'] ?? '') . "\n";
}

$db = App\Models\StandaloneMariadb::where('name', 'pr6-db')->first();
echo "\nDB pr6-db status: {$db->status}\n";

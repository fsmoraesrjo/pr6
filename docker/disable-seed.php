<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

App\Models\EnvironmentVariable::where('resourceable_id', 21)
    ->where('resourceable_type', 'App\\Models\\Application')
    ->where('key', 'DB_SEED_ON_BOOT')
    ->update(['value' => 'false']);

echo "DB_SEED_ON_BOOT desligado\n";

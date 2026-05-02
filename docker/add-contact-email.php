<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ev = new App\Models\EnvironmentVariable();
$ev->key = 'PR6_CONTACT_EMAIL';
$ev->value = 'pr6@uerj.br';
$ev->resourceable_id = 21;
$ev->resourceable_type = 'App\\Models\\Application';
$ev->is_preview = false;
$ev->save();
echo "OK PR6_CONTACT_EMAIL=pr6@uerj.br adicionada\n";

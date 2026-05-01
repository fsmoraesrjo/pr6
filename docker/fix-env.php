<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$envVars = [
    'APP_NAME' => 'PR-6 UERJ',
    'APP_ENV' => 'production',
    'APP_KEY' => 'base64:v1AfFhyfIZyAddbpLFJqvwnTlkEVQxCzoacAWCa7O48=',
    'APP_DEBUG' => 'false',
    'APP_TIMEZONE' => 'America/Sao_Paulo',
    'APP_URL' => 'https://pr6.lumislabs.com.br',
    'APP_LOCALE' => 'pt_BR',
    'APP_FALLBACK_LOCALE' => 'pt_BR',
    'APP_FAKER_LOCALE' => 'pt_BR',
    'APP_MAINTENANCE_DRIVER' => 'file',
    'BCRYPT_ROUNDS' => '12',
    'LOG_CHANNEL' => 'stack',
    'LOG_STACK' => 'single',
    'LOG_LEVEL' => 'warning',
    'DB_CONNECTION' => 'mysql',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'pr6',
    'DB_USERNAME' => 'pr6',
    'SESSION_DRIVER' => 'database',
    'SESSION_LIFETIME' => '120',
    'SESSION_ENCRYPT' => 'true',
    'SESSION_PATH' => '/',
    'SESSION_DOMAIN' => '.lumislabs.com.br',
    'BROADCAST_CONNECTION' => 'log',
    'FILESYSTEM_DISK' => 'public',
    'QUEUE_CONNECTION' => 'database',
    'CACHE_STORE' => 'database',
    'CACHE_PREFIX' => 'pr6',
    'MAIL_MAILER' => 'log',
    'MAIL_FROM_ADDRESS' => 'naoresponda@pr6.lumislabs.com.br',
    'MAIL_FROM_NAME' => 'PR-6 UERJ',
    'PR6_ROOT_DOMAIN' => 'pr6.lumislabs.com.br',
    'PR6_ROOT_DOMAIN_PROD' => 'pr6.uerj.br',
    'SCOUT_DRIVER' => 'tntsearch',
    'TNTSEARCH_FUZZINESS' => 'true',
    'TRUSTED_PROXIES' => '*',
    'ASSET_URL' => 'https://pr6.lumislabs.com.br',
    'DB_SEED_ON_BOOT' => 'false',
];

// Pega a senha real do banco
$db = App\Models\StandaloneMariadb::where('name', 'pr6-db')->firstOrFail();
$envVars['DB_HOST'] = $db->uuid;
$envVars['DB_PASSWORD'] = $db->mariadb_password;

// Apaga TODAS as env vars do app (algumas estao plain text, quebrando decrypt)
\Illuminate\Support\Facades\DB::table('environment_variables')
    ->where('resourceable_id', 21)
    ->where('resourceable_type', 'App\\Models\\Application')
    ->delete();
echo "Env vars antigas deletadas\n";

$count = 0;
foreach ($envVars as $key => $value) {
    $ev = new App\Models\EnvironmentVariable();
    $ev->key = $key;
    $ev->value = $value;          // dispara mutator (encrypt)
    $ev->resourceable_id = 21;
    $ev->resourceable_type = 'App\\Models\\Application';
    $ev->is_preview = false;
    $ev->save();
    $count++;
}
echo "Refeitas $count env vars via save() individual (cast aplicado)\n";

// Validacao: ler de volta e ver se decrypta
echo "\nValidando leitura:\n";
foreach (['APP_NAME', 'DB_SEED_ON_BOOT', 'DB_PASSWORD'] as $k) {
    $ev = App\Models\EnvironmentVariable::where([
        'key' => $k, 'resourceable_id' => 21, 'resourceable_type' => 'App\\Models\\Application',
    ])->first();
    try {
        $val = $ev->real_value ?? $ev->value;
        echo "  $k = " . substr($val, 0, 40) . "\n";
    } catch (Exception $e) {
        echo "  $k ERRO: " . $e->getMessage() . "\n";
    }
}

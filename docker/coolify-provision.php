<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;

// ===== 1. PROJECT =====
$team = App\Models\Team::first();
$project = App\Models\Project::firstOrCreate(
    ['name' => 'PR-6 UERJ'],
    ['team_id' => $team->id, 'description' => 'Portal multi-tenant da Pro-Reitoria de Planejamento e Gestao da UERJ', 'uuid' => (string) new Visus\Cuid2\Cuid2()]
);
echo "Project #{$project->id} {$project->name}\n";

// Environment 'production' (criado pelo bootObserver, mas conferimos)
$env = $project->environments()->firstOrCreate(['name' => 'production'], ['uuid' => (string) new Visus\Cuid2\Cuid2()]);
echo "Environment #{$env->id} {$env->name}\n";

// ===== 2. DATABASE: MariaDB =====
$existingDb = App\Models\StandaloneMariadb::where('name', 'pr6-db')->first();
if ($existingDb) {
    echo "Database ja existe: #{$existingDb->id} {$existingDb->name}\n";
    $db = $existingDb;
} else {
    $dbPassword = Str::random(32);
    $rootPassword = Str::random(32);
    $db = App\Models\StandaloneMariadb::create([
        'name' => 'pr6-db',
        'description' => 'MariaDB 11 para PR-6 portal',
        'mariadb_root_password' => $rootPassword,
        'mariadb_password' => $dbPassword,
        'mariadb_user' => 'pr6',
        'mariadb_database' => 'pr6',
        'mariadb_conf' => '',
        'image' => 'mariadb:11',
        'is_public' => false,
        'public_port' => null,
        'environment_id' => $env->id,
        'destination_id' => 0,
        'destination_type' => 'App\\Models\\StandaloneDocker',
        'uuid' => (string) new Visus\Cuid2\Cuid2(),
    ]);
    echo "Database CRIADO #{$db->id} pr6-db (user=pr6 db=pr6)\n";
    echo "DB_PASSWORD=$dbPassword\n";
}

// ===== 3. APPLICATION =====
$existingApp = App\Models\Application::where('name', 'pr6-portal')->first();
if ($existingApp) {
    echo "Application ja existe: #{$existingApp->id}\n";
    $appModel = $existingApp;
} else {
    $appModel = App\Models\Application::create([
        'name' => 'pr6-portal',
        'description' => 'Portal PR-6 + DIRTEC + DIRGIS + DIRPLAG + COOMAS',
        'fqdn' => 'https://pr6.lumislabs.com.br,https://dirtec.pr6.lumislabs.com.br,https://dirgis.pr6.lumislabs.com.br,https://dirplag.pr6.lumislabs.com.br,https://coomas.pr6.lumislabs.com.br',
        'git_repository' => 'https://github.com/fsmoraesrjo/pr6',
        'git_branch' => 'main',
        'git_commit_sha' => 'HEAD',
        'build_pack' => 'dockerfile',
        'ports_exposes' => '80',
        'base_directory' => '/',
        'dockerfile_location' => '/Dockerfile',
        'docker_compose_location' => '/docker-compose.yaml',
        'static_image' => 'nginx:alpine',
        'environment_id' => $env->id,
        'destination_id' => 0,
        'destination_type' => 'App\\Models\\StandaloneDocker',
        'private_key_id' => null,
        'source_id' => null,
        'source_type' => null,
        'uuid' => (string) new Visus\Cuid2\Cuid2(),
    ]);
    echo "Application CRIADO #{$appModel->id} pr6-portal\n";

    // settings
    $appModel->settings()->create([
        'is_force_https_enabled' => true,
        'is_auto_deploy_enabled' => true,
        'is_gzip_enabled' => true,
        'is_log_drain_enabled' => false,
    ]);
}

// ===== 4. ENV VARS =====
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
    'DB_HOST' => $db->uuid,                 // Coolify resolve uuid -> service name na rede
    'DB_PORT' => '3306',
    'DB_DATABASE' => $db->mariadb_database,
    'DB_USERNAME' => $db->mariadb_user,
    'DB_PASSWORD' => $db->mariadb_password,
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
    'DB_SEED_ON_BOOT' => 'true',
];

foreach ($envVars as $key => $value) {
    App\Models\EnvironmentVariable::updateOrCreate(
        ['key' => $key, 'resourceable_id' => $appModel->id, 'resourceable_type' => 'App\\Models\\Application', 'is_preview' => false],
        ['value' => $value, 'is_build_time' => false, 'is_shown_once' => false]
    );
}
echo "Env vars: " . count($envVars) . " configuradas\n";

// ===== 5. PERSISTENT VOLUME (storage) =====
App\Models\LocalPersistentVolume::firstOrCreate(
    ['name' => 'pr6-storage', 'resource_id' => $appModel->id, 'resource_type' => 'App\\Models\\Application'],
    ['mount_path' => '/var/www/html/storage', 'host_path' => null]
);
echo "Volume pr6-storage configurado em /var/www/html/storage\n";

echo "\n=== RESUMO ===\n";
echo "Project UUID: {$project->uuid}\n";
echo "Env UUID:     {$env->uuid}\n";
echo "DB UUID:      {$db->uuid}\n";
echo "App UUID:     {$appModel->uuid}\n";
echo "App ID:       {$appModel->id}\n";
echo "Coolify URL:  https://coolify.lumislabs.com.br/project/{$project->uuid}/environment/{$env->uuid}/application/{$appModel->uuid}\n";

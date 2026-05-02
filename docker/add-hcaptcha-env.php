<?php
// Script one-shot para popular env vars do hCaptcha no Coolify.
// Os secrets reais são passados via variáveis de ambiente, NUNCA commitados.
// Uso: HCAPTCHA_SITEKEY=xxx HCAPTCHA_SECRET=yyy php add-hcaptcha-env.php

require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$vars = [
    'HCAPTCHA_SITEKEY' => getenv('HCAPTCHA_SITEKEY') ?: '',
    'HCAPTCHA_SECRET' => getenv('HCAPTCHA_SECRET') ?: '',
];

if (! $vars['HCAPTCHA_SITEKEY'] || ! $vars['HCAPTCHA_SECRET']) {
    echo "Erro: defina HCAPTCHA_SITEKEY e HCAPTCHA_SECRET como variáveis de ambiente antes de rodar.\n";
    exit(1);
}

foreach ($vars as $key => $value) {
    $existing = App\Models\EnvironmentVariable::where('key', $key)
        ->where('resourceable_id', 21)
        ->where('resourceable_type', 'App\\Models\\Application')
        ->first();

    if ($existing) {
        $existing->value = $value;
        $existing->save();
        echo "Atualizada $key\n";
    } else {
        $ev = new App\Models\EnvironmentVariable();
        $ev->key = $key;
        $ev->value = $value;
        $ev->resourceable_id = 21;
        $ev->resourceable_type = 'App\\Models\\Application';
        $ev->is_preview = false;
        $ev->save();
        echo "Criada $key\n";
    }
}
echo "OK\n";

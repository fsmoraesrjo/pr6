<?php
require '/var/www/html/vendor/autoload.php';
$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = App\Models\User::firstOrCreate(['email' => 'admin@pr6.uerj.br'], ['name' => 'Admin PR-6']);
$u->password = Illuminate\Support\Facades\Hash::make('PR6@admin2026');
$u->is_active = true;
$u->save();

echo "OK\n";
echo "Email: " . $u->email . "\n";
echo "Senha: PR6@admin2026\n";
echo "Active: " . ($u->is_active ? 'true' : 'false') . "\n";
echo "Hash: " . substr($u->password, 0, 30) . "...\n";

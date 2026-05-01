<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@pr6.uerj.br'],
            [
                'name' => 'Administrador PR-6',
                'password' => Hash::make('senha-forte-mude-isto'),
                'is_active' => true,
            ]
        );

        $this->call([
            TenantSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Pages\Page;

class TenantSwitcher
{
    public static function options(): array
    {
        return Tenant::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->pluck('short_name', 'id')
            ->toArray();
    }
}

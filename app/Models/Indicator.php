<?php

namespace App\Models;

use App\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'goal_value' => 'decimal:4',
        'source_config' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public const SOURCES = [
        'manual' => 'Inserção manual',
        'help_api' => 'API do HELP (chamados)',
        'cic_api' => 'API do CIC (contratos)',
        'sisuerj_api' => 'API do SISUERJ',
        'csv_import' => 'Importação de CSV',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(IndicatorValue::class);
    }

    public function latestValue()
    {
        return $this->values()->orderByDesc('recorded_at')->first();
    }
}

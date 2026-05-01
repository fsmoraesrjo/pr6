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

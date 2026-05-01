<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorValue extends Model
{
    protected $guarded = [];

    protected $casts = [
        'recorded_at' => 'datetime',
        'value' => 'decimal:4',
        'goal_value' => 'decimal:4',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }
}

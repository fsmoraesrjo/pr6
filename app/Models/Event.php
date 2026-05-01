<?php

namespace App\Models;

use App\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_online' => 'boolean',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public static bool $crossTenantOnPortal = true;

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now())->orderBy('starts_at');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}

<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use App\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes, BelongsToTenant, HasAuditLog;

    protected $guarded = [];

    protected $casts = [
        'is_public' => 'boolean',
        'published_at' => 'datetime',
    ];

    public static bool $crossTenantOnPortal = true;

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->latest();
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }
}

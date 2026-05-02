<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = [];

    protected $casts = [
        'is_root' => 'boolean',
        'is_active' => 'boolean',
        'contact' => 'array',
    ];

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class);
    }

    public function url(): string
    {
        if (request()?->has('tenant')) {
            return url('/?tenant=' . $this->slug);
        }

        $domain = app()->environment('production')
            ? $this->domain_prod
            : $this->domain_dev;

        $scheme = app()->environment('production') ? 'https' : 'http';

        return $scheme . '://' . $domain;
    }

    public function isRoot(): bool
    {
        return (bool) $this->is_root;
    }
}

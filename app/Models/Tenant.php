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

        // Decide pelo host atual: enquanto o DNS *.pr6.uerj.br nao estiver liberado,
        // o sistema roda em *.pr6.lumislabs.com.br (mesmo em APP_ENV=production).
        // Linkar para o dominio "real" antes da hora gera 404 para o usuario.
        $rootProd = config('pr6.root_domain_prod', 'pr6.uerj.br');
        $rootDev  = config('pr6.root_domain', 'pr6.lumislabs.com.br');
        $host = request()?->getHost() ?? '';

        $emProd = $host === $rootProd || str_ends_with($host, '.' . $rootProd);

        $domain = $emProd
            ? ($this->domain_prod ?: $this->domain_dev)
            : ($this->domain_dev ?: $this->domain_prod);

        // Em ambos os ambientes hospedados (lumislabs e uerj) usamos HTTPS via Traefik.
        $scheme = request()?->isSecure() ? 'https' : (app()->environment('local') ? 'http' : 'https');

        return $scheme . '://' . $domain;
    }

    public function isRoot(): bool
    {
        return (bool) $this->is_root;
    }
}

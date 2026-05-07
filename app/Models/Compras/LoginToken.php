<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LoginToken extends Model
{
    protected $table = 'compras_login_tokens';
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function respondente(): BelongsTo
    {
        return $this->belongsTo(Respondente::class);
    }

    public function pesquisa(): BelongsTo
    {
        return $this->belongsTo(Pesquisa::class);
    }

    /**
     * Gera um par (plaintext, hash) — armazene SOMENTE o hash. Plaintext vai pro e-mail.
     */
    public static function generatePair(): array
    {
        $plain = Str::random(64);
        $hash = hash('sha256', $plain);
        return [$plain, $hash];
    }

    public static function hash(string $plaintext): string
    {
        return hash('sha256', $plaintext);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}

<?php

namespace App\Models\Compras;

use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Respondente extends Model
{
    protected $table = 'compras_respondentes';
    protected $guarded = [];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function loginTokens(): HasMany
    {
        return $this->hasMany(LoginToken::class);
    }
}

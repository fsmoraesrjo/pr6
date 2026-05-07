<?php

namespace App\Models\Compras;

use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesquisaSetor extends Model
{
    protected $table = 'compras_pesquisa_setores';
    protected $guarded = [];

    protected $casts = [
        'respondido' => 'boolean',
        'sem_demanda' => 'boolean',
        'respondido_em' => 'datetime',
    ];

    public function pesquisa(): BelongsTo
    {
        return $this->belongsTo(Pesquisa::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function respondidoPor(): BelongsTo
    {
        return $this->belongsTo(Respondente::class, 'respondido_por_respondente_id');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class);
    }
}

<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesquisaEquipamento extends Model
{
    protected $table = 'compras_pesquisa_equipamentos';
    protected $guarded = [];

    public function pesquisa(): BelongsTo
    {
        return $this->belongsTo(Pesquisa::class);
    }

    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class);
    }
}

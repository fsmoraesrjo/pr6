<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resposta extends Model
{
    protected $table = 'compras_respostas';
    protected $guarded = [];

    public function pesquisaSetor(): BelongsTo
    {
        return $this->belongsTo(PesquisaSetor::class);
    }

    public function pesquisaEquipamento(): BelongsTo
    {
        return $this->belongsTo(PesquisaEquipamento::class);
    }

    public function destinos(): HasMany
    {
        return $this->hasMany(RespostaDestino::class);
    }

    public function trocas(): HasMany
    {
        return $this->hasMany(RespostaTroca::class);
    }
}

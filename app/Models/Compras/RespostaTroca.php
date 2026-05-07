<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespostaTroca extends Model
{
    public const TIPO_SUBSTITUICAO = 'substituicao';
    public const TIPO_EXPANSAO = 'expansao';
    public const TIPO_REPOSICAO = 'reposicao';

    public const TIPOS = [
        self::TIPO_SUBSTITUICAO => 'Substituição',
        self::TIPO_EXPANSAO => 'Expansão',
        self::TIPO_REPOSICAO => 'Reposição',
    ];

    protected $table = 'compras_resposta_trocas';
    protected $guarded = [];

    public function resposta(): BelongsTo
    {
        return $this->belongsTo(Resposta::class);
    }
}

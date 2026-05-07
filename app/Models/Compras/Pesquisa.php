<?php

namespace App\Models\Compras;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesquisa extends Model
{
    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_ABERTA = 'aberta';
    public const STATUS_ENCERRADA = 'encerrada';
    public const STATUS_CONSOLIDADA = 'consolidada';
    public const STATUS_CANCELADA = 'cancelada';

    public const STATUSES = [
        self::STATUS_RASCUNHO => 'Rascunho',
        self::STATUS_ABERTA => 'Aberta',
        self::STATUS_ENCERRADA => 'Encerrada',
        self::STATUS_CONSOLIDADA => 'Consolidada',
        self::STATUS_CANCELADA => 'Cancelada',
    ];

    protected $table = 'compras_pesquisas';
    protected $guarded = [];

    protected $casts = [
        'data_abertura' => 'date',
        'data_encerramento' => 'date',
        'aberta_em' => 'datetime',
        'encerrada_em' => 'datetime',
        'consolidada_em' => 'datetime',
    ];

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por_user_id');
    }

    public function equipamentos(): HasMany
    {
        return $this->hasMany(PesquisaEquipamento::class)->orderBy('ordem');
    }

    public function setores(): HasMany
    {
        return $this->hasMany(PesquisaSetor::class);
    }

    public function loginTokens(): HasMany
    {
        return $this->hasMany(LoginToken::class);
    }

    public function isAberta(): bool
    {
        return $this->status === self::STATUS_ABERTA;
    }

    public function podeResponder(): bool
    {
        if (!$this->isAberta()) {
            return false;
        }

        if ($this->data_encerramento && $this->data_encerramento->lt(now()->startOfDay())) {
            return false;
        }

        return true;
    }
}

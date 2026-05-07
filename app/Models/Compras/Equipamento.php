<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipamento extends Model
{
    protected $table = 'compras_equipamentos';
    protected $guarded = [];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function pesquisaEquipamentos(): HasMany
    {
        return $this->hasMany(PesquisaEquipamento::class);
    }
}

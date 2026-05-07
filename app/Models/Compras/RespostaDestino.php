<?php

namespace App\Models\Compras;

use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespostaDestino extends Model
{
    protected $table = 'compras_resposta_destinos';
    protected $guarded = [];

    public function resposta(): BelongsTo
    {
        return $this->belongsTo(Resposta::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }
}

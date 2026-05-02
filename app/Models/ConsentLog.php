<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsentLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'granted' => 'boolean',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}

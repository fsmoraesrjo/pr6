<?php

namespace App\Models;

use App\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use BelongsToTenant;

    protected $table = 'service_categories';
    protected $guarded = [];
}

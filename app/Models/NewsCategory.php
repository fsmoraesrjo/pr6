<?php

namespace App\Models;

use App\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    use BelongsToTenant;

    protected $table = 'news_categories';
    protected $guarded = [];
}

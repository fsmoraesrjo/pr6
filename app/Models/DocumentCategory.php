<?php

namespace App\Models;

use App\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use BelongsToTenant;

    protected $table = 'document_categories';
    protected $guarded = [];
}

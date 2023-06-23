<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemTag extends Pivot
{
    use HasFactory;

    protected $casts = [
        'id'=>'integer',
        'item_id'=>'integer',
        'tag_id'=>'integer'
    ];
}

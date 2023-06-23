<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ModuleZone extends Pivot
{
    use HasFactory;

    protected $casts = [
        'id'=>'integer',
        'module_id'=>'integer',
        'zone_id'=>'integer',
        'per_km_shipping_charge'=>'float',
        'minimum_shipping_charge'=>'float',
        'maximum_cod_order_amount'=>'float',
    ];
}

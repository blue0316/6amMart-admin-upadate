<?php

namespace App\Models;

use App\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $casts = [
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'discount' => 'float',
        'limit'=>'integer',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
    
    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }
    
    // protected static function booted()
    // {
    //     if(auth('vendor')->check())
    //     {
    //         static::addGlobalScope(new StoreScope);
    //     } 
    // }
}

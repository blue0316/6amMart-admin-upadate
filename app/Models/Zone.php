<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use App\Scopes\ZoneScope;

class Zone extends Model
{
    use HasFactory;
    use SpatialTrait;

    protected $spatialFields = [
        'coordinates'
    ];

    protected $casts = [
        'status' => 'integer',
        'cash_on_delivery' => 'boolean',
        'digital_payment' => 'boolean',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function deliverymen()
    {
        return $this->hasMany(DeliveryMan::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Store::class);
    }


    public function campaigns()
    {
        return $this->hasManyThrough(Campaigns::class, Store::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class)->withPivot(['per_km_shipping_charge','minimum_shipping_charge','maximum_cod_order_amount'])->using('App\Models\ModuleZone');
    }
}

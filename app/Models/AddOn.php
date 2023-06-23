<?php

namespace App\Models;

use App\Scopes\StoreScope;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AddOn extends Model
{
    protected $casts = [
        'price' => 'float',
        'store_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    protected static function booted()
    {
        if(auth('vendor')->check() || auth('vendor_employee')->check())
        {
            static::addGlobalScope(new StoreScope);
        } 
        static::addGlobalScope(new ZoneScope);

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}

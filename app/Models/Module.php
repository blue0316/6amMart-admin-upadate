<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $casts = [
        'id'=>'integer',
        'stores_count'=>'integer',
        'theme_id'=>'integer',
        'status'=>'string',
        'all_zone_service'=>'integer'
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeParcel($query)
    {
        return $query->where('module_type', 'parcel');
    }

    public function scopeNotParcel($query)
    {
        return $query->where('module_type', '!=' ,'parcel');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class);
    }
}

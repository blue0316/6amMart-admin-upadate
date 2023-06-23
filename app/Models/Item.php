<?php

namespace App\Models;

use App\Scopes\StoreScope;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use HasFactory;
    
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'avg_rating' => 'float',
        'set_menu' => 'integer',
        'category_id' => 'integer',
        'store_id' => 'integer',
        'reviews_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'veg'=>'integer',
        'images'=>'array',
        'module_id'=>'integer',
        'stock'=>'integer',
    ];

    protected $appends = ['unit_type'];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->whereHas('store', function($query){
            return $query->where('status', 1);
        });
    }

    public function scopePopular($query)
    {
        return $query->orderBy('order_count', 'desc');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class,'module_id');
    }

    public function getUnitTypeAttribute()
    {
        return $this->unit?$this->unit->unit:null;
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
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


    public function scopeType($query, $type)
    {
        if($type == 'veg')
        {
            return $query->where('veg', true);
        }
        else if($type == 'non_veg')
        {
            return $query->where('veg', false);
        }
        
        return $query;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    
}

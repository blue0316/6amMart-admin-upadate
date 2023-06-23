<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Builder;

class ItemCampaign extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at', 'start_date', 'end_date', 'start_time', 'end_time'];

    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'discount' => 'float',
        'status' => 'integer',
        'store_id' => 'integer',
        'category_id' => 'integer',
        'module_id' => 'integer',
        'veg' => 'integer',
        'stock'=>'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
    
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function orderdetails()
    {
        return $this->hasMany(OrderDetail::class)->latest();
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
    
    public function scopeRunning($query)
    {
        return $query->whereDate('end_date', '>=', date('Y-m-d'));
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}

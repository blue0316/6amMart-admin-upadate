<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ZoneScope;

class Banner extends Model
{
    use HasFactory;
    protected $casts = [
        'data' => 'integer',
    ];
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', '=', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }
}

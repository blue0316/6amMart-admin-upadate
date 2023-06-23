<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'start_date','end_date','start_time','end_time','min_purchase','max_discount','discount','discount_type','store_id',
    ];
    protected $casts = [
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'discount' => 'float',
        'store_id'=>'integer'
    ];
    protected $dates = ['created_at', 'updated_at'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeValidate($query)
    {
        $query->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'))->whereTime('start_time','<=',date('H:i:s'))->whereTime('end_time','>=',date('H:i:s'));
    }
}

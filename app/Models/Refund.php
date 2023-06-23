<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    protected $casts = [
        'refund_amount' => 'float',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

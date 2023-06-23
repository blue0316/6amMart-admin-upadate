<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => 'float',
        'discount_on_item' => 'float',
        'total_add_on_price' => 'float',
        'tax_amount' => 'float',
        'item_id'=> 'integer',
        'order_id'=> 'integer',
        'quantity'=>'integer',
        'item_campaign_id'=>'integer'
    ];

    protected $primaryKey   = 'id';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function vendor()
    {
        return $this->order->store();
    }
    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }
    public function campaign()
    {
        return $this->belongsTo(ItemCampaign::class, 'item_campaign_id');
    }
}

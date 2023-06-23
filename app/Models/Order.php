<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'order_amount' => 'float',
        'coupon_discount_amount' => 'float',
        'total_tax_amount' => 'float',
        'store_discount_amount' => 'float',
        'delivery_address_id' => 'integer',
        'delivery_man_id' => 'integer',
        'delivery_charge' => 'float',
        'original_delivery_charge'=>'float',
        'user_id' => 'integer',
        'scheduled' => 'integer',
        'store_id' => 'integer',
        'details_count' => 'integer',
        'module_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'original_delivery_charge'=>'float',
        'receiver_details'=>'array',
        'dm_tips'=>'float',
        'distance'=>'float', 
        'prescription_order' => 'boolean'
    ];

    protected $appends = ['module_type'];

    public function setDeliveryChargeAttribute($value)
    {
        $this->attributes['delivery_charge'] = round($value, 3);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function delivery_history()
    {
        return $this->hasMany(DeliveryHistory::class, 'order_id');
    }

    public function dm_last_location()
    {
        // return $this->hasOne(DeliveryHistory::class, 'order_id')->latest();
        return $this->delivery_man->last_location();
    }

    public function transaction()
    {
        return $this->hasOne(OrderTransaction::class);
    }

    public function parcel_category()
    {
        return $this->belongsTo(ParcelCategory::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'order_id');
    }

    public function getModuleTypeAttribute()
    {
        return $this->module?$this->module->module_type:null;
    }

    public function scopeAccepteByDeliveryman($query)
    {
        return $query->where('order_status', 'accepted');
    }

    public function scopePreparing($query)
    {
        return $query->whereIn('order_status', ['confirmed','processing','handover']);
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeOngoing($query)
    {
        return $query->whereIn('order_status', ['accepted','confirmed','processing','handover','picked_up']);
    }

    public function scopeItemOnTheWay($query)
    {
        return $query->where('order_status','picked_up');
    }

    public function scopePending($query)
    {
        return $query->where('order_status','pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('order_status','failed');
    }

    public function scopeCanceled($query)
    {
        return $query->where('order_status','canceled');
    }

    public function scopeDelivered($query)
    {
        return $query->where('order_status','delivered');
    }

    public function scopeNotRefunded($query)
    {
        return $query->where(function($query){
            $query->whereNotIn('order_status', ['refunded']);
        });
    }

    public function scopeRefunded($query)
    {
        return $query->where('order_status','refunded');
    }
    public function scopeRefund_requested($query)
    {
        return $query->where('order_status','refund_requested');
    }

    public function scopeRefund_request_canceled($query)
    {
        return $query->where('order_status','refund_request_canceled');
    }

    public function scopeSearchingForDeliveryman($query)
    {
        return $query->whereNull('delivery_man_id')->whereIn('order_type' , ['delivery','parcel'])->whereNotIn('order_status',['delivered','failed','canceled', 'refund_requested','refund_request_canceled', 'refunded']);
    }

    public function scopeDelivery($query)
    {
        return $query->where('order_type', '=' , 'delivery');
    }

    public function scopeScheduled($query)
    {
        return $query->whereRaw('created_at <> schedule_at')->where('scheduled', '1');
    }

    public function scopeOrderScheduledIn($query, $interval)
    {
        return $query->where(function($query)use($interval){
            $query->whereRaw('created_at <> schedule_at')->where(function($q) use ($interval) {
            $q->whereBetween('schedule_at', [Carbon::now()->toDateTimeString(),Carbon::now()->addMinutes($interval)->toDateTimeString()]);
            })->orWhere('schedule_at','<',Carbon::now()->toDateTimeString());
        })->orWhereRaw('created_at = schedule_at');

    }


    public function scopeStoreOrder($query)
    {
        return $query->where(function($q){
            $q->where('order_type', 'take_away')->orWhere('order_type', 'delivery');
        });
    }

    public function scopeDmOrder($query)
    {
        return $query->where(function($q){
            $q->where('order_type', 'parcel')->orWhere('order_type', 'delivery');
        });
    }

    public function scopeParcelOrder($query)
    {
        return $query->where('order_type', 'parcel');
    }

    public function scopePos($query)
    {
        return $query->where('order_type', '=' , 'pos');
    }

    public function scopeNotpos($query)
    {
        return $query->where('order_type', '<>' , 'pos');
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s',strtotime($value));
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }
}

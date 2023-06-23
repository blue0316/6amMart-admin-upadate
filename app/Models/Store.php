<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;
use App\Scopes\ZoneScope;

class Store extends Model
{

    protected $casts = [
        'minimum_order' => 'float',
        'comission' => 'float',
        'tax' => 'float',
        'minimum_shipping_charge' => 'float',
        'per_km_shipping_charge' => 'float',
        'schedule_order'=>'boolean',
        'free_delivery'=>'boolean',
        'vendor_id'=>'integer',
        'status'=>'integer',
        'delivery'=>'boolean',
        'take_away'=>'boolean',
        'zone_id'=>'integer',
        'module_id'=>'integer',
        'item_section'=>'boolean',
        'reviews_section'=>'boolean',
        'active'=>'boolean',
        'gst_status'=>'boolean',
        'pos_system'=>'boolean',
        'self_delivery_system'=>'integer',
        'open'=>'integer',
        'gst_code'=>'string',
        'off_day'=>'string',
        'gst'=>'string',
        'veg'=>'integer',
        'non_veg'=>'integer',
        'order_place_to_schedule_interval'=>'integer',
        'featured'=>'integer',
        'prescription_order'=>'boolean'
    ];

    protected $appends = ['gst_status','gst_code'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'gst'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function schedules()
    {
        return $this->hasMany(StoreSchedule::class)->orderBy('opening_time');
    }

    public function deliverymen()
    {
        return $this->hasMany(DeliveryMan::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function itemCampaigns()
    {
        return $this->hasMany(ItemCampaign::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Item::class);
    }

    public function getScheduleOrderAttribute($value)
    {
        return (boolean)(\App\CentralLogics\Helpers::schedule_order()?$value:0);
    }
    public function getRatingAttribute($value)
    {
        $ratings = json_decode($value, true);
        $rating5 = $ratings?$ratings[5]:0;
        $rating4 = $ratings?$ratings[4]:0;
        $rating3 = $ratings?$ratings[3]:0;
        $rating2 = $ratings?$ratings[2]:0;
        $rating1 = $ratings?$ratings[1]:0;
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public function getGstStatusAttribute()
    {
        return (boolean)($this->gst?json_decode($this->gst, true)['status']:0);
    }

    public function getGstCodeAttribute()
    {
        return (string)($this->gst?json_decode($this->gst, true)['code']:'');
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeDelivery($query)
    {
        $query->where('delivery',1);
    }

    public function scopeTakeaway($query)
    {
        $query->where('take_away',1);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', '=', 1);
    }

    public function scopeOpened($query)
    {
        return $query->where('active', 1);
    }


    public function scopeWithOpen($query,$longitude,$latitude)
    {
        $query->selectRaw('*, IF(((select count(*) from `store_schedule` where `stores`.`id` = `store_schedule`.`store_id` and `store_schedule`.`day` = '.now()->dayOfWeek.' and `store_schedule`.`opening_time` < "'.now()->format('H:i:s').'" and `store_schedule`.`closing_time` >"'.now()->format('H:i:s').'") > 0), true, false) as open,ST_Distance_Sphere(point(longitude, latitude),point('.$longitude.', '.$latitude.')) as distance');
    }

    public function scopeWeekday($query)
    {
        return $query->where('off_day', 'not like', "%".now()->dayOfWeek."%");
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }

    public function scopeType($query, $type)
    {
        if($type == 'veg')
        {
            return $query->where('veg', true);
        }
        else if($type == 'non_veg')
        {
            return $query->where('non_veg', true);
        }

        return $query;

    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Store;
use Carbon\Carbon;

class Vendor extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'password',
        'auth_token',
        'remember_token',
    ];

    public function order_transaction()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function todays_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereDate('created_at',now());
    }

    public function this_week_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function this_month_earning()
    {
        return $this->hasMany(OrderTransaction::class)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
    }

    public function todaysorders()
    {
        return $this->hasManyThrough(Order::class, Store::class)->whereDate('orders.created_at',now());
    }

    public function this_week_orders()
    {
        return $this->hasManyThrough(Order::class, Store::class)->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function this_month_orders()
    {
        return $this->hasManyThrough(Order::class, Store::class)->whereMonth('orders.created_at', date('m'))->whereYear('orders.created_at', date('Y'));
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Store::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
    public function withdrawrequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }
    public function wallet()
    {
        return $this->hasOne(StoreWallet::class);
    }

    public function userinfo()
    {
        return $this->hasOne(UserInfo::class,'vendor_id', 'id');
    }


}

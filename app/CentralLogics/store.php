<?php

namespace App\CentralLogics;

use App\Models\Store;
use App\Models\OrderTransaction;
use App\Models\Review;
use App\Models\StoreSchedule;
use Exception;

use function Symfony\Component\VarDumper\Dumper\esc;

class StoreLogic
{
    public static function get_stores( $zone_id, $filter, $type, $limit = 10, $offset = 1, $featured=false,$longitude=0,$latitude=0)
    {
        $paginator = Store::
        withOpen($longitude,$latitude)
        ->with(['discount'=>function($q){
            return $q->validate();
        }])
        ->whereHas('module',function($query){
            $query->active();
        })
        ->when($filter=='delivery', function($q){
            return $q->delivery();
        })
        ->when($filter=='take_away', function($q){
            return $q->takeaway();
        })
        ->when($featured, function($query){
            $query->featured();
        });
        if(config('module.current_module_data')) {
            $paginator = $paginator->whereHas('zone.modules', function($query){
                $query->where('modules.id', config('module.current_module_data')['id']);
            })->module(config('module.current_module_data')['id'])
            ->when(!config('module.current_module_data')['all_zone_service'], function($query)use($zone_id){
                $query->whereIn('zone_id', json_decode($zone_id,true));
            });
        } else {
            $paginator = $paginator->whereIn('zone_id', json_decode($zone_id,true));
        }
        $paginator = $paginator->Active()
        ->type($type)
        ->orderBy('open', 'desc')
        ->orderBy('distance')
        ->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'stores' => $paginator->items()
        ];
    }

    public static function get_latest_stores($zone_id, $limit = 10, $offset = 1, $type='all',$longitude=0,$latitude=0)
    {
        $paginator = Store::withOpen($longitude,$latitude)
        ->with(['discount'=>function($q){
            return $q->validate();
        }])
        ->when(config('module.current_module_data'), function($query)use($zone_id){
            $query->whereHas('zone.modules', function($query){
                $query->where('modules.id', config('module.current_module_data')['id']);
            })->module(config('module.current_module_data')['id']);
            if(!config('module.current_module_data')['all_zone_service']) {
                $query->whereIn('zone_id', json_decode($zone_id, true));
            }
        })
        ->Active()
        ->type($type)
        ->latest()
        ->limit(50)
        ->get();

        return [
            'total_size' => $paginator->count(),
            'limit' => $limit,
            'offset' => $offset,
            'stores' => $paginator
        ];
    }

    public static function get_popular_stores($zone_id, $limit = 10, $offset = 1, $type = 'all',$longitude=0,$latitude=0)
    {
        $paginator = Store::withOpen($longitude,$latitude)
        ->with(['discount'=>function($q){
            return $q->validate();
        }])
        ->when(config('module.current_module_data'), function($query)use($zone_id){
            $query->whereHas('zone.modules', function($query){
                $query->where('modules.id', config('module.current_module_data')['id']);
            })->module(config('module.current_module_data')['id']);
            if(!config('module.current_module_data')['all_zone_service']) {
                $query->whereIn('zone_id', json_decode($zone_id, true));
            }
        })
        ->Active()
        ->type($type)
        ->withCount('orders')
        ->orderBy('orders_count', 'desc')
        ->limit(50)
        ->get();

        return [
            'total_size' => $paginator->count(),
            'limit' => $limit,
            'offset' => $offset,
            'stores' => $paginator
        ];
    }

    public static function get_store_details($store_id)
    {
        return Store::with(['discount'=>function($q){
            return $q->validate();
        }, 'campaigns', 'schedules'])
        ->when(config('module.current_module_data'), function($query){
            $query->module(config('module.current_module_data')['id']);
        })
        ->active()->whereId($store_id)->first();
    }

    public static function calculate_store_rating($ratings)
    {
        $total_submit = $ratings[0]+$ratings[1]+$ratings[2]+$ratings[3]+$ratings[4];
        $rating = ($ratings[0]*5+$ratings[1]*4+$ratings[2]*3+$ratings[3]*2+$ratings[4])/($total_submit?$total_submit:1);
        return ['rating'=>$rating, 'total'=>$total_submit];
    }

    public static function update_store_rating($ratings, $product_rating)
    {
        $store_ratings = [1=>0 , 2=>0, 3=>0, 4=>0, 5=>0];
        if($ratings)
        {
            $store_ratings[1] = $ratings[4];
            $store_ratings[2] = $ratings[3];
            $store_ratings[3] = $ratings[2];
            $store_ratings[4] = $ratings[1];
            $store_ratings[5] = $ratings[0];
            $store_ratings[$product_rating] = $ratings[5-$product_rating] + 1;
        }
        else
        {
            $store_ratings[$product_rating] = 1;
        }
        return json_encode($store_ratings);
    }

    public static function search_stores($name, $zone_id, $category_id= null,$limit = 10, $offset = 1, $type = 'all',$longitude=0,$latitude=0)
    {
        $key = explode(' ', $name);
        $paginator = Store::whereHas('zone.modules', function($query){
            $query->where('modules.id', config('module.current_module_data')['id']);
        })->withOpen($longitude,$latitude)->with(['discount'=>function($q){
            return $q->validate();
        }])->weekday()->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })
        ->when(config('module.current_module_data'), function($query)use($zone_id){
            $query->module(config('module.current_module_data')['id']);
            if(!config('module.current_module_data')['all_zone_service']) {
                $query->whereIn('zone_id', json_decode($zone_id, true));
            }
        })
        ->when($category_id, function($query)use($category_id){
            $query->whereHas('items.category', function($q)use($category_id){
                return $q->whereId($category_id)->orWhere('parent_id', $category_id);
            });
        })
        ->active()->orderBy('open', 'desc')->orderBy('distance')->type($type)->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'stores' => $paginator->items()
        ];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_earning_data($vendor_id)
    {
        $monthly_earning = OrderTransaction::whereMonth('created_at', date('m'))->NotRefunded()->where('vendor_id', $vendor_id)->sum('store_amount');
        $weekly_earning = OrderTransaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->NotRefunded()->where('vendor_id', $vendor_id)->sum('store_amount');
        $daily_earning = OrderTransaction::whereDate('created_at', now())->NotRefunded()->where('vendor_id', $vendor_id)->sum('store_amount');

        return['monthely_earning'=>(float)$monthly_earning, 'weekly_earning'=>(float)$weekly_earning, 'daily_earning'=>(float)$daily_earning];
    }

    public static function format_export_stores($stores)
    {
        $storage = [];
        foreach($stores as $item)
        {
            if($item->stores->count()<1)
            {
                break;
            }
            $storage[] = [
                'id'=>$item->id,
                'ownerFirstName'=>$item->f_name,
                'ownerLastName'=>$item->l_name,
                'storeName'=>$item->stores[0]->name,
                'logo'=>$item->stores[0]->logo,
                'phone'=>$item->phone,
                'email'=>$item->email,
                'delivery_time'=>$item->delivery_time,
                'latitude'=>$item->stores[0]->latitude,
                'longitude'=>$item->stores[0]->longitude,
                'zone_id'=>$item->stores[0]->zone_id,
                'module_id'=>$item->stores[0]->module_id,
            ];
        }

        return $storage;
    }

    public static function insert_schedule(int $store_id, array $days=[0,1,2,3,4,5,6], String $opening_time='00:00:00', String $closing_time='23:59:59')
    {
        $data = array_map(function($item)use($store_id, $opening_time, $closing_time){
            return     ['store_id'=>$store_id,'day'=>$item,'opening_time'=>$opening_time,'closing_time'=>$closing_time];
        },$days);
        try{
            StoreSchedule::upsert($data,['store_id','day','opening_time','closing_time']);
            return true;
        }catch(Exception $e)
        {
            return $e;
        }
        return false;

    }

    public static function format_store_sales_export_data($items)
    {
        $data = [];
        foreach($items as $key=>$item)
        {

            $data[]=[
                '#'=>$key+1,
                translate('messages.name')=>$item->name,
                translate('messages.quantity')=>$item->orders->sum('quantity'),
                translate('messages.gross_sale')=>$item->orders->sum('price'),
                translate('messages.discount_given')=>$item->orders->sum('discount_on_item'),

            ];
        }
        return $data;
    }

    public static function format_store_summary_export_data($stores)
    {
        $data = [];
        foreach($stores as $key=>$store)
        {
            $delivered = $store->orders->where('order_status', 'delivered')->count();
            $canceled = $store->orders->where('order_status', 'canceled')->count();
            $refunded = $store->orders->where('order_status', 'refunded')->count();
            $total = $store->orders->count();
            $refund_requested = $store->orders->whereNotNull('refund_requested')->count();
            $data[]=[
                '#'=>$key+1,
                translate('Store')=>$store->name,
                translate('Total Order')=>$total,
                translate('Delivered Order')=>$delivered,
                translate('Total Amount')=>$store->orders->where('order_status','delivered')->sum('order_amount'),
                translate('Completion Rate')=>($store->orders->count() > 0 && $delivered > 0)? number_format((100*$delivered)/$store->orders->count(), config('round_up_to_digit')): 0,
                translate('Ongoing Rate')=>($store->orders->count() > 0 && $delivered > 0)? number_format((100*($store->orders->count()-($delivered+$canceled)))/$store->orders->count(), config('round_up_to_digit')): 0,
                translate('Cancelation Rate')=>($store->orders->count() > 0 && $canceled > 0)? number_format((100*$canceled)/$store->orders->count(), config('round_up_to_digit')): 0,
                translate('Refund Request')=>$refunded,

            ];
        }
        return $data;
    }
}

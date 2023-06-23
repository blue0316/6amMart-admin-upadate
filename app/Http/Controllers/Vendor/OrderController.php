<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\CouponLogic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function list($status)
    {
        Order::where(['checked' => 0])->where('store_id',Helpers::get_store_id())->update(['checked' => 1]);

        $orders = Order::with(['customer'])
        ->when($status == 'searching_for_deliverymen', function($query){
            return $query->SearchingForDeliveryman();
        })
        ->when($status == 'confirmed', function($query){
            return $query->whereIn('order_status',['confirmed', 'accepted'])->whereNotNull('confirmed');
        })
        ->when($status == 'pending', function($query){
            if(config('order_confirmation_model') == 'store' || Helpers::get_store_data()->self_delivery_system)
            {
                return $query->where('order_status','pending');
            }
            else
            {
                return $query->where('order_status','pending')->where('order_type', 'take_away');
            }
        })
        ->when($status == 'cooking', function($query){
            return $query->where('order_status','processing');
        })
        ->when($status == 'item_on_the_way', function($query){
            return $query->where('order_status','picked_up');
        })
        ->when($status == 'delivered', function($query){
            return $query->Delivered();
        })
        ->when($status == 'ready_for_delivery', function($query){
            return $query->where('order_status','handover');
        })
        ->when($status == 'refund_requested', function($query){
            return $query->RefundRequest();
        })
        ->when($status == 'refunded', function($query){
            return $query->Refunded();
        })
        ->when($status == 'scheduled', function($query){
            return $query->Scheduled()->where(function($q){
                if(config('order_confirmation_model') == 'store' || Helpers::get_store_data()->self_delivery_system)
                {
                    $q->whereNotIn('order_status',['failed','canceled', 'refund_requested', 'refunded']);
                }
                else
                {
                    $q->whereNotIn('order_status',['pending','failed','canceled', 'refund_requested', 'refunded'])->orWhere(function($query){
                        $query->where('order_status','pending')->where('order_type', 'take_away');
                    });
                }

            });
        })
        ->when($status == 'all', function($query){
            return $query->where(function($query){
                $query->whereNotIn('order_status',(config('order_confirmation_model') == 'store'|| Helpers::get_store_data()->self_delivery_system)?['failed','canceled', 'refund_requested', 'refunded']:['pending','failed','canceled', 'refund_requested', 'refunded'])
                ->orWhere(function($query){
                    return $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            });
        })
        ->when(in_array($status, ['pending','confirmed']), function($query){
            return $query->OrderScheduledIn(30);
        })
        ->StoreOrder()
        ->where('store_id',\App\CentralLogics\Helpers::get_store_id())
        ->orderBy('schedule_at', 'desc')
        ->paginate(config('default_pagination'));

        $status = translate('messages.'.$status);
        return view('vendor-views.order.list', compact('orders', 'status'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $orders=Order::where(['store_id'=>Helpers::get_store_id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->StoreOrder()->limit(100)->get();
        return response()->json([
            'view'=>view('vendor-views.order.partials._table',compact('orders'))->render()
        ]);
    }

    public function details(Request $request,$id)
    {
        $order = Order::with(['details', 'customer'=>function($query){
            return $query->withCount('orders');
        },'delivery_man'=>function($query){
            return $query->withCount('orders');
        }])->where(['id' => $id, 'store_id' => Helpers::get_store_id()])->first();
        if (isset($order)) {
            return view('vendor-views.order.order-view', compact('order'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'order_status' => 'required|in:confirmed,processing,handover,delivered,canceled'
        ],[
            'id.required' => 'Order id is required!'
        ]);

        $order = Order::where(['id' => $request->id, 'store_id' => Helpers::get_store_id()])->first();

        if($order->delivered != null)
        {
            Toastr::warning(translate('messages.cannot_change_status_after_delivered'));
            return back();
        }

        if($request['order_status']=='canceled' && !config('canceled_by_store'))
        {
            Toastr::warning(translate('messages.you_can_not_cancel_a_order'));
            return back();
        }

        if($request['order_status']=='canceled' && $order->confirmed)
        {
            Toastr::warning(translate('messages.you_can_not_cancel_after_confirm'));
            return back();
        }



        if($request['order_status']=='delivered' && $order->order_type != 'take_away' && !Helpers::get_store_data()->self_delivery_system)
        {
            Toastr::warning(translate('messages.you_can_not_delivered_delivery_order'));
            return back();
        }

        if($request['order_status'] =="confirmed")
        {
            if(!Helpers::get_store_data()->self_delivery_system && config('order_confirmation_model') == 'deliveryman' && $order->order_type != 'take_away')
            {
                Toastr::warning(translate('messages.order_confirmation_warning'));
                return back();
            }
        }

        if ($request->order_status == 'delivered') {
            $order_delivery_verification = (boolean)\App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value;
            if($order_delivery_verification)
            {
                if($request->otp)
                {
                    if($request->otp != $order->otp)
                    {
                        Toastr::warning(translate('messages.order_varification_code_not_matched'));
                        return back();
                    }
                }
                else
                {
                    Toastr::warning(translate('messages.order_varification_code_is_required'));
                    return back();
                }
            }

            if($order->transaction  == null)
            {
                if($order->payment_method == 'cash_on_delivery')
                {
                    $ol = OrderLogic::create_transaction($order,'store', null);
                }
                else{
                    $ol = OrderLogic::create_transaction($order,'admin', null);
                }


                if(!$ol)
                {
                    Toastr::warning(translate('messages.faield_to_create_order_transaction'));
                    return back();
                }
            }

            $order->payment_status = 'paid';

            $order->details->each(function($item, $key){
                if($item->item)
                {
                    $item->item->increment('order_count');
                }
            });
            $order->customer->increment('order_count');
        }
        if($request->order_status == 'canceled' || $request->order_status == 'delivered')
        {
            if($order->delivery_man)
            {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();
            }
        }

        if($request->order_status == 'delivered')
        {
            $order->store->increment('order_count');
            if($order->delivery_man)
            {
                $order->delivery_man->increment('order_count');
            }

        }

        $order->order_status = $request->order_status;
        $order[$request['order_status']] = now();
        $order->save();
        if(!Helpers::send_order_notification($order))
        {
            Toastr::warning(translate('messages.push_notification_faild'));
        }

        Toastr::success(translate('messages.order').' '.translate('messages.status_updated'));
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success('Delivery address updated!');
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::where(['id' => $id, 'store_id' => Helpers::get_store_id()])->first();
        return view('vendor-views.order.invoice', compact('order'));
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id, 'store_id' => Helpers::get_store_id()])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Payment reference code is added!');
        return back();
    }

    public function edit_order_amount(Request $request)
    {
        $request->validate([
            'order_amount' => 'required',

        ]);

        $order = Order::find($request->order_id);
        $store = Store::find($order->store_id);
        $coupon = null;
        $free_delivery_by = null;
        if ($order->coupon_code) {
            $coupon = Coupon::active()->where(['code' => $order->coupon_code])->first();
            if (isset($coupon)) {
                $staus = CouponLogic::is_valide($coupon, $order->user_id, $order->store_id);
                if ($staus == 407) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.coupon_expire')]
                        ]
                    ], 407);
                } else if ($staus == 406) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.coupon_usage_limit_over')]
                        ]
                    ], 406);
                } else if ($staus == 404) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.not_found')]
                        ]
                    ], 404);
                }
            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.not_found')]
                    ]
                ], 404);
            }
        }
        $product_price = $request->order_amount;
        $total_addon_price = 0;
        $store_discount_amount = $order->store_discount_amount;
        if($store_discount_amount == 0){
            $store_discount = Helpers::get_store_discount($store);
            if (isset($store_discount)) {
                if ($product_price + $total_addon_price < $store_discount['min_purchase']) {
                    $store_discount_amount = 0;
                }
    
                if ($store_discount['max_discount'] != 0 && $store_discount_amount > $store_discount['max_discount']) {
                    $store_discount_amount = $store_discount['max_discount'];
                }
            }
        }

        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;
        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

        $tax = $store->tax;
        $total_tax_amount = ($tax > 0) ? (($total_price * $tax) / 100) : 0;

        $free_delivery_over = BusinessSetting::where('key', 'free_delivery_over')->first()->value;
        if (isset($free_delivery_over)) {
            if ($free_delivery_over <= $product_price + $total_addon_price - $coupon_discount_amount - $store_discount_amount) {
                $order->delivery_charge = 0;
                $free_delivery_by = 'admin';
            }
        }

        if ($store->free_delivery) {
            $order->delivery_charge = 0;
            $free_delivery_by = 'vendor';
        }

        if ($coupon) {
            if ($coupon->coupon_type == 'free_delivery') {
                if ($coupon->min_purchase <= $product_price + $total_addon_price - $store_discount_amount) {
                    $order->delivery_charge = 0;
                    $free_delivery_by = 'admin';
                }
            }
            $coupon->increment('total_uses');
        }

        $order->coupon_discount_amount = round($coupon_discount_amount, config('round_up_to_digit'));
        $order->coupon_discount_title = $coupon ? $coupon->title : '';

        $order->store_discount_amount = round($store_discount_amount, config('round_up_to_digit'));
        $order->total_tax_amount = round($total_tax_amount, config('round_up_to_digit'));
        $order->order_amount = round($total_price + $total_tax_amount + $order->delivery_charge, config('round_up_to_digit'));
        $order->free_delivery_by = $free_delivery_by;
        $order->order_amount = $order->order_amount + $order->dm_tips;
        $order->save();

        Toastr::success(translate('messages.order_amount_updated'));
        return back();
    }
    public function edit_discount_amount(Request $request)
    {
        $request->validate([
            'discount_amount' => 'required',

        ]);

        $order = Order::find($request->order_id);
        $product_price = $order['order_amount']-$order['delivery_charge']-$order['total_tax_amount']-$order['dm_tips']+$order->store_discount_amount;
        if($request->discount_amount > $product_price)
        {
            Toastr::error(translate('messages.discount_amount_is_greater_then_product_amount'));
            return back();
        }
        $order->store_discount_amount = round($request->discount_amount, config('round_up_to_digit'));
        $order->order_amount = $product_price+$order['delivery_charge']+$order['total_tax_amount']+$order['dm_tips'] -$order->store_discount_amount;
        $order->save();

        Toastr::success(translate('messages.discount_amount_updated'));
        return back();
    }
}

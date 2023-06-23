<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class POSController extends Controller
{
    public function place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paid_amount' => 'required',
            'payment_method'=>'required',
            'store_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $store = $request->vendor->stores[0];
        $cart = $request['cart'];

        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;

        $order_details = [];
        $order = new Order();
        $order->id = 100000 + Order::all()->count() + 1;
        if (Order::find($order->id)) {
            $order->id = Order::latest()->first()->id + 1;
        }
        $order->payment_status = 'paid';
        $order->order_status = 'delivered';
        $order->order_type = 'pos';
        $order->payment_method = $request->payment_method;
        $order->transaction_reference = $request->paid_amount;
        $order->store_id = $store->id;
        $order->user_id = $request->user_id;
        $order->delivery_charge = 0;
        $order->original_delivery_charge = 0;
        $order->created_at = now();
        $order->updated_at = now();
        foreach ($cart as $c) {
            if(is_array($c))
            {
                $product = Item::find($c['item_id']);
                if ($product) {
                    if (count(json_decode($product['variations'], true)) > 0) {
                        $price = Helpers::variation_price($product, json_encode($c['variation']));
                    } else {
                        $price = $product['price'];
                    }

                    $product->tax = $store->tax;
                    $product = Helpers::product_data_formatting($product);
                    $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id',$c['add_on_ids'])->get(), $c['add_on_qtys']);
                    $or_d = [
                        'item_id' => $product->id,
                        'item_campaign_id' => null,
                        'food_details' => json_encode($product),
                        'quantity' => $c['quantity'],
                        'price' => $price,
                        'tax_amount' => Helpers::tax_calculate($product, $price),
                        'discount_on_item' => Helpers::product_discount_calculate($product, $price, $store),
                        'discount_type' => 'discount_on_product',
                        'variant' => json_encode($c['variant']),
                        'variation' => json_encode([$c['variation']]),
                        'add_ons' => json_encode($addon_data['addons']),
                        'total_add_on_price' => $addon_data['total_add_on_price'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $total_addon_price += $or_d['total_add_on_price'];
                    $product_price += $price*$or_d['quantity'];
                    $store_discount_amount += $or_d['discount_on_item']*$or_d['quantity'];
                    $order_details[] = $or_d;
                }
                else {
                    return response()->json([
                        'errors' => [
                            ['code' => 'campaign', 'message' => 'not found!']
                        ]
                    ], 401);
                }
            }
        }

        if(isset($request['discount']))
        {
            $store_discount_amount += $request['discount_type']=='percent'&&$request['discount']>0?((($product_price + $total_addon_price) * $request['discount'])/100):$request['discount'];
        }
        $store_discount_amount = round($store_discount_amount ,2);
        $total_price = $product_price + $total_addon_price - $store_discount_amount;
        $tax = isset($request['tax'])?$request['tax']:$store->tax;
        $total_tax_amount= ($tax > 0)?(($total_price * $tax)/100):0;
        $coupon_discount_amount = 0; 
        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

        $tax = $store->tax;
        $total_tax_amount= round(($tax > 0)?(($total_price * $tax)/100):0 ,2);
        
        try {
            $order->store_discount_amount= $store_discount_amount;
            $order->total_tax_amount= $total_tax_amount;
            $order->order_amount = $total_price + $total_tax_amount + $order->delivery_charge;
            $order->save();
            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;
            }
            OrderDetail::insert($order_details);
            return response()->json([
                'message' => translate('messages.order_placed_successfully'),
                'order_id' => $order->id,
                'total_ammount' => $total_price+$order->delivery_charge+$total_tax_amount
            ], 200);
        } catch (\Exception $e) {
            info($e);
        }
        Toastr::warning(translate('messages.failed_to_place_order'));
        return back();
    }

    public function order_list(Request $request)
    {
        $vendor = $request['vendor'];

        $orders = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->where('order_type', 'pos')
        ->latest()
        ->get();
        $orders= Helpers::order_data_formatting($orders, true);
        return response()->json($orders, 200);
    }

    public function generate_invoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];

        $order = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->where('id', $request->order_id)
        ->where('order_type', 'pos')
        ->first();

        if($order)
        {
            return response()->json([
                'view' => view('vendor-views.pos.order.invoice', compact('order'))->render(),
            ]);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.not_found')]
            ]
        ],404);
    }

    public function get_customers(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $key = explode(' ', $request['search']);
        $data = User::
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                ->orWhere('l_name', 'like', "%{$value}%")
                ->orWhere('phone', 'like', "%{$value}%");
            }
        })
        ->limit(8)
        ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);
        
        $data[]=(object)['id'=>false, 'text'=>translate('messages.walk_in_customer')];

        return response()->json($data);
    }
}

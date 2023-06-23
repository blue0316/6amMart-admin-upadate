<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Item;
use App\Models\Zone;
use App\Models\Order;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\OrderDetail;
use App\Mail\PlaceOrder;
use App\Models\ItemCampaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\CentralLogics\CouponLogic;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\CentralLogics\CustomerLogic;
use App\Http\Controllers\Controller;
use App\Models\ParcelCategory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Stripe\Product;
use App\Models\Refund;
use App\Models\RefundReason;

class OrderController extends Controller
{
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $order = Order::with(['store', 'delivery_man.rating', 'parcel_category', 'refund'])->withCount('details')->where(['id' => $request['order_id'], 'user_id' => $request->user()->id])->Notpos()->first();
        if ($order) {
            $order['store'] = $order['store'] ? Helpers::store_data_formatting($order['store']) : $order['store'];
            $order['delivery_address'] = $order['delivery_address'] ? json_decode($order['delivery_address']) : $order['delivery_address'];
            $order['delivery_man'] = $order['delivery_man'] ? Helpers::deliverymen_data_formatting([$order['delivery_man']]) : $order['delivery_man'];
            $order['refund_cancellation_note'] = $order['refund'] ? $order['refund']['admin_note'] : null;
            $order['refund_customer_note'] = $order['refund'] ? $order['refund']['customer_note'] : null;
            unset($order['details']);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'schedule_at', 'message' => translate('messages.not_found')]
                ]
            ], 404);
        }
        return response()->json($order, 200);
    }

    public function place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'payment_method' => 'required|in:cash_on_delivery,digital_payment,wallet',
            'order_type' => 'required|in:take_away,delivery,parcel',
            'store_id' => 'required_unless:order_type,parcel',
            'distance' => 'required_unless:order_type,take_away',
            'address' => 'required_unless:order_type,take_away',
            'longitude' => 'required_unless:order_type,take_away',
            'latitude' => 'required_unless:order_type,take_away',
            'parcel_category_id' => 'required_if:order_type,parcel',
            'receiver_details' => 'required_if:order_type,parcel',
            'charge_payer' => 'required_if:order_type,parcel|in:sender,receiver',
            'dm_tips' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $coupon = null;
        $delivery_charge = null;
        $schedule_at = $request->schedule_at ? \Carbon\Carbon::parse($request->schedule_at) : now();
        $store = null;
        $free_delivery_by = null;
        if ($request->order_type !== 'parcel') {
            if ($request->schedule_at && $schedule_at < now()) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order_time', 'message' => translate('messages.you_can_not_schedule_a_order_in_past')]
                    ]
                ], 406);
            }
            $store = Store::with('discount')->selectRaw('*, IF(((select count(*) from `store_schedule` where `stores`.`id` = `store_schedule`.`store_id` and `store_schedule`.`day` = ' . $schedule_at->format('w') . ' and `store_schedule`.`opening_time` < "' . $schedule_at->format('H:i:s') . '" and `store_schedule`.`closing_time` >"' . $schedule_at->format('H:i:s') . '") > 0), true, false) as open')->where('id', $request->store_id)->first();

            if (!$store) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order_time', 'message' => translate('messages.store_not_found')]
                    ]
                ], 404);
            }

            if ($request->schedule_at && !$store->schedule_order) {
                return response()->json([
                    'errors' => [
                        ['code' => 'schedule_at', 'message' => translate('messages.schedule_order_not_available')]
                    ]
                ], 406);
            }

            if ($store->open == false) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order_time', 'message' => translate('messages.store_is_closed_at_order_time')]
                    ]
                ], 406);
            }

            if ($request['coupon_code']) {
                $coupon = Coupon::active()->where(['code' => $request['coupon_code']])->first();
                if (isset($coupon)) {
                    $staus = CouponLogic::is_valide($coupon, $request->user()->id, $request['store_id']);
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
            $module_wise_delivery_charge = $store->zone->modules()->where('modules.id', $request->header('moduleId'))->first();
            if ($module_wise_delivery_charge) {
                $per_km_shipping_charge = $module_wise_delivery_charge->pivot->per_km_shipping_charge;
                $minimum_shipping_charge = $module_wise_delivery_charge->pivot->minimum_shipping_charge;
            } else {
                $per_km_shipping_charge = (float)BusinessSetting::where(['key' => 'per_km_shipping_charge'])->first()->value;
                $minimum_shipping_charge = (float)BusinessSetting::where(['key' => 'minimum_shipping_charge'])->first()->value;
            }
            $original_delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
            if ($request['order_type'] != 'take_away' && !$store->free_delivery && !isset($delivery_charge)) {
                if ($store->self_delivery_system) {
                    $per_km_shipping_charge = $store->per_km_shipping_charge;
                    $minimum_shipping_charge = $store->minimum_shipping_charge;
                    $original_delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
                    $delivery_charge = $original_delivery_charge;
                } else {
                    $delivery_charge = !isset($delivery_charge) ? $original_delivery_charge : $delivery_charge;
                }
            }
        } else {
            $parcel_category = ParcelCategory::findOrFail($request->parcel_category_id);
            if ($parcel_category->parcel_per_km_shipping_charge && $parcel_category->parcel_minimum_shipping_charge) {
                $per_km_shipping_charge = $parcel_category->parcel_per_km_shipping_charge;
                $minimum_shipping_charge = $parcel_category->parcel_minimum_shipping_charge;
            } else {
                $per_km_shipping_charge = (float)BusinessSetting::where(['key' => 'parcel_per_km_shipping_charge'])->first()->value;
                $minimum_shipping_charge = (float)BusinessSetting::where(['key' => 'parcel_minimum_shipping_charge'])->first()->value;
            }
            // $per_km_shipping_charge = (float)BusinessSetting::where(['key' => 'parcel_per_km_shipping_charge'])->first()->value;
            // $minimum_shipping_charge = (float)BusinessSetting::where(['key' => 'parcel_minimum_shipping_charge'])->first()->value;
            $original_delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
        }

        $zone = null;
        if ($request->latitude && $request->longitude) {
            $point = new Point($request->latitude, $request->longitude);
            $zone_id = isset($store) ? [$store->zone_id] : json_decode($request->header('zoneId'), true);
            $zone = Zone::whereIn('id', $zone_id)->contains('coordinates', $point)->latest()->first();

            if (!$zone) {
                $errors = [];
                array_push($errors, ['code' => 'coordinates', 'message' => translate('messages.out_of_coverage')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
        }

        $address = [
            'contact_person_name' => $request->contact_person_name ? $request->contact_person_name : $request->user()->f_name . ' ' . $request->user()->f_name,
            'contact_person_number' => $request->contact_person_number ? $request->contact_person_number : $request->user()->phone,
            'address_type' => $request->address_type ? $request->address_type : 'Delivery',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;
        $product_data = [];

        $order_details = [];
        $order = new Order();
        $order->id = 100000 + Order::count() + 1;
        if (Order::find($order->id)) {
            $order->id = Order::orderBy('id', 'desc')->first()->id + 1;
        }
        $order->user_id = $request->user()->id;
        $order->order_amount = $request['order_amount'];
        $order->payment_status = $request['payment_method'] == 'wallet' ? 'paid' : 'unpaid';
        $order->order_status = $request['payment_method'] == 'digital_payment' ? 'failed' : ($request->payment_method == 'wallet' ? 'confirmed' : 'pending');
        $order->coupon_code = $request['coupon_code'];
        $order->payment_method = $request->payment_method;
        $order->transaction_reference = null;
        $order->order_note = $request['order_note'];
        $order->order_type = $request['order_type'];
        $order->store_id = $request['store_id'];
        $order->delivery_charge = round($delivery_charge, config('round_up_to_digit')) ?? 0;
        $order->original_delivery_charge = round($original_delivery_charge, config('round_up_to_digit'));
        $order->delivery_address = json_encode($address);
        $order->schedule_at = $schedule_at;
        $order->scheduled = $request->schedule_at ? 1 : 0;
        $order->otp = rand(1000, 9999);
        $order->zone_id = isset($zone) ? $zone->id : end(json_decode($request->header('zoneId'), true));
        $order->module_id = $request->header('moduleId');
        $order->parcel_category_id = $request->parcel_category_id;
        $order->receiver_details = json_decode($request->receiver_details);
        if ($request['payment_method'] == 'wallet') {
            $order->confirmed = now();
        }
        $order->pending = now();
        $order->order_attachment = $request->has('order_attachment') ? Helpers::upload('order/', 'png', $request->file('order_attachment')) : null;
        $order->distance = $request->distance;
        $order->created_at = now();
        $order->updated_at = now();
        $order->charge_payer = $request->charge_payer;

        //Added DM TIPS
        $dm_tips_manage_status = BusinessSetting::where('key', 'dm_tips_status')->first()->value;
        if ($dm_tips_manage_status == 1) {
            $order->dm_tips = $request->dm_tips ?? 0;
        } else {
            $order->dm_tips = 0;
        }

        if ($request->order_type !== 'parcel') {
            foreach (json_decode($request['cart'], true) as $c) {
                if ($c['item_campaign_id'] != null) {
                    $product = ItemCampaign::with('module')->active()->find($c['item_campaign_id']);
                    if ($product) {
                        if($product->module->module_type == 'food' && $product->food_variations){
                            $product_variations = json_decode($product->food_variations, true);
                            $variations=[];
                            if (count($product_variations)) {
                                $variation_data = Helpers::get_varient($product_variations, $c['variation']);
                                $price = $product['price'] + $variation_data['price'];
                                $variations = $variation_data['variations'];
                            } else {
                                $price = $product['price'];
                            }
                            $product->tax = $store->tax;
                            $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());
                            $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id',$c['add_on_ids'])->get(), $c['add_on_qtys']);
        
                            $or_d = [
                                'item_id' => null,
                                'item_campaign_id' => $c['item_campaign_id'],
                                'item_details' => json_encode($product),
                                'quantity' => $c['quantity'],
                                'price' => round($price, config('round_up_to_digit')),
                                'tax_amount' => Helpers::tax_calculate($product, $price),
                                'discount_on_item' => Helpers::product_discount_calculate($product, $price, $store),
                                'discount_type' => 'discount_on_product',
                                'variant' => json_encode($c['variant']),
                                'variation' => json_encode($variations),
                                'add_ons' => json_encode($addon_data['addons']),
                                'total_add_on_price' => $addon_data['total_add_on_price'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $order_details[] = $or_d;
                            $total_addon_price += $or_d['total_add_on_price'];
                            $product_price += $price*$or_d['quantity'];
                            $store_discount_amount += $or_d['discount_on_item']*$or_d['quantity'];
                        }else{
                            if (count(json_decode($product['variations'], true)) > 0) {
                                $variant_data = Helpers::variation_price($product, json_encode($c['variation']));
                                $price = $variant_data['price'];
                                $stock = $variant_data['stock'];
                            } else {
                                $price = $product['price'];
                                $stock = $product->stock;
                            }
                            if (config('module.' . $product->module->module_type)['stock']) {
                                if ($c['quantity'] > $stock) {
                                    return response()->json([
                                        'errors' => [
                                            ['code' => 'campaign', 'message' => translate('messages.product_out_of_stock_warning', ['item' => $product->title])]
                                        ]
                                    ], 406);
                                }
    
                                $product_data[] = [
                                    'item' => clone $product,
                                    'quantity' => $c['quantity'],
                                    'variant' => count($c['variation']) > 0 ? $c['variation'][0]['type'] : null
                                ];
                            }
    
                            $product->tax = $store->tax;
                            $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());
                            $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id', $c['add_on_ids'])->get(), $c['add_on_qtys']);
                            $or_d = [
                                'item_id' => null,
                                'item_campaign_id' => $c['item_campaign_id'],
                                'item_details' => json_encode($product),
                                'quantity' => $c['quantity'],
                                'price' => $price,
                                'tax_amount' => Helpers::tax_calculate($product, $price),
                                'discount_on_item' => Helpers::product_discount_calculate($product, $price, $store),
                                'discount_type' => 'discount_on_product',
                                'variant' => json_encode($c['variant']),
                                'variation' => json_encode($c['variation']),
                                'add_ons' => json_encode($addon_data['addons']),
                                'total_add_on_price' => $addon_data['total_add_on_price'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $order_details[] = $or_d;
                            $total_addon_price += $or_d['total_add_on_price'];
                            $product_price += $price * $or_d['quantity'];
                            $store_discount_amount += $or_d['discount_on_item'] * $or_d['quantity'];
                        }
                    } else {
                        return response()->json([
                            'errors' => [
                                ['code' => 'campaign', 'message' => translate('messages.product_unavailable_warning')]
                            ]
                        ], 404);
                    }
                } else {
                    $product = Item::with('module')->active()->find($c['item_id']);
                    if ($product) {
                        if ($product->module->module_type == 'food' && $product->food_variations) {
                            // if (count(json_decode($product['variations'], true)) > 0) {
                            //     $price = Helpers::variation_price($product, json_encode($c['variation']));
                            // } else {
                            $product_variations = json_decode($product->food_variations, true);
                            $variations = [];
                            if (count($product_variations)) {
                                $variation_data = Helpers::get_varient($product_variations, $c['variation']);
                                // $price = Helpers::variation_price($product, json_encode($c['variation']));
                                $price = $product['price'] + $variation_data['price'];
                                $variations =  $variation_data['variations'];
                            } else {
                                $price = $product['price'];
                            }
                            $product->tax = $store->tax;
                            $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());
                            $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id', $c['add_on_ids'])->get(), $c['add_on_qtys']);
                            $or_d = [
                                'item_id' => $c['item_id'],
                                'item_campaign_id' => null,
                                'item_details' => json_encode($product),
                                'quantity' => $c['quantity'],
                                'price' => round($price, config('round_up_to_digit')),
                                'tax_amount' => round(Helpers::tax_calculate($product, $price), config('round_up_to_digit')),
                                'discount_on_item' => Helpers::product_discount_calculate($product, $price, $store),
                                'discount_type' => 'discount_on_product',
                                'variant' => json_encode($c['variant']),
                                'variation' => json_encode($variations),

                                // 'variation' => json_encode($c['variation']),
                                'add_ons' => json_encode($addon_data['addons']),
                                'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $total_addon_price += $or_d['total_add_on_price'];
                            $product_price += $price * $or_d['quantity'];
                            $store_discount_amount += $or_d['discount_on_item'] * $or_d['quantity'];
                            $order_details[] = $or_d;
                        } else {

                            if (count(json_decode($product['variations'], true)) > 0) {
                                $variant_data = Helpers::variation_price($product, json_encode($c['variation']));
                                $price = $variant_data['price'];
                                $stock = $variant_data['stock'];
                            } else {
                                $price = $product['price'];
                                $stock = $product->stock;
                            }

                            if (config('module.' . $product->module->module_type)['stock']) {
                                if ($c['quantity'] > $stock) {
                                    return response()->json([
                                        'errors' => [
                                            ['code' => 'campaign', 'message' => translate('messages.product_out_of_stock_warning', ['item' => $product->name])]
                                        ]
                                    ], 406);
                                }

                                $product_data[] = [
                                    'item' => clone $product,
                                    'quantity' => $c['quantity'],
                                    'variant' => count($c['variation']) > 0 ? $c['variation'][0]['type'] : null
                                ];
                            }

                            $product->tax = $store->tax;
                            $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());
                            $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id', $c['add_on_ids'])->get(), $c['add_on_qtys']);
                            $or_d = [
                                'item_id' => $c['item_id'],
                                'item_campaign_id' => null,
                                'item_details' => json_encode($product),
                                'quantity' => $c['quantity'],
                                'price' => round($price, config('round_up_to_digit')),
                                'tax_amount' => round(Helpers::tax_calculate($product, $price), config('round_up_to_digit')),
                                'discount_on_item' => Helpers::product_discount_calculate($product, $price, $store),
                                'discount_type' => 'discount_on_product',
                                'variant' => json_encode($c['variant']),
                                'variation' => json_encode($c['variation']),
                                'add_ons' => json_encode($addon_data['addons']),
                                'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $total_addon_price += $or_d['total_add_on_price'];
                            $product_price += $price * $or_d['quantity'];
                            $store_discount_amount += $or_d['discount_on_item'] * $or_d['quantity'];
                            $order_details[] = $or_d;
                        }
                    } else {
                        return response()->json([
                            'errors' => [
                                ['code' => 'item', 'message' => translate('messages.product_unavailable_warning')]
                            ]
                        ], 404);
                    }
                }
            }
            $store_discount = Helpers::get_store_discount($store);
            if (isset($store_discount)) {
                if ($product_price + $total_addon_price < $store_discount['min_purchase']) {
                    $store_discount_amount = 0;
                }

                if ($store_discount['max_discount'] != 0 && $store_discount_amount > $store_discount['max_discount']) {
                    $store_discount_amount = $store_discount['max_discount'];
                }
            }
            $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;
            $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

            $tax = ($store->tax > 0)?$store->tax:0;
            $order->tax_status = 'excluded';
    
            $tax_included =BusinessSetting::where(['key'=>'tax_included'])->first() ?  BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
            if ($tax_included ==  1){
                $order->tax_status = 'included';
            }
    
            $total_tax_amount=Helpers::product_tax($total_price,$tax,$order->tax_status =='included');
    
            $tax_a=$order->tax_status =='included'?0:$total_tax_amount;
            
            if ($store->minimum_order > $product_price + $total_addon_price) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order_time', 'message' => translate('messages.you_need_to_order_at_least', ['amount' => $store->minimum_order . ' ' . Helpers::currency_code()])]
                    ]
                ], 406);
            }

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
            $order->order_amount = round($total_price + $tax_a + $order->delivery_charge, config('round_up_to_digit'));
            $order->free_delivery_by = $free_delivery_by;
        } else {
            $point = new Point(json_decode($request->receiver_details, true)['latitude'], json_decode($request->receiver_details, true)['longitude']);
            $zone_id =  json_decode($request->header('zoneId'), true);
            $zone = Zone::whereIn('id', $zone_id)->contains('coordinates', $point)->latest()->first();
            if (!$zone) {
                $errors = [];
                array_push($errors, ['code' => 'receiver_details', 'message' => translate('messages.out_of_coverage')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
            $order->delivery_charge = round($original_delivery_charge, config('round_up_to_digit')) ?? 0;
            $order->original_delivery_charge = round($original_delivery_charge, config('round_up_to_digit'));
            $order->order_amount = round($order->delivery_charge, config('round_up_to_digit'));
        }

        //DM TIPS
        $order->order_amount = $order->order_amount + $order->dm_tips;
        if ($request->payment_method == 'wallet' && $request->user()->wallet_balance < $order->order_amount) {
            return response()->json([
                'errors' => [
                    ['code' => 'order_amount', 'message' => translate('messages.insufficient_balance')]
                ]
            ], 203);
        }
        if (isset($module_wise_delivery_charge ) && $request->payment_method == 'cash_on_delivery' && $module_wise_delivery_charge->pivot->maximum_cod_order_amount && $order->order_amount > $module_wise_delivery_charge->pivot->maximum_cod_order_amount) {
            return response()->json([
                'errors' => [
                    ['code' => 'order_amount', 'message' => translate('messages.amount_crossed_maximum_cod_order_amount')]
                ]
            ], 203);
        }

        try {
            DB::beginTransaction();
            $order->save();
            if ($request->order_type !== 'parcel') {
                foreach ($order_details as $key => $item) {
                    $order_details[$key]['order_id'] = $order->id;
                }
                OrderDetail::insert($order_details);
                if (count($product_data) > 0) {
                    foreach ($product_data as $item) {
                        ProductLogic::update_stock($item['item'], $item['quantity'], $item['variant'])->save();
                    }
                }
                $store->increment('total_order');
            }
            $customer = $request->user();
            $customer->zone_id = $order->zone_id;
            $customer->save();
            if ($request->payment_method == 'wallet') CustomerLogic::create_wallet_transaction($order->user_id, $order->order_amount, 'order_place', $order->id);
            DB::commit();
            Helpers::send_order_notification($order);
            //PlaceOrderMail
            try {
                if ($order->order_status == 'pending' && config('mail.status')) {
                    Mail::to($request->user()->email)->send(new PlaceOrder($order->id));
                }
            } catch (\Exception $ex) {
                info($ex);
            }
            //PlaceOrderMail end
            return response()->json([
                'message' => translate('messages.order_placed_successfully'),
                'order_id' => $order->id,
                'total_ammount' => $order->order_amount
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e], 403);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order_time', 'message' => translate('messages.failed_to_place_order')]
            ]
        ], 403);
    }
    public function prescription_place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required_unless:order_type,parcel',
            'order_attachment' => 'required',
            'distance' => 'required_unless:order_type,take_away',
            'address' => 'required_unless:order_type,take_away',
            'longitude' => 'required_unless:order_type,take_away',
            'latitude' => 'required_unless:order_type,take_away',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $coupon = null;
        $delivery_charge = null;
        $schedule_at = $request->schedule_at ? \Carbon\Carbon::parse($request->schedule_at) : now();
        $store = null;
        $free_delivery_by = null;
        if ($request->schedule_at && $schedule_at < now()) {
            return response()->json([
                'errors' => [
                    ['code' => 'order_time', 'message' => translate('messages.you_can_not_schedule_a_order_in_past')]
                ]
            ], 406);
        }
        $store = Store::with('discount')->selectRaw('*, IF(((select count(*) from `store_schedule` where `stores`.`id` = `store_schedule`.`store_id` and `store_schedule`.`day` = ' . $schedule_at->format('w') . ' and `store_schedule`.`opening_time` < "' . $schedule_at->format('H:i:s') . '" and `store_schedule`.`closing_time` >"' . $schedule_at->format('H:i:s') . '") > 0), true, false) as open')->where('id', $request->store_id)->first();

        if (!$store) {
            return response()->json([
                'errors' => [
                    ['code' => 'order_time', 'message' => translate('messages.store_not_found')]
                ]
            ], 404);
        }

        if ($request->schedule_at && !$store->schedule_order) {
            return response()->json([
                'errors' => [
                    ['code' => 'schedule_at', 'message' => translate('messages.schedule_order_not_available')]
                ]
            ], 406);
        }

        if ($store->open == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'order_time', 'message' => translate('messages.store_is_closed_at_order_time')]
                ]
            ], 406);
        }

        if ($request['coupon_code']) {
            $coupon = Coupon::active()->where(['code' => $request['coupon_code']])->first();
            if (isset($coupon)) {
                $staus = CouponLogic::is_valide($coupon, $request->user()->id, $request['store_id']);
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
        $module_wise_delivery_charge = $store->zone->modules()->where('modules.id', $request->header('moduleId'))->first();
        if ($module_wise_delivery_charge) {
            $per_km_shipping_charge = $module_wise_delivery_charge->pivot->per_km_shipping_charge;
            $minimum_shipping_charge = $module_wise_delivery_charge->pivot->minimum_shipping_charge;
        } else {
            $per_km_shipping_charge = (float)BusinessSetting::where(['key' => 'per_km_shipping_charge'])->first()->value;
            $minimum_shipping_charge = (float)BusinessSetting::where(['key' => 'minimum_shipping_charge'])->first()->value;
        }
        $original_delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
        if ($request['order_type'] != 'take_away' && !$store->free_delivery && !isset($delivery_charge)) {
            if ($store->self_delivery_system) {
                $per_km_shipping_charge = $store->per_km_shipping_charge;
                $minimum_shipping_charge = $store->minimum_shipping_charge;
                $original_delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
                $delivery_charge = $original_delivery_charge;
            } else {
                $delivery_charge = !isset($delivery_charge) ? $original_delivery_charge : $delivery_charge;
            }
        }
        

        $zone = null;
        if ($request->latitude && $request->longitude) {
            $point = new Point($request->latitude, $request->longitude);
            $zone_id = isset($store) ? [$store->zone_id] : json_decode($request->header('zoneId'), true);
            $zone = Zone::whereIn('id', $zone_id)->contains('coordinates', $point)->latest()->first();

            if (!$zone) {
                $errors = [];
                array_push($errors, ['code' => 'coordinates', 'message' => translate('messages.out_of_coverage')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
        }

        $address = [
            'contact_person_name' => $request->contact_person_name ? $request->contact_person_name : $request->user()->f_name . ' ' . $request->user()->f_name,
            'contact_person_number' => $request->contact_person_number ? $request->contact_person_number : $request->user()->phone,
            'address_type' => $request->address_type ? $request->address_type : 'Delivery',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $img_names = [];
        $images = [];
        if (!empty($request->file('order_attachment'))) {
            foreach ($request->order_attachment as $img) {
                $image_name = Helpers::upload('order/', 'png', $img);
                array_push($img_names, $image_name);
            }
            $images = $img_names;
        }else{
            $images = null;
        }

        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;
        $order = new Order();
        $order->id = 100000 + Order::count() + 1;
        if (Order::find($order->id)) {
            $order->id = Order::orderBy('id', 'desc')->first()->id + 1;
        }
        $order->user_id = $request->user()->id;
        $order->payment_status = 'unpaid';
        $order->order_status = 'pending';
        $order->coupon_code = $request['coupon_code'];
        $order->payment_method = 'cash_on_delivery';
        $order->transaction_reference = null;
        $order->order_note = $request['order_note'];
        $order->order_type = 'delivery';
        $order->store_id = $request['store_id'];
        $order->delivery_charge = round($delivery_charge, config('round_up_to_digit')) ?? 0;
        $order->original_delivery_charge = round($original_delivery_charge, config('round_up_to_digit'));
        $order->delivery_address = json_encode($address);
        $order->schedule_at = $schedule_at;
        $order->scheduled = $request->schedule_at ? 1 : 0;
        $order->otp = rand(1000, 9999);
        $order->zone_id = isset($zone) ? $zone->id : end(json_decode($request->header('zoneId'), true));
        $order->module_id = $request->header('moduleId');
        $order->pending = now();
        $order->order_attachment = json_encode($images);
        $order->distance = $request->distance;
        $order->prescription_order = 1;
        $order->created_at = now();
        $order->updated_at = now();

        //Added DM TIPS
        $dm_tips_manage_status = BusinessSetting::where('key', 'dm_tips_status')->first()->value;
        if ($dm_tips_manage_status == 1) {
            $order->dm_tips = $request->dm_tips ?? 0;
        } else {
            $order->dm_tips = 0;
        }

        $store_discount = Helpers::get_store_discount($store);
        if (isset($store_discount)) {
            if ($product_price + $total_addon_price < $store_discount['min_purchase']) {
                $store_discount_amount = 0;
            }

            if ($store_discount['max_discount'] != 0 && $store_discount_amount > $store_discount['max_discount']) {
                $store_discount_amount = $store_discount['max_discount'];
            }
        }
        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;
        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

        $tax = ($store->tax > 0)?$store->tax:0;
        $order->tax_status = 'excluded';

        $tax_included =BusinessSetting::where(['key'=>'tax_included'])->first() ?  BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
        if ($tax_included ==  1){
            $order->tax_status = 'included';
        }

        $total_tax_amount=Helpers::product_tax($total_price,$tax,$order->tax_status =='included');

        $tax_a=$order->tax_status =='included'?0:$total_tax_amount;

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
        $order->order_amount = round($total_price + $tax_a + $order->delivery_charge, config('round_up_to_digit'));
        $order->free_delivery_by = $free_delivery_by;
        $order->order_amount = $order->order_amount + $order->dm_tips;

        try {
            DB::beginTransaction();
            $order->save();
            $store->increment('total_order');
            $customer = $request->user();
            $customer->zone_id = $order->zone_id;
            $customer->save();
            DB::commit();
            Helpers::send_order_notification($order);
            //PlaceOrderMail
            try {
                if ($order->order_status == 'pending' && config('mail.status')) {
                    Mail::to($request->user()->email)->send(new PlaceOrder($order->id));
                }
            } catch (\Exception $ex) {
                info($ex->getMessage());
            }
            //PlaceOrderMail end
            return response()->json([
                'message' => translate('messages.order_placed_successfully'),
                'order_id' => $order->id
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e], 403);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order_time', 'message' => translate('messages.failed_to_place_order')]
            ]
        ], 403);
    }

    public function get_order_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $paginator = Order::with(['store', 'delivery_man.rating', 'parcel_category', 'refund:order_id,admin_note,customer_note'])->withCount('details')->where(['user_id' => $request->user()->id])->whereIn('order_status', ['delivered', 'canceled', 'refund_requested', 'refund_request_canceled', 'refunded', 'failed'])->Notpos()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $orders = array_map(function ($data) {
            $data['delivery_address'] = $data['delivery_address'] ? json_decode($data['delivery_address']) : $data['delivery_address'];
            $data['store'] = $data['store'] ? Helpers::store_data_formatting($data['store']) : $data['store'];
            $data['delivery_man'] = $data['delivery_man'] ? Helpers::deliverymen_data_formatting([$data['delivery_man']]) : $data['delivery_man'];
            $data['refund_cancellation_note'] = $data['refund'] ? $data['refund']['admin_note'] : null;
            $data['refund_customer_note'] = $data['refund'] ? $data['refund']['customer_note'] : null;
            return $data;
        }, $paginator->items());
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }


    public function get_running_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $paginator = Order::with(['store', 'delivery_man.rating', 'parcel_category'])->withCount('details')->where(['user_id' => $request->user()->id])->whereNotIn('order_status', ['delivered', 'canceled', 'refund_requested', 'refund_request_canceled', 'refunded', 'failed'])->Notpos()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $orders = array_map(function ($data) {
            $data['delivery_address'] = $data['delivery_address'] ? json_decode($data['delivery_address']) : $data['delivery_address'];
            $data['store'] = $data['store'] ? Helpers::store_data_formatting($data['store']) : $data['store'];
            $data['delivery_man'] = $data['delivery_man'] ? Helpers::deliverymen_data_formatting([$data['delivery_man']]) : $data['delivery_man'];
            return $data;
        }, $paginator->items());
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::with('details', 'parcel_category')->where('user_id', $request->user()->id)->find($request->order_id);

        $details = isset($order->details)?$order->details:null;
        if ($details != null && $details->count() > 0) {
            $details = Helpers::order_details_data_formatting($details);
            return response()->json($details, 200);
        } else if ($order->order_type == 'parcel' || $order->prescription_order == 1) {
            $order->delivery_address = json_decode($order->delivery_address, true);
            if($order->prescription_order && $order->order_attachment){
                $order->order_attachment = json_decode($order->order_attachment, true);
            }
            return response()->json(($order), 200);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.not_found')]
            ]
        ], 404);
    }

    public function cancel_order(Request $request)
    {
        $order = Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->Notpos()->first();
        if (!$order) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 403);
        } else if ($order->order_status == 'pending') {
            if (config('module.' . $order->module->module_type)['stock']) {
                foreach ($order->details as $detail) {
                    $variant = json_decode($detail['variation'], true);
                    $item = $detail->item;
                    if ($detail->campaign) {
                        $item = $detail->campaign;
                    }
                    ProductLogic::update_stock($item, -$detail->quantity, count($variant) ? $variant[0]['type'] : null)->save();
                }
            }
            $order->order_status = 'canceled';
            $order->canceled = now();
            $order->save();
            Helpers::send_order_notification($order);
            return response()->json(['message' => translate('messages.order_canceled_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.you_can_not_cancel_after_confirm')]
            ]
        ], 403);
    }

    public function refund_request(Request $request)
    {
        if (BusinessSetting::where(['key' => 'refund_active_status'])->first()->value == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('You can not request for a refund')]
                ]
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'customer_reason' => 'required|string|max:254',
            'refund_method' => 'nullable|string|max:100',
            'customer_note' => 'nullable|string|max:65535',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->Notpos()->first();
        if (!$order) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 404);
        } else if ($order->order_status == 'delivered' && $order->payment_status == 'paid') {

            $id_img_names = [];
            if (!empty($request->file('image'))) {
                foreach ($request->image as $img) {
                    $image = Helpers::upload('refund/', 'png', $img);
                    array_push($id_img_names, $image);
                }
                $image = json_encode($id_img_names);
            } else {
                $image = json_encode([]);
            }
            $refund_amount = round($order->order_amount - $order->delivery_charge - $order->dm_tips, config('round_up_to_digit'));
            $refund = new Refund();
            $refund->order_id = $order->id;
            $refund->user_id = $order->user_id;
            $refund->order_status = $order->order_status;
            $refund->refund_status = 'pending';
            $refund->refund_method = $request->refund_method ?? 'wallet';
            $refund->customer_reason = $request->customer_reason;
            $refund->customer_note = $request->customer_note;
            $refund->refund_amount = $refund_amount;
            $refund->image = $image;

            $order->order_status = 'refund_requested';
            $order->refund_requested = now();
            DB::beginTransaction();
            $refund->save();
            $order->save();
            DB::commit();
            return response()->json(['message' => translate('messages.refund_request_placed_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('Something went wrong')]
            ]
        ], 403);
    }

    public function update_payment_method(Request $request)
    {
        $config = Helpers::get_business_settings('cash_on_delivery');
        if ($config['status'] == 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'cod', 'message' => translate('messages.Cash on delivery order not available at this time')]
                ]
            ], 403);
        }
        $order = Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->Notpos()->first();
        if ($order) {
            Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'payment_method' => 'cash_on_delivery', 'order_status' => 'pending', 'pending' => now()
            ]);

            $fcm_token = $request->user()->cm_firebase_token;
            $value = Helpers::order_status_update_message('pending', $order->module->module_type);
            try {
                if ($value) {
                    $data = [
                        'title' => translate('messages.order_placed_successfully'),
                        'description' => $value,
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order_status',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'user_id' => $request->user()->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                if ($order->order_type == 'delivery' && !$order->scheduled) {
                    $data = [
                        'title' => translate('messages.order_placed_successfully'),
                        'description' => translate('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_topic($data, $order->store->zone->deliveryman_wise_topic, 'order_request');
                }
            } catch (\Exception $e) {
                info($e);
            }
            return response()->json(['message' => translate('messages.payment') . ' ' . translate('messages.method') . ' ' . translate('messages.updated_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.not_found')]
            ]
        ], 404);
    }

    public function refund_reasons()
    {
        $refund_reasons = RefundReason::where('status', 1)->get();
        return response()->json([
            'refund_reasons' => $refund_reasons
        ], 200);
    }
}

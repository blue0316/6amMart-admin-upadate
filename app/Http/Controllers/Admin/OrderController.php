<?php

namespace App\Http\Controllers\Admin;

use App\Models\Item;
use App\Models\Zone;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Refund;
use App\Models\Category;
use App\Scopes\ZoneScope;
use App\Scopes\StoreScope;
use App\Models\DeliveryMan;
use App\Models\OrderDetail;
use App\Models\ItemCampaign;
use App\Models\RefundReason;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\CentralLogics\OrderLogic;
use App\Models\DeliveryManWallet;
use App\CentralLogics\CouponLogic;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\CentralLogics\CustomerLogic;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Rap2hpoutre\FastExcel\FastExcel;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    public function list($status, Request $request)
    {
        // dd($status);
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }
        $module_id = $request->query('module_id', null);
        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
        }
        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'store'])
            ->when(isset($module_id), function ($query) use ($module_id) {
                return $query->module($module_id);
            })
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('store', function ($q) use ($request) {
                    return $q->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->whereRaw('created_at <> schedule_at');
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'pending', function ($query) {
                return $query->Pending();
            })
            ->when($status == 'accepted', function ($query) {
                return $query->AccepteByDeliveryman();
            })
            ->when($status == 'processing', function ($query) {
                return $query->Preparing();
            })
            ->when($status == 'item_on_the_way', function ($query) {
                return $query->ItemOnTheWay();
            })
            ->when($status == 'delivered', function ($query) {
                return $query->Delivered();
            })
            ->when($status == 'canceled', function ($query) {
                return $query->Canceled();
            })
            ->when($status == 'failed', function ($query) {
                return $query->failed();
            })
            ->when($status == 'refunded', function ($query) {
                return $query->Refunded();
            })
            ->when($status == 'requested', function ($query) {
                return $query->Refund_requested();
            })
            ->when($status == 'rejected', function ($query) {
                return $query->Refund_request_canceled();
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->Scheduled();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(($status != 'all' && $status != 'scheduled' && $status != 'canceled' && $status != 'rejected' && $status != 'requested' && $status != 'refunded' && $status != 'delivered' && $status != 'failed'), function ($query) {
                return $query->OrderScheduledIn(30);
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('order_status', $request->orderStatus);
            })
            ->when(isset($request->order_type), function ($query) use ($request) {
                return $query->where('order_type', $request->order_type);
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->StoreOrder()
            ->module(Config::get('module.current_module_id'))
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));
        $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
        $scheduled = isset($request->scheduled) ? $request->scheduled : 0;
        $vendor_ids = isset($request->vendor) ? $request->vendor : [];
        $zone_ids = isset($request->zone) ? $request->zone : [];
        $from_date = isset($request->from_date) ? $request->from_date : null;
        $to_date = isset($request->to_date) ? $request->to_date : null;
        $order_type = isset($request->order_type) ? $request->order_type : null;
        $total = $orders->total();


        return view('admin-views.order.list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'order_type'));
    }

    public function dispatch_list($module,$status, Request $request)
    {
        $module_id = $request->query('module_id', null);

        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
            $zone_ids = isset($request->zone) ? $request->zone : 0;
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'store'])
            ->whereHas('module', function($query) use($module){
                $query->where('id', $module);
            })
            ->when(isset($module_id), function ($query) use ($module_id) {
                return $query->module($module_id);
            })
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->StoreOrder()
            ->OrderScheduledIn(30)
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
        $scheduled = isset($request->scheduled) ? $request->scheduled : 0;
        $vendor_ids = isset($request->vendor) ? $request->vendor : [];
        $zone_ids = isset($request->zone) ? $request->zone : [];
        $from_date = isset($request->from_date) ? $request->from_date : null;
        $to_date = isset($request->to_date) ? $request->to_date : null;
        $total = $orders->total();

        return view('admin-views.order.distaptch_list', compact('orders','module', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'));
    }

    public function details(Request $request, $id)
    {
        $order = Order::with(['details', 'refund', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'customer' => function ($query) {
            return $query->withCount('orders');
        }, 'delivery_man' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where(['id' => $id])->first();
        if (isset($order)) {
            if (isset($order->store)) {
                $deliveryMen = DeliveryMan::where('zone_id', $order->store->zone_id)->available()->active()->get();
            } else {
                $deliveryMen = isset($order->zone_id) ? DeliveryMan::where('zone_id', $order->zone_id)->zonewise()->available()->active()->get() : [];
            }
            $category = $request->query('category_id', 0);
            // $sub_category = $request->query('sub_category', 0);
            $categories = Category::active()->get();
            $keyword = $request->query('keyword', false);
            $key = explode(' ', $keyword);
            $products = Item::withoutGlobalScope(StoreScope::class)->where('store_id', $order->store_id)
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('category', function ($q) use ($category) {
                        return $q->whereId($category)->orWhere('parent_id', $category);
                    });
                })
                ->when($keyword, function ($query) use ($key) {
                    return $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%");
                        }
                    });
                })
                ->latest()->paginate(10);
            $editing = false;
            if ($request->session()->has('order_cart')) {
                $cart = session()->get('order_cart');
                if (count($cart) > 0 && $cart[0]->order_id == $order->id) {
                    $editing = true;
                } else {
                    session()->forget('order_cart');
                }
            }

            $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);
            return view('admin-views.order.order-view', compact('order', 'deliveryMen', 'categories', 'products', 'category', 'keyword', 'editing'));
        } else {
            Toastr::info(translate('messages.no_more_orders'));
            return back();
        }
    }
    public function all_details(Request $request, $id)
    {
        $order = Order::with(['details', 'refund', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'customer' => function ($query) {
            return $query->withCount('orders');
        }, 'delivery_man' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where(['id' => $id])->first();
        if (isset($order)) {
            if (isset($order->store)) {
                $deliveryMen = DeliveryMan::where('zone_id', $order->store->zone_id)->available()->active()->get();
            } else {
                $deliveryMen = isset($order->zone_id) ? DeliveryMan::where('zone_id', $order->zone_id)->zonewise()->available()->active()->get() : [];
            }
            $category = $request->query('category_id', 0);
            // $sub_category = $request->query('sub_category', 0);
            $categories = Category::active()->get();
            $keyword = $request->query('keyword', false);
            $key = explode(' ', $keyword);
            $products = Item::withoutGlobalScope(StoreScope::class)->where('store_id', $order->store_id)
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('category', function ($q) use ($category) {
                        return $q->whereId($category)->orWhere('parent_id', $category);
                    });
                })
                ->when($keyword, function ($query) use ($key) {
                    return $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%");
                        }
                    });
                })
                ->latest()->paginate(10);
            $editing = false;
            if ($request->session()->has('order_cart')) {
                $cart = session()->get('order_cart');
                if (count($cart) > 0 && $cart[0]->order_id == $order->id) {
                    $editing = true;
                } else {
                    session()->forget('order_cart');
                }
            }

            $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);
            return view('admin-views.order.order-view', compact('order', 'deliveryMen', 'categories', 'products', 'category', 'keyword', 'editing'));
        } else {
            Toastr::info(translate('messages.no_more_orders'));
            return back();
        }
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $parcel_order = $request->parcel_order ?? false;
        $module_section_type = $request->module_section_type ?? false;
        $orders = Order::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        });
        if ($module_section_type) {
            $orders = $orders->module($module_section_type);
        }
        if ($parcel_order) {
            $orders = $orders->withOutGlobalScope(ZoneScope::class)->ParcelOrder();
        } else {
            $orders = $orders->StoreOrder();
        }
        $orders = $orders->limit(50)->get();

        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders', 'parcel_order'))->render()
        ]);
    }

    public function status(Request $request)
    {
        $order = Order::with(['details', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->withOutGlobalScope(ZoneScope::class)->find($request->id);

        if (in_array($order->order_status, ['refunded', 'failed'])) {
            Toastr::warning(translate('messages.you_can_not_change_the_status_of_a_completed_order'));
            return back();
        }
        if (in_array($order->order_status, ['refund_requested']) && BusinessSetting::where(['key' => 'refund_active_status'])->first()->value == false) {
            Toastr::warning(translate('Refund Option is not active. Please active it from Refund Settings'));
            return back();
        }

        if ($order['delivery_man_id'] == null && $request->order_status == 'out_for_delivery') {
            Toastr::warning(translate('messages.please_assign_deliveryman_first'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning(translate('messages.add_your_paymen_ref_first'));
            return back();
        }

        if ($request->order_status == 'delivered') {

            if ($order->transaction  == null) {
                if ($order->payment_method == "cash_on_delivery") {
                    if ($order->order_type == 'take_away') {
                        $ol = OrderLogic::create_transaction($order, 'store', null);
                    } else if ($order->delivery_man_id) {
                        $ol =  OrderLogic::create_transaction($order, 'deliveryman', null);
                    } else if ($order->user_id) {
                        $ol =  OrderLogic::create_transaction($order, false, null);
                    }
                } else {
                    $ol = OrderLogic::create_transaction($order, 'admin', null);
                }
                if (!$ol) {
                    Toastr::warning(translate('messages.faield_to_create_order_transaction'));
                    return back();
                }
            } else if ($order->delivery_man_id) {
                $order->transaction->update(['delivery_man_id' => $order->delivery_man_id]);
            }

            $order->payment_status = 'paid';
            if ($order->delivery_man) {
                $dm = $order->delivery_man;
                $dm->increment('order_count');
                $dm->current_orders = $dm->current_orders > 1 ? $dm->current_orders - 1 : 0;
                $dm->save();
            }
            $order->details->each(function ($item, $key) {
                if ($item->item) {
                    $item->item->increment('order_count');
                }
            });
            $order->customer->increment('order_count');
            if ($order->store) {
                $order->store->increment('order_count');
            }
            if ($order->parcel_category) {
                $order->parcel_category->increment('orders_count');
            }
        } else if ($request->order_status == 'refunded' && BusinessSetting::where('key', 'refund_active_status')->first()->value == 1) {
            if ($order->payment_status == "unpaid") {
                Toastr::warning(translate('messages.you_can_not_refund_a_cod_order'));
                return back();
            }
            if (isset($order->delivered)) {
                $rt = OrderLogic::refund_order($order);
                if (!$rt) {
                    Toastr::warning(translate('messages.faield_to_create_order_transaction'));
                    return back();
                }
            }
            $refund_method = $request->refund_method  ?? 'manual';
            $wallet_status = BusinessSetting::where('key', 'wallet_status')->first()->value;
            $refund_to_wallet = BusinessSetting::where('key', 'wallet_add_refund')->first()->value;
            if ($order->payment_status == "paid" && $wallet_status == 1 && $refund_to_wallet == 1) {
                $refund_amount = round($order->order_amount - $order->delivery_charge - $order->dm_tips, config('round_up_to_digit'));
                CustomerLogic::create_wallet_transaction($order->user_id, $refund_amount, 'order_refund', $order->id);
                Toastr::info(translate('Refunded amount added to customer wallet'));
                $refund_method = 'wallet';
            } else {
                Toastr::warning(translate('Customer Wallet Refund is not active.Plase Manage the Refund Amount Manually'));
                $refund_method = $request->refund_method  ?? 'manual';
            }
            Refund::where('order_id', $order->id)->update([
                'order_status' => 'refunded',
                'admin_note' => $request->admin_note ?? null,
                'refund_status' => 'approved',
                'refund_method' => $refund_method,
            ]);
            if ($order->delivery_man) {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders > 1 ? $dm->current_orders - 1 : 0;
                $dm->save();
            }
        } else if ($request->order_status == 'canceled') {
            if (in_array($order->order_status, ['delivered', 'canceled', 'refund_requested', 'refunded', 'failed', 'picked_up']) || $order->picked_up) {
                Toastr::warning(translate('messages.you_can_not_cancel_a_completed_order'));
                return back();
            }
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
            if ($order->delivery_man) {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders > 1 ? $dm->current_orders - 1 : 0;
                $dm->save();
            }
            OrderLogic::refund_before_delivered($order);
        }
        $order->order_status = $request->order_status;
        $order[$request->order_status] = now();
        $order->save();

        if (!Helpers::send_order_notification($order)) {
            Toastr::warning(translate('messages.push_notification_faild'));
        }

        Toastr::success(translate('messages.order_status_updated'));
        return back();
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([
                'errors' => [
                    ['delivery_man_id' => translate('messages.deliveryman') . ' ' . translate('messages.not_found')]
                ]
            ], 404);
        }
        $order = Order::withOutGlobalScope(ZoneScope::class)->find($order_id);

        $deliveryman = DeliveryMan::where('id', $delivery_man_id)->available()->active()->first();
        if ($order->delivery_man_id == $delivery_man_id) {
            return response()->json([
                'errors' => [
                    ['delivery_man_id' => translate('messages.order_already_assign_to_this_deliveryman')]
                ]
            ], 400);
        }
        if ($deliveryman) {
            if ($deliveryman->current_orders >= config('dm_maximum_orders')) {
                return response()->json([
                    'errors' => [
                        ['current_orders' => translate('messages.dm_maximum_order_exceed_warning')]
                    ]
                ], 404);
            }
            if ($order->delivery_man) {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders > 1 ? $dm->current_orders - 1 : 0;
                $dm->save();

                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.you_are_unassigned_from_a_order'),
                    'order_id' => '',
                    'image' => '',
                    'type' => 'assign'
                ];
                Helpers::send_push_notif_to_device($dm->fcm_token, $data);

                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'delivery_man_id' => $dm->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $order->delivery_man_id = $delivery_man_id;
            $order->order_status = in_array($order->order_status, ['pending', 'confirmed']) ? 'accepted' : $order->order_status;
            $order->accepted = now();
            $order->save();

            $deliveryman->current_orders = $deliveryman->current_orders + 1;
            $deliveryman->save();
            $deliveryman->increment('assigned_order_count');
            $fcm_token = $order->customer->cm_firebase_token;
            $value = Helpers::order_status_update_message('accepted',$order->module->module_type,$order->customer?
            $order->customer->current_language_key:'en');
            try {
                if ($value) {
                    $data = [
                        'title' => translate('messages.order_push_title'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order_status'
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);

                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'user_id' => $order->customer->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.you_are_assigned_to_a_order'),
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'assign'
                ];
                Helpers::send_push_notif_to_device($deliveryman->fcm_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'delivery_man_id' => $deliveryman->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                info($e);
                Toastr::warning(translate('messages.push_notification_faild'));
            }
            return response()->json([], 200);
        }
        return response()->json(['message' => 'Deliveryman not available!'], 400);
    }

    public function update_shipping(Request $request, Order $order)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
        ]);
        if ($request->latitude && $request->longitude) {
            $point = new Point($request->latitude, $request->longitude);
            $zone = Zone::where('id', $order->store->zone_id)->contains('coordinates', $point)->first();
            if (!$zone) {
                Toastr::error(translate('messages.out_of_coverage'));
                return back();
            }
        }
        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude
        ];

        $order->delivery_address = json_encode($address);
        $order->save();
        Toastr::success(translate('messages.delivery_address_updated'));
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::withOutGlobalScope(ZoneScope::class)->with(['details', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where('id', $id)->first();
        return view('admin-views.order.invoice', compact('order'));
    }

    public function print_invoice($id)
    {
        $order = Order::withOutGlobalScope(ZoneScope::class)->with(['details', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where('id', $id)->first();
        return view('admin-views.order.invoice-print', compact('order'))->render();
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        $request->validate([
            'transaction_reference' => 'max:30'
        ]);
        Order::where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(translate('messages.payment_reference_code_is_added'));
        return back();
    }

    public function restaurnt_filter($id)
    {
        session()->put('restaurnt_filter', $id);
        return back();
    }

    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);
        session()->put('order_filter', json_encode($request->all()));
        return back();
    }
    public function filter_reset(Request $request)
    {
        session()->forget('order_filter');
        return back();
    }

    public function add_to_cart(Request $request)
    {
        if ($request->item_type == 'item') {
            $product = Item::find($request->id);
        } else {
            $product = ItemCampaign::find($request->id);
        }

        if (isset($product->module_id) && $product->module->module_type == 'food' && $product->food_variations) {
            $data = new OrderDetail();
            if ($request->order_details_id) {
                $data['id'] = $request->order_details_id;
            }

            $data['item_id'] = $request->item_type == 'item' ? $product->id : null;
            $data['item_campaign_id'] = $request->item_type == 'campaign' ? $product->id : null;
            $data['item'] = $request->item_type == 'item' ? $product : null;
            $data['item_campaign'] = $request->item_type == 'campaign' ? $product : null;
            $data['order_id'] = $request->order_id;
            $variations = [];
            $price = 0;
            $addon_price = 0;
            $variation_price = 0;

            $product_variations = json_decode($product->food_variations, true);
            if ($request->variations && count($product_variations)) {
                foreach ($request->variations  as $key => $value) {

                    if ($value['required'] == 'on' &&  isset($value['values']) == false) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select items from') . ' ' . $value['name'],
                        ]);
                    }
                    if (isset($value['values'])  && $value['min'] != 0 && $value['min'] > count($value['values']['label'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select minimum ') . $value['min'] . translate(' For ') . $value['name'] . '.',
                        ]);
                    }
                    if (isset($value['values']) && $value['max'] != 0 && $value['max'] < count($value['values']['label'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select maximum ') . $value['max'] . translate(' For ') . $value['name'] . '.',
                        ]);
                    }
                }
                $variation_data = Helpers::get_varient($product_variations, $request->variations);
                $variation_price = $variation_data['price'];
                $variations = $variation_data['variations'];
            }
            $price = $product->price + $variation_price;
            $data['variation'] = json_encode($variations);
            $data['variant'] = '';
            // $data['variation_price'] = $variation_price;
            $data['quantity'] = $request['quantity'];
            $data['price'] = $price;
            $data['status'] = true;
            $data['discount_on_item'] = Helpers::product_discount_calculate($product, $price, $product->store);
            $data["discount_type"] = "discount_on_product";
            $data["tax_amount"] = Helpers::tax_calculate($product, $price);
            $add_ons = [];
            $add_on_qtys = [];

            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                    $add_on_qtys[] = $request['addon-quantity' . $id];
                }
                $add_ons = $request['addon_id'];
            }

            $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::withOutGlobalScope(StoreScope::class)->whereIn('id', $add_ons)->get(), $add_on_qtys);
            $data['add_ons'] = json_encode($addon_data['addons']);
            $data['total_add_on_price'] = $addon_data['total_add_on_price'];
            $cart = $request->session()->get('order_cart', collect([]));

            if (isset($request->cart_item_key)) {
                $cart[$request->cart_item_key] = $data;
                return response()->json([
                    'data' => 2
                ]);
            } else {
                $cart->push($data);
            }
        } else {

            $data = new OrderDetail();
            if ($request->order_details_id) {
                $data['id'] = $request->order_details_id;
            }

            $data['item_id'] = $request->item_type == 'item' ? $product->id : null;
            $data['item_campaign_id'] = $request->item_type == 'campaign' ? $product->id : null;
            $data['order_id'] = $request->order_id;
            $str = '';
            $price = 0;
            $addon_price = 0;

            //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request[$choice->name]);
                } else {
                    $str .= str_replace(' ', '', $request[$choice->name]);
                }
            }
            $data['variant'] = json_encode([]);
            $data['variation'] = json_encode([]);
            if ($request->session()->has('order_cart') && !isset($request->cart_item_key)) {
                if (count($request->session()->get('order_cart')) > 0) {
                    foreach ($request->session()->get('order_cart') as $key => $cartItem) {
                        // dd($cartItem);
                        if ($cartItem && $cartItem['item_id'] == $request['id'] && $cartItem['status'] == true) {
                            if (count(json_decode($cartItem['variation'], true)) > 0) {
                                if (json_decode($cartItem['variation'], true)[0]['type'] == $str) {
                                    return response()->json([
                                        'data' => 1
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'data' => 1
                                ]);
                            }
                        }
                    }
                }
            }
            //Check the string and decreases quantity for the stock
            if ($str != null) {
                $count = count(json_decode($product->variations));
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variations)[$i]->type == $str) {
                        $vr = json_decode($product->variations);
                        $price = $vr[$i]->price;
                        $stock = isset($vr[$i]->stock) ? $vr[$i]->stock : 0;
                    }
                }
                $data['variation'] = json_encode([["type" => $str, "price" => $price, "stock" => $stock]]);
            } else {
                $price = $product->price;
            }

            $data['quantity'] = $request['quantity'];
            $data['price'] = $price;
            $data['status'] = true;
            $data['discount_on_item'] = Helpers::product_discount_calculate($product, $price, $product->store);
            $data["discount_type"] = "discount_on_product";
            $data["tax_amount"] = Helpers::tax_calculate($product, $price);
            $add_ons = [];
            $add_on_qtys = [];

            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                    $add_on_qtys[] = $request['addon-quantity' . $id];
                }
                $add_ons = $request['addon_id'];
            }

            $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::withoutGlobalScope(StoreScope::class)->whereIn('id', $add_ons)->get(), $add_on_qtys);
            $data['add_ons'] = json_encode($addon_data['addons']);
            $data['total_add_on_price'] = $addon_data['total_add_on_price'];
            // dd($data);
            $cart = $request->session()->get('order_cart', collect([]));
            if (isset($request->cart_item_key)) {
                $cart[$request->cart_item_key] = $data;
                return response()->json([
                    'data' => 2
                ]);
            } else {
                $cart->push($data);
            }
        }
        return response()->json([
            'data' => 0
        ]);
    }

    public function remove_from_cart(Request $request)
    {
        $cart = $request->session()->get('order_cart', collect([]));
        $cart[$request->key]->status = false;
        $request->session()->put('order_cart', $cart);

        return response()->json([], 200);
    }

    public function edit(Request $request, Order $order)
    {
        $order = Order::with(['details', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'customer' => function ($query) {
            return $query->withCount('orders');
        }, 'delivery_man' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where(['id' => $order->id])->StoreOrder()->first();
        if ($request->cancle) {
            if ($request->session()->has(['order_cart'])) {
                session()->forget(['order_cart']);
            }
            return back();
        }
        $cart = collect([]);
        foreach ($order->details as $details) {
            unset($details['item_details']);
            $details['status'] = true;
            $cart->push($details);
        }

        if ($request->session()->has('order_cart')) {
            session()->forget('order_cart');
        } else {
            $request->session()->put('order_cart', $cart);
        }
        return back();
    }

    public function update(Request $request, Order $order)
    {
        $order = Order::with(['details', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'customer' => function ($query) {
            return $query->withCount('orders');
        }, 'delivery_man' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where(['id' => $order->id])->StoreOrder()->first();

        if (!$request->session()->has('order_cart')) {
            Toastr::error(translate('messages.order_data_not_found'));
            return back();
        }
        $cart = $request->session()->get('order_cart', collect([]));
        $store = $order->store;
        $coupon = null;
        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;
        if ($order->coupon_code) {
            $coupon = Coupon::where(['code' => $order->coupon_code])->first();
        }
        foreach ($cart as $c) {
            try {
                if ($c['status'] == true) {
                    unset($c['status']);
                    if ($c['item_campaign_id'] != null) {
                        $product = ItemCampaign::find($c['item_campaign_id']);
                        if ($product) {
    
                            $price = $c['price'];
    
                            $product = Helpers::product_data_formatting($product);
    
                            $c->item_details = json_encode($product);
                            $c->updated_at = now();
                            if (isset($c->id)) {
                                OrderDetail::where('id', $c->id)->update(
                                    [
                                        'item_id' => $c->item_id,
                                        'item_campaign_id' => $c->item_campaign_id,
                                        'item_details' => $c->item_details,
                                        'quantity' => $c->quantity,
                                        'price' => $c->price,
                                        'tax_amount' => $c->tax_amount,
                                        'discount_on_item' => $c->discount_on_item,
                                        'discount_type' => $c->discount_type,
                                        'variant' => $c->variant,
                                        'variation' => $c->variation,
                                        'add_ons' => $c->add_ons,
                                        'total_add_on_price' => $c->total_add_on_price,
                                        'updated_at' => $c->updated_at
                                    ]
                                );
                            } else {
                                $c->save();
                            }
    
                            $total_addon_price += $c['total_add_on_price'];
                            $product_price += $price * $c['quantity'];
                            $store_discount_amount += $c['discount_on_item'] * $c['quantity'];
                        } else {
                            Toastr::error(translate('messages.item_not_found'));
                            return back();
                        }
                    } else {
                        unset($c['item']);
                        unset($c['item_campaign']);
                        $product = Item::find($c['item_id']);
                        if ($product) {
                            $price = $c['price'];
    
                            $product = Helpers::product_data_formatting($product);
    
                            $c->item_details = json_encode($product);
                            $c->updated_at = now();
                            if (isset($c->id)) {
                                OrderDetail::where('id', $c->id)->update(
                                    [
                                        'item_id' => $c->item_id,
                                        'item_campaign_id' => $c->item_campaign_id,
                                        'item_details' => $c->item_details,
                                        'quantity' => $c->quantity,
                                        'price' => $c->price,
                                        'tax_amount' => $c->tax_amount,
                                        'discount_on_item' => $c->discount_on_item,
                                        'discount_type' => $c->discount_type,
                                        'variant' => $c->variant,
                                        'variation' => $c->variation,
                                        'add_ons' => $c->add_ons,
                                        'total_add_on_price' => $c->total_add_on_price,
                                        'updated_at' => $c->updated_at
                                    ]
                                );
                            } else {
                                $c->save();
                            }
    
                            $total_addon_price += $c['total_add_on_price'];
                            $product_price += $price * $c['quantity'];
                            $store_discount_amount += $c['discount_on_item'] * $c['quantity'];
                        } else {
                            Toastr::error(translate('messages.item_not_found'));
                            return back();
                        }
                    }
                } else {
                    $c->delete();
                }
            } catch (\Throwable $th) {
                info($th->getMessage());
            }
        }

        $store_discount = Helpers::get_store_discount($store);
        if (isset($store_discount)) {
            if ($product_price + $total_addon_price < $store_discount['min_purchase']) {
                $store_discount_amount = 0;
            }

            if ($store_discount_amount > $store_discount['max_discount'] && $store_discount_amount > $store_discount['max_discount']) {
                $store_discount_amount = $store_discount['max_discount'];
            }
        }
        $order->delivery_charge = $order->original_delivery_charge;
        if ($coupon) {
            if ($coupon->coupon_type == 'free_delivery') {
                $order->delivery_charge = 0;
                $coupon = null;
            }
        }

        if ($order->store->free_delivery || $order->order_type == 'take_away') {
            $order->delivery_charge = 0;
        }

        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;
        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

        $tax = $store->tax;
        $total_tax_amount = ($tax > 0) ? (($total_price * $tax) / 100) : 0;
        if ($store->minimum_order > $product_price + $total_addon_price) {
            Toastr::error(translate('messages.you_need_to_order_at_least', ['amount' => $store->minimum_order . ' ' . Helpers::currency_code()]));
            return back();
        }

        $free_delivery_over = BusinessSetting::where('key', 'free_delivery_over')->first()->value;
        if (isset($free_delivery_over)) {
            if ($free_delivery_over <= $product_price + $total_addon_price - $coupon_discount_amount - $store_discount_amount) {
                $order->delivery_charge = 0;
            }
        }
        $total_order_ammount = $total_price + $total_tax_amount + $order->delivery_charge;
        $adjustment = $order->order_amount - $total_order_ammount;

        $order->coupon_discount_amount = $coupon_discount_amount;
        $order->store_discount_amount = $store_discount_amount;
        $order->total_tax_amount = $total_tax_amount;
        $order->order_amount = $total_order_ammount;
        $order->adjusment = $adjustment;
        $order->edited = true;
        $order->save();
        session()->forget('order_cart');
        Toastr::success(translate('messages.order_updated_successfully'));
        return back();
    }

    public function quick_view(Request $request)
    {

        $product = $product = Item::findOrFail($request->product_id);
        $item_type = 'item';
        $order_id = $request->order_id;

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.order.partials._quick-view', compact('product', 'order_id', 'item_type'))->render(),
        ]);
    }

    public function quick_view_cart_item(Request $request)
    {
        $cart_item = session('order_cart')[$request->key];
        $order_id = $request->order_id;
        $item_key = $request->key;
        $product = $cart_item->item ? $cart_item->item : $cart_item->campaign;
        $item_type = $cart_item->item ? 'item' : 'campaign';

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.order.partials._quick-view-cart-item', compact('order_id', 'product', 'cart_item', 'item_key', 'item_type'))->render(),
        ]);
    }

    public function export_orders($file_type, $status, $type, Request $request)
    {
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        $module_id = $request->query('module_id', null);

        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'store'])
            ->when(isset($module_id), function ($query) use ($module_id) {
                return $query->module($module_id);
            })
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('store', function ($q) use ($request) {
                    return $q->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->whereRaw('created_at <> schedule_at');
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'pending', function ($query) {
                return $query->Pending();
            })
            ->when($status == 'accepted', function ($query) {
                return $query->AccepteByDeliveryman();
            })
            ->when($status == 'processing', function ($query) {
                return $query->Preparing();
            })
            ->when($status == 'item_on_the_way', function ($query) {
                return $query->ItemOnTheWay();
            })
            ->when($status == 'delivered', function ($query) {
                return $query->Delivered();
            })
            ->when($status == 'canceled', function ($query) {
                return $query->Canceled();
            })
            ->when($status == 'failed', function ($query) {
                return $query->failed();
            })
            ->when($status == 'refunded', function ($query) {
                return $query->Refunded();
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->Scheduled();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(($status != 'all' && $status != 'scheduled' && $status != 'canceled' && $status != 'refund_requested' && $status != 'refunded' && $status != 'delivered' && $status != 'failed'), function ($query) {
                return $query->OrderScheduledIn(30);
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('order_status', $request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                return $query->scheduled();
            })
            ->when(isset($request->order_type) && $type == 'order', function ($query) use ($request) {
                return $query->where('order_type', $request->order_type);
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->when($type == 'order', function ($query) {
                $query->StoreOrder();
            })
            ->when($type == 'parcel', function ($query) {
                $query->ParcelOrder();
            })
            ->module(Config::get('module.current_module_id'))
            ->orderBy('schedule_at', 'desc')
            ->get();
        if ($file_type == 'excel') {
            return (new FastExcel(OrderLogic::format_export_data($orders, $type)))->download('Orders.xlsx');
        } else if ($file_type == 'csv') {
            return (new FastExcel(OrderLogic::format_export_data($orders, $type)))->download('Orders.csv');
        }
        return (new FastExcel(OrderLogic::format_export_data($orders, $type)))->download('Orders.xlsx');
    }

    public function store_order_search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $orders = Order::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        return response()->json([
            'view' => view('admin-views.vendor.view.partials._order', compact('orders'))->render()
        ]);
    }
    public function store_order_export($type, $store_id)
    {
        $orders = Order::where('store_id', $store_id)->Notpos()->get();

        if ($type == 'excel') {
            return (new FastExcel(OrderLogic::format_export_data($orders, $type)))->download('Orders.xlsx');
        } else if ($type == 'csv') {
            return (new FastExcel(OrderLogic::format_export_data($orders, $type)))->download('Orders.csv');
        }
        return (new FastExcel(OrderLogic::format_export_data($orders, $type)))->download('Orders.xlsx');
    }


    public function refund_settings()
    {
        $refund_active_status = BusinessSetting::where(['key' => 'refund_active_status'])->first();
        $reasons = RefundReason::orderBy('id', 'desc')
            ->paginate(config('default_pagination'));
        return view('admin-views.refund.index', compact('refund_active_status', 'reasons'));
    }

    public function refund_reason(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:100',
        ]);
        RefundReason::create([
            'reason' => $request->reason,
        ]);

        Toastr::success(translate('Refund Reason Added Successfully'));
        return back();
    }

    public function reason_delete(Request $request)
    {
        $refund_reason = RefundReason::findOrFail($request->id);
        $refund_reason->delete();
        Toastr::success(translate('Refund Reason Deleted Successfully'));
        return back();
    }
    public function reason_edit(Request $request)
    {
        $request->validate([
            'reason' => 'required|max:100',
        ]);
        $refund_reason = RefundReason::findOrFail($request->reason_id);
        $refund_reason->reason = $request->reason;
        $refund_reason->save();

        Toastr::success(translate('Refund Reason Updated Successfully'));
        return back();
    }
    public function reason_status(Request $request)
    {
        $refund_reason = RefundReason::findOrFail($request->id);
        $refund_reason->status = $request->status;
        $refund_reason->save();
        Toastr::success(translate('messages.status_updated'));
        return back();
    }

    public function order_refund_rejection(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'admin_note' => 'nullable|string|max:65535',
        ]);
        Refund::where('order_id', $request->order_id)->update([
            'order_status' => 'refund_request_canceled',
            'admin_note' => $request->admin_note ?? null,
            'refund_status' => 'rejected',
            'refund_method' => 'canceled',
        ]);

        $order = Order::Notpos()->find($request->order_id);
        $order->order_status = 'refund_request_canceled';
        $order->save();

        Toastr::success(translate('Refund Rejection Successfully'));
        Helpers::send_order_notification($order);
        return back();
    }


    public function refund_mode()
    {
        $refund_mode = BusinessSetting::where('key', 'refund_active_status')->first();
        if (isset($refund_mode) == false) {
            DB::table('business_settings')->insert([
                'key' => 'refund_active_status',
                'value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('business_settings')->where(['key' => 'refund_active_status'])->update([
                'key' => 'refund_active_status',
                'value' => $refund_mode->value == 1 ? 0 : 1,
                'updated_at' => now(),
            ]);
        }

        if (isset($refund_mode) && $refund_mode->value) {
            return response()->json(['message' => 'Order Refund Request Mode is off.']);
        }
        return response()->json(['message' => 'Order Refund Request Mode is on.']);
    }
}

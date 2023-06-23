<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\CentralLogics\CouponLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\StoreLogic;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\Campaign;
use App\Models\Coupon;
use App\Models\WithdrawRequest;
use App\Models\Item;
use App\Models\Store;
use App\Models\VendorEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function get_profile(Request $request)
    {
        $vendor = $request['vendor'];
        $store = Helpers::store_data_formatting($vendor->stores[0], false);
        $discount=Helpers::get_store_discount($vendor->stores[0]);  
        unset($store['discount']);
        $store['discount']=$discount;
        $store['schedules']=$store->schedules()->get();
        $store['module']=$store->module;

        $vendor['order_count'] =$vendor->orders->where('order_type','!=','pos')->whereNotIn('order_status',['canceled','failed'])->count();
        $vendor['todays_order_count'] =$vendor->todaysorders->where('order_type','!=','pos')->whereNotIn('order_status',['canceled','failed'])->count();
        $vendor['this_week_order_count'] =$vendor->this_week_orders->where('order_type','!=','pos')->whereNotIn('order_status',['canceled','failed'])->count();
        $vendor['this_month_order_count'] =$vendor->this_month_orders->where('order_type','!=','pos')->whereNotIn('order_status',['canceled','failed'])->count();
        $vendor['member_since_days'] =$vendor->created_at->diffInDays();
        $vendor['cash_in_hands'] =$vendor->wallet?(float)$vendor->wallet->collected_cash:0;
        $vendor['balance'] =$vendor->wallet?(float)$vendor->wallet->balance:0;
        $vendor['total_earning'] =$vendor->wallet?(float)$vendor->wallet->total_earning:0;
        $vendor['todays_earning'] =(float)$vendor->todays_earning()->sum('store_amount');
        $vendor['this_week_earning'] =(float)$vendor->this_week_earning()->sum('store_amount');
        $vendor['this_month_earning'] =(float)$vendor->this_month_earning()->sum('store_amount');
        $vendor["stores"] = $store;
        if ($request['vendor_employee']) {
            $vendor_employee = $request['vendor_employee'];
            $role = $vendor_employee->role ? json_decode($vendor_employee->role->modules):[];
            $vendor["roles"] = $role;
            $vendor["employee_info"] = json_decode($request['vendor_employee']);
        }
        unset($vendor['orders']);
        unset($vendor['rating']);
        unset($vendor['todaysorders']);
        unset($vendor['this_week_orders']);
        unset($vendor['wallet']);
        unset($vendor['todaysorders']);
        unset($vendor['this_week_orders']);
        unset($vendor['this_month_orders']);

        return response()->json($vendor, 200);
    }

    public function active_status(Request $request)
    {
        $store = $request->vendor->stores[0];
        $store->active = $store->active?0:1;
        $store->save();
        return response()->json(['message' => $store->active?translate('messages.store_opened'):translate('messages.store_temporarily_closed')], 200);
    }

    public function get_earning_data(Request $request)
    {
        $vendor = $request['vendor'];
        $data= StoreLogic::get_earning_data($vendor->id);
        return response()->json($data, 200);
    }

    public function update_profile(Request $request)
    {
        $vendor = $request['vendor'];
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required|unique:vendors,phone,'.$vendor->id,
            'password'=>'nullable|min:6',
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'l_name.required' => translate('messages.Last name is required!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image = $request->file('image');

        if ($request->has('image')) {
            $imageName = Helpers::update('vendor/', $vendor->image, 'png', $request->file('image'));
        } else {
            $imageName = $vendor->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $vendor->password;
        }
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->phone = $request->phone;
        $vendor->image = $imageName;
        $vendor->password = $pass;
        $vendor->updated_at = now();
        $vendor->save();

        return response()->json(['message' => translate('messages.profile_updated_successfully')], 200);
    }

    public function get_current_orders(Request $request)
    {
        $vendor = $request['vendor'];

        $orders = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')

        ->where(function($query)use($vendor){
            if(config('order_confirmation_model') == 'store' || $vendor->stores[0]->self_delivery_system)
            {
                $query->whereIn('order_status', ['accepted','pending','confirmed', 'processing', 'handover','picked_up']);
            }
            else
            {
                $query->whereIn('order_status', ['confirmed', 'processing', 'handover','picked_up'])
                ->orWhere(function($query){
                    $query->where('payment_status','paid')->where('order_status', 'accepted');
                })
                ->orWhere(function($query){
                    $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            }
        })
        ->Notpos()
        ->orderBy('schedule_at', 'desc')
        ->get();
        $orders= Helpers::order_data_formatting($orders, true);
        return response()->json($orders, 200);
    }

    public function get_completed_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'status' => 'required|in:all,refunded,delivered',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];

        $paginator = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->when($request->status == 'all', function($query){
            return $query->whereIn('order_status', ['refunded', 'delivered']);
        })
        ->when($request->status != 'all', function($query)use($request){
            return $query->where('order_status', $request->status);
        })
        ->Notpos()
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $orders= Helpers::order_data_formatting($paginator->items(), true);
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }

    public function update_order_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required|in:confirmed,processing,handover,delivered,canceled'
        ]);

        $validator->sometimes('otp', 'required', function ($request) {
            return (Config::get('order_delivery_verification')==1 && $request['status']=='delivered');
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];

        $order = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->where('id', $request['order_id'])
        ->Notpos()
        ->first();

        if($request['order_status']=='canceled')
        {
            if(!config('canceled_by_store'))
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'status', 'message' => translate('messages.you_can_not_cancel_a_order')]
                    ]
                ], 403);
            }
            else if($order->confirmed)
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'status', 'message' => translate('messages.you_can_not_cancel_after_confirm')]
                    ]
                ], 403);
            }
        }

        if($request['status'] =="confirmed" && !$vendor->stores[0]->self_delivery_system && config('order_confirmation_model') == 'deliveryman' && $order->order_type != 'take_away')
        {
            return response()->json([
                'errors' => [
                    ['code' => 'order-confirmation-model', 'message' => translate('messages.order_confirmation_warning')]
                ]
            ], 403);
        }

        if($order->picked_up != null)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'status', 'message' => translate('messages.You_can_not_change_status_after_picked_up_by_delivery_man')]
                ]
            ], 403);
        }

        if($request['status']=='delivered' && $order->order_type != 'take_away' && !$vendor->stores[0]->self_delivery_system)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'status', 'message' => translate('messages.you_can_not_delivered_delivery_order')]
                ]
            ], 403);
        }
        if(Config::get('order_delivery_verification')==1 && $request['status']=='delivered' && $order->otp != $request['otp'])
        {
            return response()->json([
                'errors' => [
                    ['code' => 'otp', 'message' => 'Not matched']
                ]
            ], 401);
        }

        if ($request->status == 'delivered' && $order->transaction == null) {
            if($order->payment_method == 'cash_on_delivery')
            {
                $ol = OrderLogic::create_transaction($order,'store', null);
            }
            else
            {
                $ol = OrderLogic::create_transaction($order,'admin', null);
            }

            $order->payment_status = 'paid';
        }

        if($request->status == 'delivered')
        {
            $order->details->each(function($item, $key){
                if($item->item)
                {
                    $item->item->increment('order_count');
                }
            });
            $order->customer->increment('order_count');
            $order->store->increment('order_count');
        }
        if($request->status == 'canceled' || $request->status == 'delivered')
        {
            if($order->delivery_man)
            {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();
            }
        }

        $order->order_status = $request['status'];
        $order[$request['status']] = now();
        $order->save();
        Helpers::send_order_notification($order);

        return response()->json(['message' => 'Status updated'], 200);
    }

    public function get_order_details(Request $request)
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
        ->with(['customer','details'])
        ->where('id', $request['order_id'])
        ->Notpos()
        ->first();
        if(!$order){
            return response()->json(['errors'=>[['code'=>'order_id', 'message'=>trans('messages.order_data_not_found')]]],404);
        }
        $details = isset($order->details)?$order->details:null;
        if ($details != null && $details->count() > 0) {
            $details = $details = Helpers::order_details_data_formatting($details);
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

    public function get_order(Request $request)
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
        ->with(['customer','details','delivery_man'])
        ->where('id', $request['order_id'])
        ->first();
        if(!$order){
            return response()->json(['errors'=>[['code'=>'order_id', 'message'=>trans('messages.order_data_not_found')]]],404);
        }
        return response()->json(Helpers::order_data_formatting($order),200);
    }

    public function get_all_orders(Request $request)
    {
        $vendor = $request['vendor'];

        $orders = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->Notpos()
        ->orderBy('schedule_at', 'desc')
        ->get();
        $orders= Helpers::order_data_formatting($orders, true);
        return response()->json($orders, 200);
    }

    public function update_fcm_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        if (!$request->hasHeader('vendorType')) {
            $errors = [];
            array_push($errors, ['code' => 'vendor_type', 'message' => translate('messages.vendor_type_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $vendor_type= $request->header('vendorType');
        $vendor = $request['vendor'];
        if($vendor_type == 'owner'){
            Vendor::where(['id' => $vendor['id']])->update([
                'firebase_token' => $request['fcm_token']
            ]);
        }else{
            VendorEmployee::where(['id' => $vendor['id']])->update([
                'firebase_token' => $request['fcm_token']
            ]);

        }

        return response()->json(['message'=>'successfully updated!'], 200);
    }

    public function get_notifications(Request $request){
        $vendor = $request['vendor'];

        $notifications = Notification::active()->where(function($q) use($vendor){
            $q->whereNull('zone_id')->orWhere('zone_id', $vendor->stores[0]->zone_id);
        })->where('tergat', 'store')->where('created_at', '>=', \Carbon\Carbon::today()->subDays(7))->get();

        $notifications->append('data');

        $user_notifications = UserNotification::where('vendor_id', $vendor->id)->where('created_at', '>=', \Carbon\Carbon::today()->subDays(7))->get();

        $notifications =  $notifications->merge($user_notifications);

        try {
            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_basic_campaigns(Request $request)
    {
        $vendor = $request['vendor'];
        $store_id = $vendor->stores[0]->id;
        $module_id = $vendor->stores[0]->module_id;

        $campaigns=Campaign::with('stores')->module($module_id)->Running()->latest()->get();
        $data = [];

        foreach ($campaigns as $item) {
            $store_ids = count($item->stores)?$item->stores->pluck('id')->toArray():[];
            if($item->start_date)
            {
                $item['available_date_starts']=$item->start_date->format('Y-m-d');
                unset($item['start_date']);
            }
            if($item->end_date)
            {
                $item['available_date_ends']=$item->end_date->format('Y-m-d');
                unset($item['end_date']);
            }

            if (count($item['translations'])>0 ) {
                $translate = array_column($item['translations']->toArray(), 'value', 'key');
                $item['title'] = $translate['title'];
                $item['description'] = $translate['description'];
            }

            $item['is_joined'] = in_array($store_id, $store_ids)?true:false;
            unset($item['stores']);
            array_push($data, $item);
        }
        // $data = CampaignLogic::get_basic_campaigns($vendor->stores[0]->id, $request['limite'], $request['offset']);
        return response()->json($data, 200);
    }

    public function remove_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $campaign = Campaign::where('status', 1)->find($request->campaign_id);
        if(!$campaign)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'campaign', 'message'=>'Campaign not found or upavailable!']
                ]
            ]);
        }
        $store = $request['vendor']->stores[0];
        $campaign->stores()->detach($store);
        $campaign->save();
        return response()->json(['message'=>translate('messages.you_are_successfully_removed_from_the_campaign')], 200);
    }
    public function addstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $campaign = Campaign::where('status', 1)->find($request->campaign_id);
        if(!$campaign)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'campaign', 'message'=>'Campaign not found or upavailable!']
                ]
            ]);
        }
        $store = $request['vendor']->stores[0];
        $campaign->stores()->attach($store);
        $campaign->save();
        return response()->json(['message'=>translate('messages.you_are_successfully_joined_to_the_campaign')], 200);
    }

    public function get_items(Request $request)
    {
        $limit=$request->limit?$request->limit:25;
        $offset=$request->offset?$request->offset:1;

        $type = $request->query('type', 'all');

        $paginator = Item::withoutGlobalScope('translate')->with('tags')->type($type)->where('store_id', $request['vendor']->stores[0]->id)->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'items' => Helpers::product_data_formatting($paginator->items(), true, true, app()->getLocale())
        ];

        return response()->json($data, 200);
    }

    public function update_bank_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|max:191',
            'branch' => 'required|max:191',
            'holder_name' => 'required|max:191',
            'account_no' => 'required|max:191'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $bank = $request['vendor'];
        $bank->bank_name = $request->bank_name;
        $bank->branch = $request->branch;
        $bank->holder_name = $request->holder_name;
        $bank->account_no = $request->account_no;
        $bank->save();

        return response()->json(['message'=>translate('messages.bank_info_updated_successfully'),200]);
    }

    public function withdraw_list(Request $request)
    {
        $withdraw_req = WithdrawRequest::where('vendor_id', $request['vendor']->id)->latest()->get();

        $temp = [];
        $status = [
            0=>'Pending',
            1=>'Approved',
            2=>'Denied'
        ];
        foreach($withdraw_req as $item)
        {
            $item['status'] = $status[$item->approved];
            $item['requested_at'] = $item->created_at->format('Y-m-d H:i:s');
            $item['bank_name'] = $request['vendor']->bank_name;
            unset($item['created_at']);
            unset($item['approved']);
            $temp[] = $item;
        }

        return response()->json($temp, 200);
    }

    public function request_withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $w = $request['vendor']->wallet;
        if ($w->balance >= $request['amount']) {
            $data = [
                'vendor_id' => $w->vendor_id,
                'amount' => $request['amount'],
                'transaction_note' => null,
                'approved' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            try
            {
                DB::table('withdraw_requests')->insert($data);
                $w->increment('pending_withdraw', $request['amount']);
                return response()->json(['message'=>translate('messages.withdraw_request_placed_successfully')],200);
            }
            catch(\Exception $e)
            {
                return response()->json($e);
            }
        }
        return response()->json([
            'errors'=>[
                ['code'=>'amount', 'message'=>translate('messages.insufficient_balance')]
            ]
        ],403);
    }

    public function remove_account(Request $request)
    {
        $vendor = $request['vendor'];

        if(Order::where('store_id', $vendor->stores[0]->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count())
        {
            return response()->json(['errors'=>[['code'=>'on-going', 'message'=>translate('messages.user_account_delete_warning')]]],203);
        }

        if($vendor->wallet && $vendor->wallet->collected_cash > 0)
        {
            return response()->json(['errors'=>[['code'=>'on-going', 'message'=>translate('messages.user_account_wallet_delete_warning')]]],203);
        }

        if (Storage::disk('public')->exists('vendor/' . $vendor['image'])) {
            Storage::disk('public')->delete('vendor/' . $vendor['image']);
        }
        if (Storage::disk('public')->exists('store/' . $vendor->stores[0]->logo)) {
            Storage::disk('public')->delete('store/' . $vendor->stores[0]->logo);
        }

        if (Storage::disk('public')->exists('store/cover/' . $vendor->stores[0]->cover_photo)) {
            Storage::disk('public')->delete('store/cover/' . $vendor->stores[0]->cover_photo);
        }
        foreach($vendor->stores[0]->deliverymen as $dm) {
            if (Storage::disk('public')->exists('delivery-man/' . $dm['image'])) {
                Storage::disk('public')->delete('delivery-man/' . $dm['image']);
            }

            foreach (json_decode($dm['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }
        }
        $vendor->stores[0]->deliverymen()->delete();
        $vendor->stores()->delete();
        if($vendor->userinfo){
            $vendor->userinfo->delete();
        }
        $vendor->delete();
        return response()->json([]);
    }
    public function edit_order_amount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if($request->order_amount){
            $vendor = $request['vendor'];
            $vendor_store = Helpers::store_data_formatting($vendor->stores[0], false);
            $order = Order::find($request->order_id);
            if ($order->store_id != $vendor_store->id) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => translate('Order not found')]
                    ]
                ], 403);
            }
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
            $order->save();
        }

        if($request->discount_amount){
            $vendor = $request['vendor'];
            $vendor_store = Helpers::store_data_formatting($vendor->stores[0], false);
            $order = Order::find($request->order_id);
            if ($order->store_id != $vendor_store->id) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => translate('Order not found')]
                    ]
                ], 403);
            }
            $order = Order::find($request->order_id);
            $product_price = $order['order_amount']-$order['delivery_charge']-$order['total_tax_amount']-$order['dm_tips']+$order->store_discount_amount;
            if($request->discount_amount > $product_price)
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => translate('messages.discount_amount_is_greater_then_product_amount')]
                    ]
                ], 403);
            }
            $order->store_discount_amount = round($request->discount_amount, config('round_up_to_digit'));
            $order->order_amount = $product_price+$order['delivery_charge']+$order['total_tax_amount']+$order['dm_tips'] -$order->store_discount_amount;
            $order->save();
        }


        return response()->json(['message'=>translate('messages.order_updated_successfully')],200);
    }
}

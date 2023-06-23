<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Store;
use App\Models\Module;
use App\Models\Vendor;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\AccountTransaction;
use App\Models\OrderTransaction;
use App\Models\UserInfo;
use App\Scopes\StoreScope;
use App\Models\StoreWallet;
use Illuminate\Http\Request;
use App\Models\StoreSchedule;
use App\CentralLogics\Helpers;
use App\Models\WithdrawRequest;
use App\CentralLogics\StoreLogic;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Config;


class VendorController extends Controller
{
    public function index()
    {
        return view('admin-views.vendor.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'name' => 'required|max:191',
            'address' => 'required|max:1000',
            'latitude' => 'required',
            'longitude' => 'required',
            'email' => 'required|unique:vendors',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors',
            'minimum_delivery_time' => 'required',
            'maximum_delivery_time' => 'required',
            'delivery_time_type'=>'required',
            'password' => 'required|min:6',
            'zone_id' => 'required',
            // 'module_id' => 'required',
            'logo' => 'required',
            'tax' => 'required'
        ], [
            'f_name.required' => translate('messages.first_name_is_required')
        ]);

        if($request->zone_id)
        {
            $point = new Point($request->latitude, $request->longitude);
            $zone = Zone::contains('coordinates', $point)->where('id', $request->zone_id)->first();
            if(!$zone){
                $validator->getMessageBag()->add('latitude', translate('messages.coordinates_out_of_zone'));
                return back()->withErrors($validator)
                        ->withInput();
            }
        }
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $vendor = new Vendor();
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = bcrypt($request->password);
        $vendor->save();

        $store = new Store;
        $store->name = $request->name;
        $store->phone = $request->phone;
        $store->email = $request->email;
        $store->logo = Helpers::upload('store/', 'png', $request->file('logo'));
        $store->cover_photo = Helpers::upload('store/cover/', 'png', $request->file('cover_photo'));
        $store->address = $request->address;
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->vendor_id = $vendor->id;
        $store->zone_id = $request->zone_id;
        $store->tax = $request->tax;
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->module_id = Config::get('module.current_module_id');
        $store->save();
        $store->module->increment('stores_count');
        if(config('module.'.$store->module->module_type)['always_open'])
        {
            StoreLogic::insert_schedule($store->id);
        }
        // $store->zones()->attach($request->zone_ids);
        Toastr::success(translate('messages.store').translate('messages.added_successfully'));
        return redirect('admin/store/list');
    }

    public function edit($id)
    {
        if(env('APP_MODE')=='demo' && $id == 2)
        {
            Toastr::warning(translate('messages.you_can_not_edit_this_store_please_add_a_new_store_to_edit'));
            return back();
        }
        $store = Store::findOrFail($id);
        return view('admin-views.vendor.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'name' => 'required|max:191',
            'email' => 'required|unique:vendors,email,'.$store->vendor->id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors,phone,'.$store->vendor->id,
            'zone_id'=>'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'tax' => 'required',
            'password' => 'nullable|min:6',
            'minimum_delivery_time' => 'required',
            'maximum_delivery_time' => 'required',
            'delivery_time_type'=>'required'
        ], [
            'f_name.required' => translate('messages.first_name_is_required')
        ]);

        if($request->zone_id)
        {
            $point = new Point($request->latitude, $request->longitude);
            $zone = Zone::contains('coordinates', $point)->where('id', $request->zone_id)->first();
            if(!$zone){
                $validator->getMessageBag()->add('latitude', translate('messages.coordinates_out_of_zone'));
                return back()->withErrors($validator)
                        ->withInput();
            }
        }
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }
        $vendor = Vendor::findOrFail($store->vendor->id);
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = strlen($request->password)>1?bcrypt($request->password):$store->vendor->password;
        $vendor->save();

        $store->email = $request->email;
        $store->phone = $request->phone;
        $store->logo = $request->has('logo') ? Helpers::update('store/', $store->logo, 'png', $request->file('logo')) : $store->logo;
        $store->cover_photo = $request->has('cover_photo') ? Helpers::update('store/cover/', $store->cover_photo, 'png', $request->file('cover_photo')) : $store->cover_photo;
        $store->name = $request->name;
        $store->address = $request->address;
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->zone_id = $request->zone_id;
        $store->tax = $request->tax;
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->save();
        if ($vendor->userinfo) {
            $userinfo = $vendor->userinfo;
            $userinfo->f_name = $store->name;
            $userinfo->l_name = '';
            $userinfo->email = $store->email;
            $userinfo->image = $store->logo;
            $userinfo->save();
        }
        Toastr::success(translate('messages.store').translate('messages.updated_successfully'));
        return redirect('admin/store/list');
    }

    public function destroy(Request $request, Store $store)
    {
        if(env('APP_MODE')=='demo' && $store->id == 2)
        {
            Toastr::warning(translate('messages.you_can_not_delete_this_store_please_add_a_new_store_to_delete'));
            return back();
        }
        if (Storage::disk('public')->exists('store/' . $store['logo'])) {
            Storage::disk('public')->delete('store/' . $store['logo']);
        }
        $store->delete();

        $vendor = Vendor::findOrFail($store->vendor->id);
        if($vendor->userinfo){
            $vendor->userinfo->delete();
        }
        $vendor->delete();
        Toastr::success(translate('messages.store').' '.translate('messages.removed'));
        return back();
    }

    public function view(Store $store, $tab=null, $sub_tab='cash')
    {
        $wallet = $store->vendor->wallet;
        if(!$wallet)
        {
            $wallet= new StoreWallet();
            $wallet->vendor_id = $store->vendor->id;
            $wallet->total_earning= 0.0;
            $wallet->total_withdrawn=0.0;
            $wallet->pending_withdraw=0.0;
            $wallet->created_at=now();
            $wallet->updated_at=now();
            $wallet->save();
        }
        if($tab == 'settings')
        {
            return view('admin-views.vendor.view.settings', compact('store'));
        }
        else if($tab == 'order')
        {
            return view('admin-views.vendor.view.order', compact('store'));
        }
        else if($tab == 'item')
        {
            return view('admin-views.vendor.view.product', compact('store'));
        }
        else if($tab == 'discount')
        {
            return view('admin-views.vendor.view.discount', compact('store'));
        }
        else if($tab == 'transaction')
        {
            return view('admin-views.vendor.view.transaction', compact('store', 'sub_tab'));
        }

        else if($tab == 'reviews')
        {
            return view('admin-views.vendor.view.review', compact('store', 'sub_tab'));

        } else if ($tab == 'conversations') {
            $user = UserInfo::where(['vendor_id' => $store->vendor->id])->first();
            if ($user) {
                $conversations = Conversation::with(['sender', 'receiver', 'last_message'])->WhereUser($user->id)
                    ->paginate(8);
            } else {
                $conversations = [];
            }
            return view('admin-views.vendor.view.conversations', compact('store', 'sub_tab', 'conversations'));
        }
        return view('admin-views.vendor.view.index', compact('store', 'wallet'));
    }

    public function view_tab(Store $store)
    {

        Toastr::error(translate('messages.unknown_tab'));
        return back();
    }

    public function list(Request $request)
    {
        $zone_id = $request->query('zone_id', 'all');
        $type = $request->query('type', 'all');
        $module_id = $request->query('module_id', 'all');
        $stores = Store::with('vendor','module')->whereHas('vendor', function($query){
            return $query->where('status', 1);
        })
        ->when(is_numeric($zone_id), function($query)use($zone_id){
                return $query->where('zone_id', $zone_id);
        })
        ->when(is_numeric($module_id), function($query)use($request){
            return $query->module($request->query('module_id'));
        })
        ->module(Config::get('module.current_module_id'))
        ->with('vendor','module')->type($type)->latest()->paginate(config('default_pagination'));
        $zone = is_numeric($zone_id)?Zone::findOrFail($zone_id):null;
        return view('admin-views.vendor.list', compact('stores', 'zone','type'));
    }

    public function pending_requests(Request $request)
    {   
        $zone_id = $request->query('zone_id', 'all');
        $search_by = $request->query('search_by');
        $key = explode(' ', $search_by);
        $type = $request->query('type', 'all');
        $module_id = $request->query('module_id', 'all');
        $stores = Store::with('vendor','module')->whereHas('vendor', function($query){
            return $query->where('status', null);
        })
        ->when(is_numeric($zone_id), function($query)use($zone_id){
                return $query->where('zone_id', $zone_id);
        })
        ->when(is_numeric($module_id), function($query)use($request){
            return $query->module($request->query('module_id'));
        })
        ->when($search_by, function($query)use($key){
            return $query->where(function($query)use($key){
                $query->orWhereHas('vendor',function ($q) use ($key) {
                    $q->where(function($q)use($key){
                        foreach ($key as $value) {
                            $q->orWhere('f_name', 'like', "%{$value}%")
                                ->orWhere('l_name', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%");
                        }
                    });
                })->orWhere(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%");
                    }
                });
            });
        })
        ->module(Config::get('module.current_module_id'))
        ->type($type)->latest()->paginate(config('default_pagination'));
        $zone = is_numeric($zone_id)?Zone::findOrFail($zone_id):null;
        return view('admin-views.vendor.pending_requests', compact('stores', 'zone','type', 'search_by'));
    }

    public function deny_requests(Request $request)
    {
        $search_by = $request->query('search_by');
        $key = explode(' ', $search_by);
        $zone_id = $request->query('zone_id', 'all');
        $type = $request->query('type', 'all');
        $module_id = $request->query('module_id', 'all');
        $stores = Store::with('vendor','module')->whereHas('vendor', function($query){
            return $query->where('status', 0);
        })
        ->when(is_numeric($zone_id), function($query)use($zone_id){
                return $query->where('zone_id', $zone_id);
        })
        ->when(is_numeric($module_id), function($query)use($request){
            return $query->module($request->query('module_id'));
        })
        ->when($search_by, function($query)use($key){
            return $query->where(function($query)use($key){
                $query->orWhereHas('vendor',function ($q) use ($key) {
                    $q->where(function($q)use($key){
                        foreach ($key as $value) {
                            $q->orWhere('f_name', 'like', "%{$value}%")
                                ->orWhere('l_name', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%");
                        }
                    });
                })->orWhere(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%");
                    }
                });
            });
        })
        ->module(Config::get('module.current_module_id'))
        ->type($type)->latest()->paginate(config('default_pagination'));
        $zone = is_numeric($zone_id)?Zone::findOrFail($zone_id):null;
        return view('admin-views.vendor.deny_requests', compact('stores', 'zone','type', 'search_by'));
    }

    public function export(Request $request){
        $zone_id = $request->query('zone_id', 'all');
        $module_id = $request->query('module_id', 'all');
        $stores = Store::whereHas('vendor', function($query){
            return $query->where('status', 1);
        })
        ->when(is_numeric($zone_id), function($query)use($zone_id){
                return $query->where('zone_id', $zone_id);
        })
        ->when(is_numeric($module_id), function($query)use($request){
            return $query->module($request->query('module_id'));
        })
        ->module(Config::get('module.current_module_id'))
        ->with('vendor','module')->get();
        if($request->type == 'excel'){
            return (new FastExcel(Helpers::export_stores($stores)))->download('Stores.xlsx');
        }elseif($request->type == 'csv'){
            return (new FastExcel(Helpers::export_stores($stores)))->download('Stores.csv');
        }
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $stores=Store::whereHas('vendor',function ($q) {
            $q->where('status', 1);
        })->where(function($query)use($key){
            $query->orWhereHas('vendor',function ($q) use ($key) {
                $q->where(function($q)use($key){
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%");
                    }
                });
            })->orWhere(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
        })
        ->module(Config::get('module.current_module_id'))
        ->get();
        $total=$stores->count();
        return response()->json([
            'view'=>view('admin-views.vendor.partials._table',compact('stores'))->render(), 'total'=>$total
        ]);
    }

    public function get_stores(Request $request){
        $zone_ids = isset($request->zone_ids)?(count($request->zone_ids)>0?$request->zone_ids:[]):0;
        $data = Store::withOutGlobalScopes()->join('zones', 'zones.id', '=', 'stores.zone_id')
        ->when($zone_ids, function($query) use($zone_ids){
            $query->whereIn('stores.zone_id', $zone_ids);
        })
        ->when($request->module_id, function($query)use($request){
            $query->where('module_id', $request->module_id);
        })
        ->when($request->module_type, function($query)use($request){
            $query->whereHas('module', function($q)use($request){
                $q->where('module_type', $request->module_type);
            });
        })
        ->where('stores.name', 'like', '%'.$request->q.'%')
        ->limit(8)->get([DB::raw('stores.id as id, CONCAT(stores.name, " (", zones.name,")") as text')]);
        if(isset($request->all))
        {
            $data[]=(object)['id'=>'all', 'text'=>'All'];
        }
        return response()->json($data);
    }

    public function status(Store $store, Request $request)
    {
        $store->status = $request->status;
        $store->save();
        $vendor = $store->vendor;

        try
        {
            if($request->status == 0)
            {   $vendor->auth_token = null;
                if(isset($vendor->fcm_token))
                {
                    $data = [
                        'title' => translate('messages.suspended'),
                        'description' => translate('messages.your_account_has_been_suspended'),
                        'order_id' => '',
                        'image' => '',
                        'type'=> 'block'
                    ];
                    Helpers::send_push_notif_to_device($vendor->fcm_token, $data);
                    DB::table('user_notifications')->insert([
                        'data'=> json_encode($data),
                        'vendor_id'=>$vendor->id,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                }

            }

        }
        catch (\Exception $e) {
            Toastr::warning(translate('messages.push_notification_faild'));
        }

        Toastr::success(translate('messages.store').translate('messages.status_updated'));
        return back();
    }

    public function store_status(Store $store, Request $request)
    {
        if($request->menu == "schedule_order" && !Helpers::schedule_order())
        {
            Toastr::warning(translate('messages.schedule_order_disabled_warning'));
            return back();
        }

        if((($request->menu == "delivery" && $store->take_away==0) || ($request->menu == "take_away" && $store->delivery==0)) &&  $request->status == 0 )
        {
            Toastr::warning(translate('messages.can_not_disable_both_take_away_and_delivery'));
            return back();
        }

        if((($request->menu == "veg" && $store->non_veg==0) || ($request->menu == "non_veg" && $store->veg==0)) &&  $request->status == 0 )
        {
            Toastr::warning(translate('messages.veg_non_veg_disable_warning'));
            return back();
        }
        if($request->menu == "self_delivery_system" && $request->status == '0') {
            $store['free_delivery'] = 0;
        }

        $store[$request->menu] = $request->status;
        $store->save();
        Toastr::success(translate('messages.store').translate('messages.settings_updated'));
        return back();
    }

    public function discountSetup(Store $store, Request $request)
    {
        $message=translate('messages.discount');
        $message .= $store->discount?translate('messages.updated_successfully'):translate('messages.added_successfully');
        $store->discount()->updateOrinsert(
        [
            'store_id' => $store->id
        ],
        [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => 'percent'
        ]
        );
        return response()->json(['message'=>$message], 200);
    }

    public function updateStoreSettings(Store $store, Request $request)
    {
        $request->validate([
            'minimum_order'=>'required',
            'comission'=>'required',
            'tax'=>'required',
            'minimum_delivery_time' => 'required|regex:/^([0-9]{2})$/|min:2|max:2',
            'maximum_delivery_time' => 'required|regex:/^([0-9]{2})$/|min:2|max:2',
        ]);

        if($request->comission_status)
        {
            $store->comission = $request->comission;
        }
        else{
            $store->comission = null;
        }

        $store->minimum_order = $request->minimum_order;
        $store->tax = $request->tax;
        $store->order_place_to_schedule_interval = $request->order_place_to_schedule_interval;
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->veg = (bool)($request->veg_non_veg == 'veg' || $request->veg_non_veg == 'both');
        $store->non_veg = (bool)($request->veg_non_veg == 'non_veg' || $request->veg_non_veg == 'both');

        $store->save();
        Toastr::success(translate('messages.store').translate('messages.settings_updated'));
        return back();
    }

    public function update_application(Request $request)
    {
        $store = Store::findOrFail($request->id);
        $store->vendor->status = $request->status;
        $store->vendor->save();
        if($request->status) $store->status = 1;
        $store->save();
        try{
            if ( config('mail.status') ) {
                Mail::to($request['email'])->send(new \App\Mail\SelfRegistration($request->status==1?'approved':'denied', $store->vendor->f_name.' '.$store->vendor->l_name));
            }
        }catch(\Exception $ex){
            info($ex);
        }
        Toastr::success(translate('messages.application_status_updated_successfully'));
        return back();
    }

    public function cleardiscount(Store $store)
    {
        $store->discount->delete();
        Toastr::success(translate('messages.store').translate('messages.discount_cleared'));
        return back();
    }

    public function withdraw()
    {
        $all = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all' ? 1 : 0;
        $active = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved' ? 1 : 0;
        $denied = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied' ? 1 : 0;
        $pending = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending' ? 1 : 0;

        $withdraw_req =WithdrawRequest::with(['vendor'])
            ->when($all, function ($query) {
                return $query;
            })
            ->when($active, function ($query) {
                return $query->where('approved', 1);
            })
            ->when($denied, function ($query) {
                return $query->where('approved', 2);
            })
            ->when($pending, function ($query) {
                return $query->where('approved', 0);
            })
            ->latest()
            ->paginate(config('default_pagination'));

            if(!Helpers::module_permission_check('withdraw_list')){
                return view('admin-views.wallet.withdraw-dashboard');
            }

        return view('admin-views.wallet.withdraw', compact('withdraw_req'));
    }
    public function withdraw_export(Request $request)
    {
        $all = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all' ? 1 : 0;
        $active = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved' ? 1 : 0;
        $denied = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied' ? 1 : 0;
        $pending = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending' ? 1 : 0;

        $withdraw_req =WithdrawRequest::with(['vendor'])
            ->when($all, function ($query) {
                return $query;
            })
            ->when($active, function ($query) {
                return $query->where('approved', 1);
            })
            ->when($denied, function ($query) {
                return $query->where('approved', 2);
            })
            ->when($pending, function ($query) {
                return $query->where('approved', 0);
            })
            ->latest()->get();
            if($request->type == 'excel'){
                return (new FastExcel(Helpers::export_store_withdraw($withdraw_req)))->download('WithdrawRequests.xlsx');
            }elseif($request->type == 'csv'){
                return (new FastExcel(Helpers::export_store_withdraw($withdraw_req)))->download('WithdrawRequests.csv');
            }
    }

    public function withdraw_search(Request $request){
        $key = explode(' ', $request['search']);
        $withdraw_req = WithdrawRequest::whereHas('vendor', function ($query) use ($key) {
            $query->whereHas('stores', function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            });
        })->get();
        $total=$withdraw_req->count();
        return response()->json([
            'view'=>view('admin-views.wallet.partials._table',compact('withdraw_req'))->render(), 'total'=>$total
        ]);
    }

    public function withdraw_view($withdraw_id, $seller_id)
    {
        $wr = WithdrawRequest::with(['vendor'])->where(['id' => $withdraw_id])->first();
        return view('admin-views.wallet.withdraw-view', compact('wr'));
    }

    public function status_filter(Request $request){
        session()->put('withdraw_status_filter',$request['withdraw_status_filter']);
        return response()->json(session('withdraw_status_filter'));
    }

    public function withdrawStatus(Request $request, $id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        $withdraw->approved = $request->approved;
        $withdraw->transaction_note = $request['note'];
        if ($request->approved == 1) {
            StoreWallet::where('vendor_id', $withdraw->vendor_id)->increment('total_withdrawn', $withdraw->amount);
            StoreWallet::where('vendor_id', $withdraw->vendor_id)->decrement('pending_withdraw', $withdraw->amount);
            $withdraw->save();
            Toastr::success(translate('messages.seller_payment_approved'));
            return redirect()->route('admin.transactions.store.withdraw_list');
        } else if ($request->approved == 2) {
            StoreWallet::where('vendor_id', $withdraw->vendor_id)->decrement('pending_withdraw', $withdraw->amount);
            $withdraw->save();
            Toastr::info(translate('messages.seller_payment_denied'));
            return redirect()->route('admin.transactions.store.withdraw_list');
        } else {
            Toastr::error(translate('messages.not_found'));
            return back();
        }
    }

    public function get_addons(Request $request)
    {
        $cat = AddOn::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->where(['store_id' => $request->store_id])->active()->get();
        $res = '';
        foreach ($cat as $row) {
            $res .= '<option value="' . $row->id.'"';
            if(count($request->data))
            {
                $res .= in_array($row->id, $request->data)?'selected':'';
            }
            $res .=  '>' . $row->name . '</option>';
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    public function get_store_data(Store $store)
    {
        return response()->json($store);
    }

    public function store_filter($id)
    {
        if ($id == 'all') {
            if (session()->has('store_filter')) {
                session()->forget('store_filter');
            }
        } else {
            session()->put('store_filter', Store::where('id', $id)->first(['id', 'name']));
        }
        return back();
    }

    public function get_account_data(Store $store)
    {
        $wallet = $store->vendor->wallet;
        $cash_in_hand = 0;
        $balance = 0;

        if($wallet)
        {
            $cash_in_hand = $wallet->collected_cash;
            $balance = $wallet->total_earning;
        }
        return response()->json(['cash_in_hand'=>$cash_in_hand, 'earning_balance'=>$balance], 200);

    }

    public function bulk_import_index()
    {
        return view('admin-views.vendor.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        $request->validate([
            'module_id'=>'required_if:stackfood,1',
            'products_file'=>'required|file'
        ]);
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }
        $duplicate_phones = $collections->duplicates('phone');
        $duplicate_emails = $collections->duplicates('email');

        // dd(['Phone'=>$duplicate_phones, 'Email'=>$duplicate_emails]);
        if($duplicate_emails->isNotEmpty())
        {
            Toastr::error(translate('messages.duplicate_data_on_column',['field'=>translate('messages.email')]));
            return back();
        }

        if($duplicate_phones->isNotEmpty())
        {
            Toastr::error(translate('messages.duplicate_data_on_column',['field'=>translate('messages.phone')]));
            return back();
        }

        $vendors = [];
        $stores = [];
        $vendor = Vendor::orderBy('id', 'desc')->first('id');
        $vendor_id = $vendor?$vendor->id:0;
        $store = Store::orderBy('id', 'desc')->first('id');
        $store_id = $store?$store->id:0;
        $store_ids = [];
        foreach ($collections as $key=>$collection) {
                if ($collection['ownerFirstName'] === "" || $collection['storeName'] === "" || $collection['phone'] === "" || $collection['email'] === "" || $collection['latitude'] === "" || $collection['longitude'] === "" || $collection['zone_id'] === "" || $collection['module_id'] === "") {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));
                    return back();
                }

                if (!is_numeric($collection['latitude']) || $collection['latitude'] > 90 || $collection['latitude'] < -90 || !is_numeric($collection['longitude']) || $collection['longitude'] > 180 || $collection['longitude'] < -180) {
                    Toastr::error(translate('messages.invalid_latitude_or_longtitude'));
                    return back();
                }


            array_push($vendors, [
                'id'=>$vendor_id+$key+1,
                'f_name' => $collection['ownerFirstName'],
                'l_name' => $collection['ownerLastName'],
                'password' => bcrypt(12345678),
                'phone' => $collection['phone'],
                'email' => $collection['email'],
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
            array_push($stores, [
                'id'=>$store_id+$key+1,
                'name' => $request->stackfood?$collection['storeName']:$collection['storeName'],
                'logo' => $collection['logo'],
                'phone' => $collection['phone'],
                'email' => $collection['email'],
                'latitude' => $collection['latitude'],
                'longitude' => $collection['longitude'],
                'vendor_id' => $vendor_id+$key+1,
                'zone_id' => $collection['zone_id'],
                'delivery_time' => (isset($collection['delivery_time']) && preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $collection['delivery_time'])) ? $collection['delivery_time'] :'30-40 min',
                'module_id' => $request->stackfood?$request->module_id:$collection['module_id'],
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
            if($module = Module::select('module_type')->where('id', $collection['module_id'])->first())
            {
                if(config('module.'.$module->module_type))
                {
                    $store_ids[] = $store_id+$key+1;
                }
            }

        }

        $data = array_map(function($id){
            return array_map(function($item)use($id){
                return     ['store_id'=>$id,'day'=>$item,'opening_time'=>'00:00:00','closing_time'=>'23:59:59'];
            },[0,1,2,3,4,5,6]);
        },$store_ids);

        try{
            DB::beginTransaction();
            DB::table('vendors')->insert($vendors);
            DB::table('stores')->insert($stores);
            DB::table('store_schedule')->insert(array_merge(...$data));
            DB::commit();
        }catch(\Exception $e)
        {
            DB::rollBack();
            info($e);
            Toastr::error(translate('messages.failed_to_import_data'));
            return back();
        }

        Toastr::success(translate('messages.store_imported_successfully',['count'=>count($stores)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.vendor.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        $request->validate([
            'type'=>'required',
            'start_id'=>'required_if:type,id_wise',
            'end_id'=>'required_if:type,id_wise',
            'from_date'=>'required_if:type,date_wise',
            'to_date'=>'required_if:type,date_wise'
        ]);
        $vendors = Vendor::with('stores')
        ->when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })->whereHas('stores', function ($q) use ($request) {
            return $q->where('module_id', Config::get('module.current_module_id'));
        })
        ->get();
        return (new FastExcel(StoreLogic::format_export_stores($vendors)))->download('Stores.xlsx');
    }

    public function add_schedule(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'start_time'=>'required|date_format:H:i',
            'end_time'=>'required|date_format:H:i|after:start_time',
            'store_id'=>'required',
        ],[
            'end_time.after'=>translate('messages.End time must be after the start time')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $temp = StoreSchedule::where('day', $request->day)->where('store_id',$request->store_id)
        ->where(function($q)use($request){
            return $q->where(function($query)use($request){
                return $query->where('opening_time', '<=' , $request->start_time)->where('closing_time', '>=', $request->start_time);
            })->orWhere(function($query)use($request){
                return $query->where('opening_time', '<=' , $request->end_time)->where('closing_time', '>=', $request->end_time);
            });
        })
        ->first();

        if(isset($temp))
        {
            return response()->json(['errors' => [
                ['code'=>'time', 'message'=>translate('messages.schedule_overlapping_warning')]
            ]]);
        }

        $store = Store::find($request->store_id);
        $store_schedule = StoreLogic::insert_schedule($request->store_id, [$request->day], $request->start_time, $request->end_time.':59');

        return response()->json([
            'view' => view('admin-views.vendor.view.partials._schedule', compact('store'))->render(),
        ]);
    }

    public function remove_schedule($store_schedule)
    {
        $schedule = StoreSchedule::find($store_schedule);
        if(!$schedule)
        {
            return response()->json([],404);
        }
        $store = $schedule->store;
        $schedule->delete();
        return response()->json([
            'view' => view('admin-views.vendor.view.partials._schedule', compact('store'))->render(),
        ]);
    }

    public function featured(Request $request)
    {
        $store = Store::findOrFail($request->store);
        $store->featured = $request->status;
        $store->save();
        Toastr::success(translate('messages.store_featured_status_updated'));
        return back();
    }

    public function conversation_list(Request $request)
    {

        $user = UserInfo::where('vendor_id', $request->user_id)->first();

        $conversations = Conversation::WhereUser($user->id);

        if ($request->query('key') != null) {
            $key = explode(' ', $request->get('key'));
            $conversations = $conversations->where(function ($qu) use ($key) {

                $qu->whereHas('sender', function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                    }
                })->orWhereHas('receiver', function ($query1) use ($key) {
                        foreach ($key as $value) {
                            $query1->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                        }
                    });
            });
        }

        $conversations = $conversations->paginate(8);

        $view = view('admin-views.vendor.view.partials._conversation_list', compact('conversations'))->render();
        return response()->json(['html' => $view]);
    }

    public function conversation_view($conversation_id, $user_id)
    {
        $convs = Message::where(['conversation_id' => $conversation_id])->get();
        $conversation = Conversation::find($conversation_id);
        $receiver = UserInfo::find($conversation->receiver_id);
        $sender = UserInfo::find($conversation->sender_id);
        $user = UserInfo::find($user_id);
        return response()->json([
            'view' => view('admin-views.vendor.view.partials._conversations', compact('convs', 'user', 'receiver'))->render()
        ]);
    }


    public function cash_export($type,$store_id)
    {
        $store = Store::find($store_id);
        $account = AccountTransaction::where('from_type', 'store')->where('from_id', $store->id)->get();
            if($type == 'excel'){
                return (new FastExcel($account))->download('CashTransaction.xlsx');
            }elseif($type == 'csv'){
                return (new FastExcel($account))->download('CashTransaction.csv');
            }
    }

    public function order_export($type,$store_id)
    {
        $store = Store::find($store_id);
        $account = OrderTransaction::where('vendor_id', $store->vendor->id)->get();
            if($type == 'excel'){
                return (new FastExcel($account))->download('OrderTransaction.xlsx');
            }elseif($type == 'csv'){
                return (new FastExcel($account))->download('OrderTransaction.csv');
            }
    }

    public function withdraw_trans_export($type,$store_id)
    {
        $store = Store::find($store_id);
        $account = WithdrawRequest::where('vendor_id', $store->vendor->id)->get();
            if($type == 'excel'){
                return (new FastExcel($account))->download('WithdrawTransaction.xlsx');
            }elseif($type == 'csv'){
                return (new FastExcel($account))->download('WithdrawTransaction.csv');
            }
    }
}

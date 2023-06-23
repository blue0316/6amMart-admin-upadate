<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Models\Module;
use App\Models\Admin;
use App\Models\Wishlist;
use App\Models\AdminRole;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller
{
    public function user_dashboard(Request $request)
    {
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => Config::get('module.current_module_id'),
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];

        session()->put('dash_params', $params);
        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $delivery_commission = $data['delivery_commission'];
        $customers = User::zone($params['zone_id'])->take(2)->get();

        $delivery_man = DeliveryMan::with('last_location')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()
        ->limit(2)->get('image');

        $active_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->Active()->count();

        $inactive_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->where('application_status','approved')->where('active',0)->count();
        
        $newly_joined_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->count();

        $reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params){
                return $query->where('zone_id', $params['zone_id']);
            });
        })->count();

        $positive_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params){
                return $query->where('zone_id', $params['zone_id']);
            });
        })->whereIn('rating', [4,5])->get()->count();
        $good_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params){
                return $query->where('zone_id', $params['zone_id']);
            });
        })->where('rating', 3)->count();
        $neutral_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params){
                return $query->where('zone_id', $params['zone_id']);
            });
        })->where('rating', 2)->count();
        $negative_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params){
                return $query->where('zone_id', $params['zone_id']);
            });
        })->where('rating', 1)->count();

        $from = now()->startOfMonth(); // first date of the current month
        $to = now();
        $this_month = User::zone($params['zone_id'])->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'))->count();
        $number = 12;
        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $last_year_users = User::zone($params['zone_id'])
            ->whereMonth('created_at', 12)
            ->whereYear('created_at', now()->format('Y')-1)
            ->count();

        $users = User::zone($params['zone_id'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupBy('year', 'month')->get()->toArray();

        for ($inc = 1; $inc <= $number; $inc++) {
            $user_data[$inc] = 0;
            foreach ($users as $match) {
                if ($match['month'] == $inc) {
                    $user_data[$inc] = $match['total'];
                }
            }
        }

        $active_customers = User::zone($params['zone_id'])->where('status',1)->count();
        $blocked_customers = User::zone($params['zone_id'])->where('status',0)->count();
        $newly_joined = User::zone($params['zone_id'])->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->count();

        $employees = Admin::zone()->with(['role'])->where('role_id', '!=','1')->get();

        $deliveryMen = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })->zonewise()->available()->active()->get();

        $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);

        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}", compact('data','reviews','this_month','user_data','neutral_reviews','good_reviews','negative_reviews','positive_reviews','employees','active_deliveryman','deliveryMen','inactive_deliveryman','newly_joined_deliveryman','delivery_man', 'total_sell', 'commission', 'delivery_commission', 'params','module_type', 'customers','active_customers','blocked_customers', 'newly_joined','last_year_users'));
    }

    public function transaction_dashboard(Request $request)
    {
        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}");
    }

    public function dispatch_dashboard(Request $request)
    {
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => Config::get('module.current_module_id'),
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];

        session()->put('dash_params', $params);
        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $delivery_commission = $data['delivery_commission'];
        $label = $data['label'];
        $customers = User::zone($params['zone_id'])->take(2)->get();

        $delivery_man = DeliveryMan::with('last_location')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()
        ->limit(2)->get('image');

        $active_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->where('active',1)->count();

        $inactive_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->where('application_status','approved')->where('active',0)->count();

        $unavailable_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->where('active',1)->Unavailable()->count();

        $available_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->where('active',1)->Available()->count();
        
        $newly_joined_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
        ->Zonewise()->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->count();

        $deliveryMen = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })->zonewise()->available()->active()->get();

        $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);

        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}", compact('data','active_deliveryman','deliveryMen','unavailable_deliveryman','available_deliveryman','inactive_deliveryman','newly_joined_deliveryman','delivery_man', 'total_sell', 'commission', 'delivery_commission','label', 'params','module_type'));
    }

    public function dashboard(Request $request)
    {
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => Config::get('module.current_module_id'),
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];
        session()->put('dash_params', $params);
        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $delivery_commission = $data['delivery_commission'];
        $label = $data['label'];
        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}", compact('data', 'total_sell', 'commission', 'delivery_commission', 'label','params','module_type'));

    }

    public function order(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'statistics_type') {
                $params['statistics_type'] = $request['statistics_type'];
            }
        }
        session()->put('dash_params', $params);

        if ($params['zone_id'] != 'all') {
            $store_ids = Store::where(['module_id' => $params['module_id']])->where(['zone_id' => $params['zone_id']])->pluck('id')->toArray();
        } else {
            $store_ids = Store::where(['module_id' => $params['module_id']])->pluck('id')->toArray();
        }
        $data = self::order_stats_calc($params['zone_id'], $params['module_id']);
        $module_type = Config::get('module.current_module_type');
        if ($module_type == 'parcel') {
            return response()->json([
                'view' => view('admin-views.partials._dashboard-order-stats-parcel', compact('data'))->render()
            ], 200);
        }
        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function zone(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'zone_id') {
                $params['zone_id'] = $request['zone_id'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $popular = $data['popular'];
        $top_deliveryman = $data['top_deliveryman'];
        $top_rated_foods = $data['top_rated_foods'];
        $top_restaurants = $data['top_restaurants'];
        $top_customers = $data['top_customers'];
        $top_sell = $data['top_sell'];
        $delivery_commission = $data['delivery_commission'];
        $module_type = Config::get('module.current_module_type');
        // if ($module_type == 'parcel') {
        //     return response()->json([
        //         'view' => view('admin-views.partials._user-overview-chart-parcel', compact('data'))->render()
        //     ], 200);
        // }

        return response()->json([
            'popular_restaurants' => view('admin-views.partials._popular-restaurants', compact('popular'))->render(),
            'top_deliveryman' => view('admin-views.partials._top-deliveryman', compact('top_deliveryman'))->render(),
            'top_rated_foods' => view('admin-views.partials._top-rated-foods', compact('top_rated_foods'))->render(),
            'top_restaurants' => view('admin-views.partials._top-restaurants', compact('top_restaurants'))->render(),
            'top_customers' => view('admin-views.partials._top-customer', compact('top_customers'))->render(),
            'top_selling_foods' => view('admin-views.partials._top-selling-foods', compact('top_sell'))->render(),

            'order_stats' =>$module_type == 'parcel'? view('admin-views.partials._dashboard-order-stats-parcel', compact('data'))->render():view('admin-views.partials._dashboard-order-stats', compact('data'))->render(),
            'user_overview' => view('admin-views.partials._user-overview-chart', compact('data'))->render(),
            'monthly_graph' => view('admin-views.partials._monthly-earning-graph', compact('total_sell', 'commission', 'delivery_commission'))->render(),
            'stat_zone' => view('admin-views.partials._zone-change', compact('data'))->render(),
        ], 200);
    }

    public function user_overview(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'user_overview') {
                $params['user_overview'] = $request['user_overview'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::user_overview_calc($params['zone_id'], $params['module_id']);
        $module_type = Config::get('module.current_module_type');
        if ($module_type == 'parcel') {
            return response()->json([
                'view' => view('admin-views.partials._user-overview-chart-parcel', compact('data'))->render()
            ], 200);
        }

        return response()->json([
            'view' => view('admin-views.partials._user-overview-chart', compact('data'))->render()
        ], 200);
    }
    public function commission_overview(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'commission_overview') {
                $params['commission_overview'] = $request['commission_overview'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::dashboard_data($request);

        return response()->json([
            'view' => view('admin-views.partials._commission-overview-chart', compact('data'))->render(),
            'gross_sale' => view('admin-views.partials._gross_sale', compact('data'))->render()
        ], 200);
    }

    public function order_stats_calc($zone_id, $module_id)
    {
        $params = session('dash_params');
        $module_type = Config::get('module.current_module_type');

        if ($module_id && $params['statistics_type'] == 'today') {
            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereDate('created_at', Carbon::now());
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereDate('accepted', Carbon::now());
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereDate('processing', Carbon::now());
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereDate('picked_up', Carbon::now());
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereDate('delivered', Carbon::now());
            $canceled = Order::where('module_id', $module_id)->where(['order_status' => 'canceled'])->whereDate('canceled', Carbon::now());
            $refund_requested = Order::where('module_id', $module_id)->where(['order_status' => 'refund_requested'])->whereDate('refund_requested', Carbon::now());
            $refunded = Order::where('module_id', $module_id)->where(['order_status' => 'refunded'])->whereDate('refunded', Carbon::now());
            $new_orders = Order::where('module_id', $module_id)->whereDate('schedule_at', Carbon::now());
            $new_items = Item::where('module_id', $module_id)->whereDate('created_at', Carbon::now());
            $new_stores = Store::where('module_id', $module_id)->whereDate('created_at', Carbon::now());
            $new_customers = User::whereDate('created_at', Carbon::now());
            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('module_id', $module_id);
            $total_stores = Store::where('module_id', $module_id);
            $total_customers = User::all();
        } elseif($module_id && $params['statistics_type'] == 'this_year'){
            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereDate('created_at', now()->format('Y'));
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereDate('accepted', now()->format('Y'));
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereDate('processing', now()->format('Y'));
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereDate('picked_up', now()->format('Y'));
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereDate('delivered', now()->format('Y'));
            $canceled = Order::where('module_id', $module_id)->where(['order_status' => 'canceled'])->whereDate('canceled', now()->format('Y'));
            $refund_requested = Order::where('module_id', $module_id)->where(['order_status' => 'refund_requested'])->whereDate('refund_requested', now()->format('Y'));
            $refunded = Order::where('module_id', $module_id)->where(['order_status' => 'refunded'])->whereDate('refunded', now()->format('Y'));
            $new_orders = Order::where('module_id', $module_id)->whereDate('schedule_at', now()->format('Y'));
            $new_items = Item::where('module_id', $module_id)->whereDate('created_at', now()->format('Y'));
            $new_stores = Store::where('module_id', $module_id)->whereDate('created_at', now()->format('Y'));
            $new_customers = User::whereDate('created_at', now()->format('Y'));
            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('module_id', $module_id);
            $total_stores = Store::where('module_id', $module_id);
            $total_customers = User::all();
        } elseif($module_id && $params['statistics_type'] == 'this_month'){
            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereMonth('accepted', now()->format('m'))->whereYear('accepted', now()->format('Y'));
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereMonth('processing', now()->format('m'))->whereYear('processing', now()->format('Y'));
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereMonth('picked_up', now()->format('m'))->whereYear('picked_up', now()->format('Y'));
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereMonth('delivered', now()->format('m'))->whereYear('delivered', now()->format('Y'));
            $canceled = Order::where('module_id', $module_id)->where(['order_status' => 'canceled'])->whereMonth('canceled', now()->format('m'))->whereYear('canceled', now()->format('Y'));
            $refund_requested = Order::where('module_id', $module_id)->where(['order_status' => 'refund_requested'])->whereMonth('refund_requested', now()->format('m'))->whereYear('refund_requested', now()->format('Y'));
            $refunded = Order::where('module_id', $module_id)->where(['order_status' => 'refunded'])->whereMonth('refunded', now()->format('m'))->whereYear('refunded', now()->format('Y'));
            $new_orders = Order::where('module_id', $module_id)->whereMonth('schedule_at', now()->format('m'))->whereYear('schedule_at', now()->format('Y'));
            $new_items = Item::where('module_id', $module_id)->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            $new_stores = Store::where('module_id', $module_id)->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            $new_customers = User::whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('module_id', $module_id);
            $total_stores = Store::where('module_id', $module_id);
            $total_customers = User::all();
        } elseif($module_id && $params['statistics_type'] == 'this_week'){
            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereDate('accepted', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereDate('processing', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereDate('picked_up', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereDate('delivered', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $canceled = Order::where('module_id', $module_id)->where(['order_status' => 'canceled'])->whereDate('canceled', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $refund_requested = Order::where('module_id', $module_id)->where(['order_status' => 'refund_requested'])->whereDate('refund_requested', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $refunded = Order::where('module_id', $module_id)->where(['order_status' => 'refunded'])->whereDate('refunded', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $new_orders = Order::where('module_id', $module_id)->whereDate('schedule_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $new_items = Item::where('module_id', $module_id)->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $new_stores = Store::where('module_id', $module_id)->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $new_customers = User::whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('module_id', $module_id);
            $total_stores = Store::where('module_id', $module_id);
            $total_customers = User::all();
        } elseif($module_id) {
            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id);
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id);
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id);
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id);
            $delivered = Order::Delivered()->where('module_id', $module_id);
            $canceled = Order::Canceled()->where('module_id', $module_id);
            $refund_requested = Order::failed()->where('module_id', $module_id);
            $refunded = Order::Refunded()->where('module_id', $module_id);
            $new_orders = Order::where('module_id', $module_id)->whereDate('schedule_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $new_items = Item::where('module_id', $module_id)->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $new_stores = Store::where('module_id', $module_id)->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $new_customers = User::whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('module_id', $module_id);
            $total_stores = Store::where('module_id', $module_id);
            $total_customers = User::all();
        } else {
            $searching_for_dm = Order::SearchingForDeliveryman();
            $accepted_by_dm = Order::AccepteByDeliveryman();
            $preparing_in_rs = Order::Preparing();
            $picked_up = Order::ItemOnTheWay();
            $delivered = Order::Delivered();
            $canceled = Order::Canceled();
            $refund_requested = Order::failed();
            $refunded = Order::Refunded();
            $new_orders = Order::whereDate('schedule_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $new_items = Item::whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $new_stores = Store::whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $new_customers = User::whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'));
            $total_orders = Order::all();
            $total_items = Item::all();
            $total_stores = Store::all();
            $total_customers = User::all();
        }

        if (is_numeric($zone_id) && $module_id && $module_type!='food') {
            $searching_for_dm = $searching_for_dm->StoreOrder()->OrderScheduledIn(30)->where('zone_id', $zone_id)->count();
            $accepted_by_dm = $accepted_by_dm->StoreOrder()->where('zone_id', $zone_id)->count();
            $preparing_in_rs = $preparing_in_rs->StoreOrder()->where('zone_id', $zone_id)->count();
            $picked_up = $picked_up->StoreOrder()->where('zone_id', $zone_id)->count();
            $delivered = $delivered->StoreOrder()->where('zone_id', $zone_id)->count();
            $canceled = $canceled->StoreOrder()->where('zone_id', $zone_id)->count();
            $refund_requested = $refund_requested->StoreOrder()->where('zone_id', $zone_id)->count();
            $refunded = $refunded->StoreOrder()->where('zone_id', $zone_id)->count();
            $total_orders = $total_orders->StoreOrder()->where('zone_id', $zone_id)->count();
            $total_items = $total_items->count();
            $total_stores = $total_stores->where('zone_id', $zone_id)->count();
            $total_customers = $total_customers->where('zone_id', $zone_id)->count();
            $new_orders = $new_orders->StoreOrder()->where('zone_id', $zone_id)->count();
            $new_items = $new_items->count();
            $new_stores = $new_stores->where('zone_id', $zone_id)->count();
            $new_customers = $new_customers->where('zone_id', $zone_id)->count();
        } elseif($module_id && $module_type!='parcel') {
            $searching_for_dm = $searching_for_dm->StoreOrder()->OrderScheduledIn(30)->count();
            $accepted_by_dm = $accepted_by_dm->StoreOrder()->count();
            $preparing_in_rs = $preparing_in_rs->StoreOrder()->count();
            $picked_up = $picked_up->StoreOrder()->count();
            $delivered = $delivered->StoreOrder()->count();
            $canceled = $canceled->StoreOrder()->count();
            $refund_requested = $refund_requested->StoreOrder()->count();
            $refunded = $refunded->StoreOrder()->count();
            $total_orders = $total_orders->StoreOrder()->count();
            $total_items = $total_items->count();
            $total_stores = $total_stores->count();
            $total_customers = $total_customers->count();
            $new_orders = $new_orders->StoreOrder()->count();
            $new_items = $new_items->count();
            $new_stores = $new_stores->count();
            $new_customers = $new_customers->count();
        } elseif($module_id && $module_type =='parcel') {
            $searching_for_dm = $searching_for_dm->ParcelOrder()->OrderScheduledIn(30)->count();
            $accepted_by_dm = $accepted_by_dm->ParcelOrder()->count();
            $preparing_in_rs = $preparing_in_rs->ParcelOrder()->count();
            $picked_up = $picked_up->ParcelOrder()->count();
            $delivered = $delivered->ParcelOrder()->count();
            $canceled = $canceled->ParcelOrder()->count();
            $refund_requested = $refund_requested->ParcelOrder()->count();
            $refunded = $refunded->ParcelOrder()->count();
            $total_orders = $total_orders->ParcelOrder()->count();
            $total_items = $total_items->count();
            $total_stores = $total_stores->count();
            $total_customers = $total_customers->count();
            $new_orders = $new_orders->ParcelOrder()->count();
            $new_items = $new_items->count();
            $new_stores = $new_stores->count();
            $new_customers = $new_customers->count();
        }else{
            $searching_for_dm = $searching_for_dm->StoreOrder()->OrderScheduledIn(30)->count();
            $accepted_by_dm = $accepted_by_dm->StoreOrder()->count();
            $preparing_in_rs = $preparing_in_rs->StoreOrder()->count();
            $picked_up = $picked_up->StoreOrder()->count();
            $delivered = $delivered->StoreOrder()->count();
            $canceled = $canceled->StoreOrder()->count();
            $refund_requested = $refund_requested->StoreOrder()->count();
            $refunded = $refunded->StoreOrder()->count();
            $total_orders = $total_orders->count();
            $total_items = $total_items->count();
            $total_stores = $total_stores->count();
            $total_customers = $total_customers->count();
            $new_orders = $new_orders->count();
            $new_items = $new_items->count();
            $new_stores = $new_stores->count();
            $new_customers = $new_customers->count();
        }
        $data = [
            'searching_for_dm' => $searching_for_dm,
            'accepted_by_dm' => $accepted_by_dm,
            'preparing_in_rs' => $preparing_in_rs,
            'picked_up' => $picked_up,
            'delivered' => $delivered,
            'canceled' => $canceled,
            'refund_requested' => $refund_requested,
            'refunded' => $refunded,
            'total_orders' => $total_orders,
            'total_items' => $total_items,
            'total_stores' => $total_stores,
            'total_customers' => $total_customers,
            'new_orders' => $new_orders,
            'new_items' => $new_items,
            'new_stores' => $new_stores,
            'new_customers' => $new_customers,
        ];
        return $data;
    }

    public function user_overview_calc($zone_id, $module_id)
    {
        $params = session('dash_params');
        //zone
        if (is_numeric($zone_id)) {
            $customer = User::where('zone_id', $zone_id);
            $stores = Store::where('module_id', $module_id)->where(['zone_id' => $zone_id]);
            $delivery_man = DeliveryMan::where('application_status', 'approved')->where('zone_id', $zone_id)->Zonewise();
        } else {
            $customer = User::whereNotNull('id');
            $stores = Store::where('module_id', $module_id)->whereNotNull('id');
            $delivery_man = DeliveryMan::where('application_status', 'approved')->Zonewise();
        }
        //user overview
        if ($params['user_overview'] == 'overall') {
            $customer = $customer->count();
            $stores = $stores->count();
            $delivery_man = $delivery_man->count();
        } elseif($params['user_overview'] == 'this_month') {
            $customer = $customer->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $stores = $stores->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $delivery_man = $delivery_man->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
        } elseif($params['user_overview'] == 'this_year') {
            $customer = $customer
                ->whereYear('created_at', date('Y'))->count();
            $stores = $stores
                ->whereYear('created_at', date('Y'))->count();
            $delivery_man = $delivery_man
                ->whereYear('created_at', date('Y'))->count();
        } else {
            $customer = $customer->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')])->count();
            $stores = $stores->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')])->count();
            $delivery_man = $delivery_man->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')])->count();
        }
        $data = [
            'customer' => $customer,
            'stores' => $stores,
            'delivery_man' => $delivery_man
        ];
        return $data;
    }


    public function dashboard_data($request)
    {
        $params = session('dash_params');
        if (!url()->current() == $request->is('admin/users')) {
        $data_os = self::order_stats_calc($params['zone_id'], $params['module_id']);
        $data_uo = self::user_overview_calc($params['zone_id'], $params['module_id']);
        }
        $popular = Wishlist::with(['store'])
            ->whereHas('store')
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id']);
                });
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('zone_id', $params['zone_id']);
                });
            })
            ->select('store_id', DB::raw('COUNT(store_id) as count'))->groupBy('store_id')->orderBy('count', 'DESC')->limit(6)->get();
        $top_sell = Item::withoutGlobalScopes()
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id']);
                });
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id'])->where('zone_id', $params['zone_id']);
                });
            })
            ->orderBy("order_count", 'desc')
            ->take(6)
            ->get();
        $top_rated_foods = Item::withoutGlobalScopes()
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id']);
                });
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('zone_id', $params['zone_id']);
                });
            })
            ->orderBy('rating_count', 'desc')
            ->take(6)
            ->get();

        $top_deliveryman = DeliveryMan::withCount('orders')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->where('zone_id', $params['zone_id']);
            })
            ->Zonewise()    
            ->orderBy("orders_count", 'desc')
            ->take(6)
            ->get();

        $top_customers = User::when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->where('zone_id', $params['zone_id']);
            })
            ->orderBy("order_count", 'desc')
            ->take(6)
            ->get();

        $top_restaurants = Store::when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->where('module_id', $params['module_id']);
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->where('zone_id', $params['zone_id']);
            })
            ->orderBy("order_count", 'desc')
            ->take(6)
            ->get();


        // custom filtering for bar chart
        $months = array(
            '"Jan"',
            '"Feb"',
            '"Mar"',
            '"Apr"',
            '"May"',
            '"Jun"',
            '"Jul"',
            '"Aug"',
            '"Sep"',
            '"Oct"',
            '"Nov"',
            '"Dec"'
        );
        $days = array(
            '"Sun"',
            '"Mon"',
            '"Tue"',
            '"Wed"',
            '"Thu"',
            '"Fri"',
            '"Sat"'
        );
        $total_sell = [];
        $commission = [];
        $label = [];
            switch ($params['commission_overview']) {
                case "this_year":
                    for ($i = 1; $i <= 12; $i++) {
                        $total_sell[$i] = OrderTransaction::NotRefunded()
                            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                            ->sum('order_amount');
                        $commission[$i] = OrderTransaction::NotRefunded()
                            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                            ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));
                        $delivery_commission[$i] = OrderTransaction::when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                            ->sum('delivery_fee_comission');
                    }
                        $label = $months;
                    break;
                case "this_week":
                    $weekStartDate = now()->startOfWeek();
                    for ($i = 1; $i <= 7; $i++) {
                        $total_sell[$i] = OrderTransaction::NotRefunded()
                            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereDay('created_at', $weekStartDate->format('d'))->whereMonth('created_at', now()->format('m'))
                            ->sum('order_amount');
                        $commission[$i] = OrderTransaction::NotRefunded()
                            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereDay('created_at', $weekStartDate->format('d'))->whereMonth('created_at', now()->format('m'))
                            ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));
                        $delivery_commission[$i] = OrderTransaction::when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereDay('created_at', $weekStartDate->format('d'))->whereMonth('created_at', now()->format('m'))
                            ->sum('delivery_fee_comission');
                    }
                    $label = $days;
                    break;
                case "this_month":
                    $start = now()->startOfMonth();
                    $end = now()->startOfMonth()->addDays(7);
                    $total_day = now()->daysInMonth;
                    $remaining_days = now()->daysInMonth - 28;
                    $weeks = array(
                        '"Day 1-7"',
                        '"Day 8-14"',
                        '"Day 15-21"',
                        '"Day 22-' . $total_day . '"',
                    );
                    for ($i = 1; $i <= 4; $i++) {
                        $total_sell[$i] = OrderTransaction::NotRefunded()
                            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                            ->sum('order_amount');
                        $commission[$i] = OrderTransaction::NotRefunded()
                            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                            ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));
                        $delivery_commission[$i] = OrderTransaction::when(is_numeric($params['module_id']), function ($q) use ($params) {
                                return $q->where('module_id', $params['module_id']);
                            })
                            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                                return $q->where('zone_id', $params['zone_id']);
                            })
                            ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                            ->sum('delivery_fee_comission');

                            $start = $start->addDays(7);
                            $end = $i == 3 ? $end->addDays(7 + $remaining_days) : $end->addDays(7);
                    }
                    $label = $weeks;
                    break;
                default:
                for ($i = 1; $i <= 12; $i++) {
                    $total_sell[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('order_amount');
                    $commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));
                    $delivery_commission[$i] = OrderTransaction::when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('delivery_fee_comission');
                }
                    $label = $months;
            }

        // $total_sell = [];
        // $commission = [];
        // for ($i = 1; $i <= 12; $i++) {
        //     $total_sell[$i] = OrderTransaction::NotRefunded()
        //         ->when(is_numeric($params['module_id']), function ($q) use ($params) {
        //             return $q->where('module_id', $params['module_id']);
        //         })
        //         ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
        //             return $q->where('zone_id', $params['zone_id']);
        //         })
        //         ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
        //         ->sum('order_amount');
        //     $commission[$i] = OrderTransaction::NotRefunded()
        //         ->when(is_numeric($params['module_id']), function ($q) use ($params) {
        //             return $q->where('module_id', $params['module_id']);
        //         })
        //         ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
        //             return $q->where('zone_id', $params['zone_id']);
        //         })
        //         ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
        //         ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));
        //     $delivery_commission[$i] = OrderTransaction::when(is_numeric($params['module_id']), function ($q) use ($params) {
        //             return $q->where('module_id', $params['module_id']);
        //         })
        //         ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
        //             return $q->where('zone_id', $params['zone_id']);
        //         })
        //         ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
        //         ->sum('delivery_fee_comission');
        // }
        if (!url()->current() == $request->is('admin/users')) {
            $dash_data = array_merge($data_os, $data_uo);
        }

        $dash_data['popular'] = $popular;
        $dash_data['top_sell'] = $top_sell;
        $dash_data['top_rated_foods'] = $top_rated_foods;
        $dash_data['top_deliveryman'] = $top_deliveryman;
        $dash_data['top_restaurants'] = $top_restaurants;
        $dash_data['top_customers'] = $top_customers;
        $dash_data['total_sell'] = $total_sell;
        $dash_data['commission'] = $commission;
        $dash_data['delivery_commission'] = $delivery_commission;
        $dash_data['label'] = $label;
        return $dash_data;
    }
}

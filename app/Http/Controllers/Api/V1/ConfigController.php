<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\Module;
use App\Models\SocialMedia;
use App\Models\Zone;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConfigController extends Controller
{
    private $map_api_key;

    function __construct()
    {
        $map_api_key_server=BusinessSetting::where(['key'=>'map_api_key_server'])->first();
        $map_api_key_server=$map_api_key_server?$map_api_key_server->value:null;
        $this->map_api_key = $map_api_key_server;
    }

    public function configuration()
    {
        $key = ['currency_code','cash_on_delivery','digital_payment','default_location','free_delivery_over','business_name','logo','address','phone','email_address','country','currency_symbol_position','app_minimum_version_android','app_url_android','app_minimum_version_ios','app_url_ios','customer_verification','schedule_order','order_delivery_verification','per_km_shipping_charge','minimum_shipping_charge','show_dm_earning','canceled_by_deliveryman','canceled_by_store','timeformat','toggle_veg_non_veg','toggle_dm_registration','toggle_store_registration','schedule_order_slot_duration','parcel_per_km_shipping_charge','parcel_minimum_shipping_charge','web_app_landing_page_settings','footer_text','landing_page_links','loyalty_point_exchange_rate', 'loyalty_point_item_purchase_point', 'loyalty_point_status', 'loyalty_point_minimum_point', 'wallet_status', 'dm_tips_status', 'ref_earning_status','ref_earning_exchange_rate','refund_active_status','refund','cancelation','shipping_policy','prescription_order_status','tax_included','icon'];

        $settings =  array_column(BusinessSetting::whereIn('key',$key)->get()->toArray(), 'value', 'key');

        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode($settings['cash_on_delivery'], true);
        $digital_payment = json_decode($settings['digital_payment'], true);
        $default_location=isset($settings['default_location'])?json_decode($settings['default_location'], true):0;
        $free_delivery_over = $settings['free_delivery_over'];
        $free_delivery_over = isset($free_delivery_over)?(float)$free_delivery_over:$free_delivery_over;
        $module = null;
        if(Module::active()->count()==1)
        {
            $module = Module::active()->first();
        }
        $languages = Helpers::get_business_settings('language');
        $lang_array = [];
        foreach ($languages as $language) {
            array_push($lang_array, [
                'key' => $language,
                'value' => Helpers::get_language_name($language)
            ]);
        }
        $social_login = [];
        foreach (Helpers::get_business_settings('social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (boolean)$social['status']
            ];
            array_push($social_login, $config);
        }
        return response()->json([
            'business_name' => $settings['business_name'],
            // 'business_open_time' => $settings['business_open_time'],
            // 'business_close_time' => $settings['business_close_time'],
            'logo' => $settings['logo'],
            'address' => $settings['address'],
            'phone' => $settings['phone'],
            'email' => $settings['email_address'],
            // 'store_location_coverage' => Branch::where(['id'=>1])->first(['longitude','latitude','coverage']),
            // 'minimum_order_value' => (float)$settings['minimum_order_value'],
            'base_urls' => [
                'item_image_url' => asset('storage/app/public/product'),
                'refund_image_url' => asset('storage/app/public/refund'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'store_image_url' => asset('storage/app/public/store'),
                'vendor_image_url' => asset('storage/app/public/vendor'),
                'store_cover_photo_url' => asset('storage/app/public/store/cover'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url' => asset('storage/app/public/conversation'),
                'campaign_image_url' => asset('storage/app/public/campaign'),
                'business_logo_url' => asset('storage/app/public/business'),
                'order_attachment_url' => asset('storage/app/public/order'),
                'module_image_url' => asset('storage/app/public/module'),
                'parcel_category_image_url' => asset('storage/app/public/parcel_category'),
                'landing_page_image_url' => asset('public/assets/landing/image'),
            ],
            'country' => $settings['country'],
            'default_location'=> [ 'lat'=> $default_location?$default_location['lat']:'23.757989', 'lng'=> $default_location?$default_location['lng']:'90.360587' ],
            'currency_symbol' => $currency_symbol,
            'currency_symbol_direction' => $settings['currency_symbol_position'],
            'app_minimum_version_android' => (integer)$settings['app_minimum_version_android'],
            'app_url_android' => $settings['app_url_android'],
            'app_minimum_version_ios' => (integer)$settings['app_minimum_version_ios'],
            'app_url_ios' => $settings['app_url_ios'],
            'customer_verification' => (boolean)$settings['customer_verification'],
            'prescription_order_status' => isset($settings['prescription_order_status'])?(boolean)$settings['prescription_order_status']:false,
            'schedule_order' => (boolean)$settings['schedule_order'],
            'order_delivery_verification' => (boolean)$settings['order_delivery_verification'],
            'cash_on_delivery' => (boolean)($cod['status'] == 1 ? true : false),
            'digital_payment' => (boolean)($digital_payment['status'] == 1 ? true : false),
            'per_km_shipping_charge' => (double)$settings['per_km_shipping_charge'],
            'minimum_shipping_charge' => (double)$settings['minimum_shipping_charge'],
            'free_delivery_over'=>$free_delivery_over,
            'demo'=>(boolean)(env('APP_MODE')=='demo'?true:false),
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'order_confirmation_model'=>config('order_confirmation_model'),
            'show_dm_earning' => (boolean)$settings['show_dm_earning'],
            'canceled_by_deliveryman' => (boolean)$settings['canceled_by_deliveryman'],
            'canceled_by_store' => (boolean)$settings['canceled_by_store'],
            'timeformat' => (string)$settings['timeformat'],
            'language' => $lang_array,
            'social_login' => $social_login,
            'toggle_veg_non_veg' => (boolean)$settings['toggle_veg_non_veg'],
            'toggle_dm_registration' => (boolean)$settings['toggle_dm_registration'],
            'toggle_store_registration' => (boolean)$settings['toggle_store_registration'],
            'refund_active_status' => (boolean)$settings['refund_active_status'],
            'schedule_order_slot_duration' => (int)$settings['schedule_order_slot_duration'],
            'digit_after_decimal_point' => (int)config('round_up_to_digit'),
            'module_config'=>config('module'),
            'module'=>$module,
            'parcel_per_km_shipping_charge' => (float)$settings['parcel_per_km_shipping_charge'],
            'parcel_minimum_shipping_charge' => (float)$settings['parcel_minimum_shipping_charge'],
            'landing_page_settings'=> isset($settings['web_app_landing_page_settings'])?json_decode($settings['web_app_landing_page_settings'], true):null,
            'social_media'=>SocialMedia::active()->get()->toArray(),
            'footer_text'=>isset($settings['footer_text'])?$settings['footer_text']:'',
            'fav_icon' => $settings['icon'],
            'landing_page_links'=>isset($settings['landing_page_links'])?json_decode($settings['landing_page_links']):[],
            //Added Business Setting
            'dm_tips_status' => (int)(isset($settings['dm_tips_status']) ? $settings['dm_tips_status'] : 0),
            'loyalty_point_exchange_rate' => (int)(isset($settings['loyalty_point_item_purchase_point']) ? $settings['loyalty_point_exchange_rate'] : 0),
            'loyalty_point_item_purchase_point' => (float)(isset($settings['loyalty_point_item_purchase_point']) ? $settings['loyalty_point_item_purchase_point'] : 0.0),
            'loyalty_point_status' => (int)(isset($settings['loyalty_point_status']) ? $settings['loyalty_point_status'] : 0),
            'customer_wallet_status' => (int)(isset($settings['wallet_status']) ? $settings['wallet_status'] : 0),
            'ref_earning_status' => (int)(isset($settings['ref_earning_status']) ? $settings['ref_earning_status'] : 0),
            'ref_earning_exchange_rate' => (double)(isset($settings['ref_earning_exchange_rate']) ? $settings['ref_earning_exchange_rate'] : 0),
            'refund_policy' => (int)(isset($settings['refund']) ? json_decode($settings['refund'], true)['status'] : 0),
            'cancelation_policy' => (int)(isset($settings['cancelation']) ? json_decode($settings['cancelation'], true)['status'] : 0),
            'shipping_policy' => (int)(isset($settings['shipping_policy']) ? json_decode($settings['shipping_policy'], true)['status'] : 0),
            'loyalty_point_minimum_point' => (int)(isset($settings['loyalty_point_minimum_point']) ? $settings['loyalty_point_minimum_point'] : 0),
            'tax_included' => (int)(isset($settings['tax_included']) ? $settings['tax_included'] : 0),
        ]);
    }

    public function get_zone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $point = new Point($request->lat,$request->lng);
        $zones = Zone::with('modules')->contains('coordinates', $point)->latest()->get(['id', 'status', 'cash_on_delivery', 'digital_payment']);
        if(count($zones)<1)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'coordinates','message'=>translate('messages.service_not_available_in_this_area')]
                ]
            ], 404);
        }
        $data = array_filter($zones->toArray(), function($zone){
            if($zone['status'] == 1) {
                return $zone;
            }
        });

        if (count($data) > 0) {
            return response()->json(['zone_id' => json_encode(array_column($data, 'id')), 'zone_data'=>array_values($data)], 200);
        }

        return response()->json([
            'errors'=>[
                ['code'=>'coordinates','message'=>translate('messages.we_are_temporarily_unavailable_in_this_area')]
            ]
        ], 403);
    }

    public function place_api_autocomplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.$request['search_text'].'&key='.$this->map_api_key);
        return $response->json();
    }


    public function distance_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$request['origin_lat'].','.$request['origin_lng'].'&destinations='.$request['destination_lat'].','.$request['destination_lng'].'&key='.$this->map_api_key.'&mode=walking');
        return $response->json();
    }


    public function place_api_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'placeid' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$request['placeid'].'&key='.$this->map_api_key);
        return $response->json();
    }

    public function geocode_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$request->lat.','.$request->lng.'&key='.$this->map_api_key);
        return $response->json();
    }
}

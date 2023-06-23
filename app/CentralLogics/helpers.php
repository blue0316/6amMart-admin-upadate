<?php

namespace App\CentralLogics;

use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\Review;
use App\Mail\PlaceOrder;
use App\Models\Currency;
use App\Models\DMReview;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\CentralLogics\StoreLogic;
use App\Models\Expense;
use App\Models\NotificationMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravelpkg\Laravelchk\Http\Controllers\LaravelchkController;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Str;
use PayPal\Api\Transaction;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function schedule_order()
    {
        return (bool)BusinessSetting::where(['key' => 'schedule_order'])->first()->value;
    }


    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        $match = json_decode($variation, true)[0];
        $result = ['price' => 0, 'stock' => 0];
        foreach (json_decode($product['variations'], true) as $property => $value) {
            if ($value['type'] == $match['type']) {
                $result = ['price' => $value['price'], 'stock' => $value['stock'] ?? 0];
            }
        }
        return $result;
    }

    public static function address_data_formatting($data)
    {
        foreach ($data as $key=>$item) {
            $point = new Point($item->latitude, $item->longitude);
            $data[$key]['zone_ids'] = array_column(Zone::contains('coordinates', $point)->latest()->get(['id'])->toArray(), 'id');;
        }
        return $data;
    }

    public static function product_data_formatting($data, $multi_data = false, $trans = false, $local = 'en')
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                if ($item->title) {
                    $item['name'] = $item->title;
                    unset($item['title']);
                }
                if ($item->start_time) {
                    $item['available_time_starts'] = $item->start_time->format('H:i');
                    unset($item['start_time']);
                }
                if ($item->end_time) {
                    $item['available_time_ends'] = $item->end_time->format('H:i');
                    unset($item['end_time']);
                }

                if ($item->start_date) {
                    $item['available_date_starts'] = $item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if ($item->end_date) {
                    $item['available_date_ends'] = $item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }
                $categories = [];
                foreach (json_decode($item['category_ids']) as $value) {
                    $categories[] = ['id' => (string)$value->id, 'position' => $value->position];
                }
                $item['category_ids'] = $categories;
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);
                $item['add_ons'] = self::addon_data_formatting(AddOn::withoutGlobalScope('translate')->whereIn('id', json_decode($item['add_ons'], true))->active()->get(), true, $trans, $local);
                foreach (json_decode($item['variations'], true) as $var) {
                    array_push($variations, [
                        'type' => $var['type'],
                        'price' => (float)$var['price'],
                        'stock' => (int)($var['stock'] ?? 0)
                    ]);
                }
                $item['variations'] = $variations;
                $item['food_variations'] = $item['food_variations']?json_decode($item['food_variations'], true):'';
                $item['module_type'] = $item->module->module_type;
                $item['store_name'] = $item->store->name;
                $item['zone_id'] = $item->store->zone_id;
                $item['store_discount'] = self::get_store_discount($item->store) ? $item->store->discount->discount : 0;
                $item['schedule_order'] = $item->store->schedule_order;
                $item['tax'] = $item->store->tax;
                $item['rating_count'] = (int)($item->rating ? array_sum(json_decode($item->rating, true)) : 0);
                $item['avg_rating'] = (float)($item->avg_rating ? $item->avg_rating : 0);

                if ($trans) {
                    $item['translations'][] = [
                        'translationable_type' => 'App\Models\Item',
                        'translationable_id' => $item->id,
                        'locale' => 'en',
                        'key' => 'name',
                        'value' => $item->name
                    ];

                    $item['translations'][] = [
                        'translationable_type' => 'App\Models\Item',
                        'translationable_id' => $item->id,
                        'locale' => 'en',
                        'key' => 'description',
                        'value' => $item->description
                    ];
                }

                if (count($item['translations']) > 0) {
                    foreach ($item['translations'] as $translation) {
                        if ($translation['locale'] == $local) {
                            if ($translation['key'] == 'name') {
                                $item['name'] = $translation['value'];
                            }

                            if ($translation['key'] == 'title') {
                                $item['name'] = $translation['value'];
                            }

                            if ($translation['key'] == 'description') {
                                $item['description'] = $translation['value'];
                            }
                        }
                    }
                }
                if (!$trans) {
                    unset($item['translations']);
                }

                unset($item['store']);
                unset($item['rating']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
            $categories = [];
            foreach (json_decode($data['category_ids']) as $value) {
                $categories[] = ['id' => (string)$value->id, 'position' => $value->position];
            }
            $data['category_ids'] = $categories;

            $data['attributes'] = json_decode($data['attributes']);
            $data['choice_options'] = json_decode($data['choice_options']);
            $data['add_ons'] = self::addon_data_formatting(AddOn::whereIn('id', json_decode($data['add_ons']))->active()->get(), true, $trans, $local);
            foreach (json_decode($data['variations'], true) as $var) {
                array_push($variations, [
                    'type' => $var['type'],
                    'price' => (float)$var['price'],
                    'stock' => (int)($var['stock'] ?? 0)
                ]);
            }
            if ($data->title) {
                $data['name'] = $data->title;
                unset($data['title']);
            }
            if ($data->start_time) {
                $data['available_time_starts'] = $data->start_time->format('H:i');
                unset($data['start_time']);
            }
            if ($data->end_time) {
                $data['available_time_ends'] = $data->end_time->format('H:i');
                unset($data['end_time']);
            }
            if ($data->start_date) {
                $data['available_date_starts'] = $data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if ($data->end_date) {
                $data['available_date_ends'] = $data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }
            $data['variations'] = $variations;
            $data['food_variations'] = $data['food_variations']?json_decode($data['food_variations'], true):'';
            $data['store_name'] = $data->store->name;
            $data['module_type'] = $data->module->module_type;
            $data['zone_id'] = $data->store->zone_id;
            $data['store_discount'] = self::get_store_discount($data->store) ? $data->store->discount->discount : 0;
            $data['schedule_order'] = $data->store->schedule_order;
            $data['rating_count'] = (int)($data->rating ? array_sum(json_decode($data->rating, true)) : 0);
            $data['avg_rating'] = (float)($data->avg_rating ? $data->avg_rating : 0);

            if ($trans) {
                $data['translations'][] = [
                    'translationable_type' => 'App\Models\Item',
                    'translationable_id' => $data->id,
                    'locale' => 'en',
                    'key' => 'name',
                    'value' => $data->name
                ];

                $data['translations'][] = [
                    'translationable_type' => 'App\Models\Item',
                    'translationable_id' => $data->id,
                    'locale' => 'en',
                    'key' => 'description',
                    'value' => $data->description
                ];
            }

            if (count($data['translations']) > 0) {
                foreach ($data['translations'] as $translation) {
                    if ($translation['locale'] == $local) {
                        if ($translation['key'] == 'name') {
                            $data['name'] = $translation['value'];
                        }

                        if ($translation['key'] == 'title') {
                            $item['name'] = $translation['value'];
                        }

                        if ($translation['key'] == 'description') {
                            $data['description'] = $translation['value'];
                        }
                    }
                }
            }
            if (!$trans) {
                unset($data['translations']);
            }

            unset($data['store']);
            unset($data['rating']);
        }

        return $data;
    }

    public static function addon_data_formatting($data, $multi_data = false, $trans = false, $local = 'en')
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                if ($trans) {
                    $item['translations'][] = [
                        'translationable_type' => 'App\Models\AddOn',
                        'translationable_id' => $item->id,
                        'locale' => 'en',
                        'key' => 'name',
                        'value' => $item->name
                    ];
                }
                if (count($item->translations) > 0) {
                    foreach ($item['translations'] as $translation) {
                        if ($translation['locale'] == $local && $translation['key'] == 'name') {
                            $item['name'] = $translation['value'];
                        }
                    }
                }

                if (!$trans) {
                    unset($item['translations']);
                }

                $storage[] = $item;
            }
            $data = $storage;
        } else if (isset($data)) {
            if ($trans) {
                $data['translations'][] = [
                    'translationable_type' => 'App\Models\AddOn',
                    'translationable_id' => $data->id,
                    'locale' => 'en',
                    'key' => 'name',
                    'value' => $data->name
                ];
            }

            if (count($data->translations) > 0) {
                foreach ($data['translations'] as $translation) {
                    if ($translation['locale'] == $local && $translation['key'] == 'name') {
                        $data['name'] = $translation['value'];
                    }
                }
            }

            if (!$trans) {
                unset($data['translations']);
            }
        }
        return $data;
    }

    public static function category_data_formatting($data, $multi_data = false, $trans = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                if (count($item->translations) > 0) {
                    $item->name = $item->translations[0]['value'];
                }

                if (!$trans) {
                    unset($item['translations']);
                }

                $storage[] = $item;
            }
            $data = $storage;
        } else if (isset($data)) {
            if (count($data->translations) > 0) {
                $data->name = $data->translations[0]['value'];
            }

            if (!$trans) {
                unset($data['translations']);
            }
        }
        return $data;
    }

    public static function parcel_category_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                if (count($item['translations']) > 0) {
                    $translate = array_column($item['translations']->toArray(), 'value', 'key');
                    $item['name'] = $translate['name'];
                    $item['description'] = $translate['description'];
                    unset($item['translations']);
                }
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if (count($data['translations']) > 0) {
                $translate = array_column($data['translations']->toArray(), 'value', 'key');
                $data['title'] = $translate['title'];
                $data['description'] = $translate['description'];
                unset($data['translations']);
            }
        }
        return $data;
    }

    public static function basic_campaign_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];

                if ($item->start_date) {
                    $item['available_date_starts'] = $item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if ($item->end_date) {
                    $item['available_date_ends'] = $item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }

                if (count($item['translations']) > 0) {
                    $translate = array_column($item['translations']->toArray(), 'value', 'key');
                    $item['title'] = $translate['title'];
                    $item['description'] = $translate['description'];
                }

                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if ($data->start_date) {
                $data['available_date_starts'] = $data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if ($data->end_date) {
                $data['available_date_ends'] = $data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }
            if (count($data['translations']) > 0) {
                $translate = array_column($data['translations']->toArray(), 'value', 'key');
                $data['title'] = $translate['title'];
                $data['description'] = $translate['description'];
            }
        }

        return $data;
    }

    public static function store_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $ratings = StoreLogic::calculate_store_rating($item['rating']);
                unset($item['rating']);
                $item['avg_rating'] = $ratings['rating'];
                $item['rating_count'] = $ratings['total'];
                unset($item['campaigns']);
                unset($item['pivot']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $ratings = StoreLogic::calculate_store_rating($data['rating']);
            unset($data['rating']);
            $data['avg_rating'] = $ratings['rating'];
            $data['rating_count'] = $ratings['total'];
            unset($data['campaigns']);
            unset($data['pivot']);
        }

        return $data;
    }

    public static function wishlist_data_formatting($data, $multi_data = false)
    {
        $items = [];
        $stores = [];
        if ($multi_data == true) {

            foreach ($data as $temp) {
                if ($temp->item) {
                    $items[] = self::product_data_formatting($temp->item, false, false, app()->getLocale());
                }
                if ($temp->store) {
                    $stores[] = self::store_data_formatting($temp->store);
                }
            }
        } else {
            if ($data->item) {
                $items[] = self::product_data_formatting($data->item, false, false, app()->getLocale());
            }
            if ($data->store) {
                $stores[] = self::store_data_formatting($data->store);
            }
        }

        return ['item' => $items, 'store' => $stores];
    }

    public static function order_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data) {
            foreach ($data as $item) {
                if (isset($item['store'])) {
                    $item['store_name'] = $item['store']['name'];
                    $item['store_address'] = $item['store']['address'];
                    $item['store_phone'] = $item['store']['phone'];
                    $item['store_lat'] = $item['store']['latitude'];
                    $item['store_lng'] = $item['store']['longitude'];
                    $item['store_logo'] = $item['store']['logo'];
                    unset($item['store']);
                } else {
                    $item['store_name'] = null;
                    $item['store_address'] = null;
                    $item['store_phone'] = null;
                    $item['store_lat'] = null;
                    $item['store_lng'] = null;
                    $item['store_logo'] = null;
                }
                $item['item_campaign'] = 0;
                foreach ($item->details as $d) {
                    if ($d->item_campaign_id != null) {
                        $item['item_campaign'] = 1;
                    }
                }

                $item['delivery_address'] = $item->delivery_address ? json_decode($item->delivery_address, true) : null;
                $item['details_count'] = (int)$item->details->count();
                if($item['prescription_order'] && $item['order_attachment']){
                    $item['order_attachment'] = json_decode($item['order_attachment'], true);
                }
                unset($item['details']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if (isset($data['store'])) {
                $data['store_name'] = $data['store']['name'];
                $data['store_address'] = $data['store']['address'];
                $data['store_phone'] = $data['store']['phone'];
                $data['store_lat'] = $data['store']['latitude'];
                $data['store_lng'] = $data['store']['longitude'];
                $data['store_logo'] = $data['store']['logo'];
                unset($data['store']);
            } else {
                $data['store_name'] = null;
                $data['store_address'] = null;
                $data['store_phone'] = null;
                $data['store_lat'] = null;
                $data['store_lng'] = null;
                $data['store_logo'] = null;
            }

            $data['item_campaign'] = 0;
            foreach ($data->details as $d) {
                if ($d->item_campaign_id != null) {
                    $data['item_campaign'] = 1;
                }
            }
            $data['delivery_address'] = $data->delivery_address ? json_decode($data->delivery_address, true) : null;
            $data['details_count'] = (int)$data->details->count();
            if($data['prescription_order'] && $data['order_attachment']){
                $data['order_attachment'] = json_decode($data['order_attachment'], true);
            }
            unset($data['details']);
        }
        return $data;
    }

    public static function order_details_data_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $item['add_ons'] = json_decode($item['add_ons']);
            $item['variation'] = json_decode($item['variation'], true);
            $item['item_details'] = json_decode($item['item_details'], true);
            array_push($storage, $item);
        }
        $data = $storage;

        return $data;
    }

    public static function deliverymen_list_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'id' => $item['id'],
                'name' => $item['f_name'] . ' ' . $item['l_name'],
                'image' => $item['image'],
                'assigned_order_count' => $item['assigned_order_count'],
                'lat' => $item->last_location ? $item->last_location->latitude : false,
                'lng' => $item->last_location ? $item->last_location->longitude : false,
                'location' => $item->last_location ? $item->last_location->location : '',
            ];
        }
        $data = $storage;

        return $data;
    }

    public static function deliverymen_data_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $item['avg_rating'] = (float)(count($item->rating) ? (float)$item->rating[0]->average : 0);
            $item['rating_count'] = (int)(count($item->rating) ? $item->rating[0]->rating_count : 0);
            $item['lat'] = $item->last_location ? $item->last_location->latitude : null;
            $item['lng'] = $item->last_location ? $item->last_location->longitude : null;
            $item['location'] = $item->last_location ? $item->last_location->location : null;
            if ($item['rating']) {
                unset($item['rating']);
            }
            if ($item['last_location']) {
                unset($item['last_location']);
            }
            $storage[] = $item;
        }
        $data = $storage;

        return $data;
    }

    public static function get_business_settings($name)
    {
        $config = null;

        $paymentmethod = BusinessSetting::where('key', $name)->first();

        if ($paymentmethod) {
            $config = json_decode($paymentmethod->value, true);
        }

        return $config;
    }

    public static function currency_code()
    {
        if(!request()->is('/api*') && !session()->has('currency_code')){
            $currency = BusinessSetting::where(['key' => 'currency'])->first()->value;
            session()->put('currency_code',$currency);
        }else{
            $currency = BusinessSetting::where(['key' => 'currency'])->first()->value;
        }

        if(!request()->is('/api*')){
            $currency = session()->get('currency_code');
        }
       
        return $currency;
    }

    // public static function currency_symbol()
    // {
    //     $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
    //     return $currency_symbol;
    // }

    public static function currency_symbol()
    {
        if(!session()->has('currency_symbol')){
            $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
            session()->put('currency_symbol',$currency_symbol);
        }
        $currency_symbol = session()->get('currency_symbol');
        return $currency_symbol;
    }

    // public static function format_currency($value)
    // {
    //     $currency_symbol_position = BusinessSetting::where(['key' => 'currency_symbol_position'])->first()->value;

    //     return $currency_symbol_position == 'right' ? number_format($value, config('round_up_to_digit')) . ' ' . self::currency_symbol() : self::currency_symbol() . ' ' . number_format($value, config('round_up_to_digit'));
    // }

    public static function format_currency($value)
    {
        if(!session()->has('currency_symbol_position')){
            $currency_symbol_position = BusinessSetting::where(['key' => 'currency_symbol_position'])->first()->value;
            session()->put('currency_symbol_position',$currency_symbol_position);
        }
        $currency_symbol_position = session()->get('currency_symbol_position');
        return $currency_symbol_position == 'right' ? number_format($value, config('round_up_to_digit')) . ' ' . self::currency_symbol() : self::currency_symbol() . ' ' . number_format($value, config('round_up_to_digit'));
    }

    public static function send_push_notif_to_device($fcm_token, $data, $web_push_link = null)
    {
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array(
            "authorization: key=" . $key . "",
            "content-type: application/json"
        );

        if(isset($data['message'])){
            $message = $data['message'];
        }else{
            $message = '';
        }
        if(isset($data['conversation_id'])){
            $conversation_id = $data['conversation_id'];
        }else{
            $conversation_id = '';
        }
        if(isset($data['sender_type'])){
            $sender_type = $data['sender_type'];
        }else{
            $sender_type = '';
        }
        if(isset($data['module_id'])){
            $module_id = $data['module_id'];
        }else{
            $module_id = '';
        }
        if(isset($data['order_type'])){
            $order_type = $data['order_type'];
        }else{
            $order_type = '';
        }

        $click_action = "";
        if($web_push_link){
            $click_action = ',
            "click_action": "'.$web_push_link.'"';
        }

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "mutable_content": true,
            "data" : {
                "title":"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "conversation_id":"' . $conversation_id . '",
                "sender_type":"' . $sender_type . '",
                "module_id":"' . $module_id . '",
                "order_type":"' . $order_type . '",
                "is_read": 0
            },
            "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "body_loc_key":"' . $data['type'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound": "notification.wav",
                "android_channel_id": "6ammart"
                '.$click_action.'
            }
        }';
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function send_push_notif_to_topic($data, $topic, $type,$web_push_link = null)
    {
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array(
            "authorization: key=" . $key . "",
            "content-type: application/json"
        );
        if(isset($data['module_id'])){
            $module_id = $data['module_id'];
        }else{
            $module_id = '';
        }
        if(isset($data['order_type'])){
            $order_type = $data['order_type'];
        }else{
            $order_type = '';
        }

        $click_action = "";
        if($web_push_link){
            $click_action = ',
            "click_action": "'.$web_push_link.'"';
        }

        if (isset($data['order_id'])) {
            $postdata = '{
                "to" : "/topics/' . $topic . '",
                "mutable_content": true,
                "data" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "order_id":"' . $data['order_id'] . '",
                    "module_id":"' . $module_id . '",
                    "order_type":"' . $order_type . '",
                    "is_read": 0,
                    "type":"' . $type . '"
                },
                "notification" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "order_id":"' . $data['order_id'] . '",
                    "title_loc_key":"' . $data['order_id'] . '",
                    "body_loc_key":"' . $type . '",
                    "type":"' . $type . '",
                    "is_read": 0,
                    "icon" : "new",
                    "sound": "notification.wav",
                    "android_channel_id": "6ammart"
                    '.$click_action.'
                  }
            }';
        } else {
            $postdata = '{
                "to" : "/topics/' . $topic . '",
                "mutable_content": true,
                "data" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "is_read": 0,
                    "type":"' . $type . '"
                },
                "notification" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "body_loc_key":"' . $type . '",
                    "type":"' . $type . '",
                    "is_read": 0,
                    "icon" : "new",
                    "sound": "notification.wav",
                    "android_channel_id": "6ammart"
                    '.$click_action.'
                  }
            }';
        }


        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }


    public static function rating_count($item_id, $rating)
    {
        return Review::where(['item_id' => $item_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($item, $price)
    {
        if ($item['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $item['tax'];
        } else {
            $price_tax = $item['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['store_discount']) {
            $price_discount = ($price / 100) * $product['store_discount'];
        } else if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function get_product_discount($product)
    {
        $store_discount = self::get_store_discount($product->store);
        if ($store_discount) {
            $discount = $store_discount['discount'] . ' %';
        } else if ($product['discount_type'] == 'percent') {
            $discount = $product['discount'] . ' %';
        } else {
            $discount = self::format_currency($product['discount']);
        }
        return $discount;
    }

    public static function product_discount_calculate($product, $price, $store)
    {
        $store_discount = self::get_store_discount($store);
        if (isset($store_discount)) {
            $price_discount = ($price / 100) * $store_discount['discount'];
        } else if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function get_price_range($product, $discount = false)
    {
        $lowest_price = $product->price;
        $highest_price = $product->price;
        if ($product->variations && is_array(json_decode($product['variations'], true))) {
            foreach (json_decode($product->variations) as $key => $variation) {
                if ($lowest_price > $variation->price) {
                    $lowest_price = round($variation->price, 2);
                }
                if ($highest_price < $variation->price) {
                    $highest_price = round($variation->price, 2);
                }
            }
        }

        if ($discount) {
            $lowest_price -= self::product_discount_calculate($product, $lowest_price, $product->store);
            $highest_price -= self::product_discount_calculate($product, $highest_price, $product->store);
        }
        $lowest_price = self::format_currency($lowest_price);
        $highest_price = self::format_currency($highest_price);

        if ($lowest_price == $highest_price) {
            return $lowest_price;
        }
        return $lowest_price . ' - ' . $highest_price;
    }
    public static function get_food_price_range($product, $discount = false)
    {
        $lowest_price = $product->price;
        // $highest_price = $product->price;
        // if ($product->variations && is_array(json_decode($product['variations'], true))) {
        //     foreach (json_decode($product->variations) as $key => $variation) {
        //         if ($lowest_price > $variation->price) {
        //             $lowest_price = round($variation->price, 2);
        //         }
        //         if ($highest_price < $variation->price) {
        //             $highest_price = round($variation->price, 2);
        //         }
        //     }
        // }

        if ($discount) {
            $lowest_price -= self::product_discount_calculate($product, $lowest_price, $product->store);
            // $highest_price -= self::product_discount_calculate($product, $highest_price, $product->store);
        }
        $lowest_price = self::format_currency($lowest_price);
        // $highest_price = self::format_currency($highest_price);

        // if ($lowest_price == $highest_price) {
        //     return $lowest_price;
        // }
        return $lowest_price;
    }

    public static function get_store_discount($store)
    {
        if ($store->discount) {
            if (date('Y-m-d', strtotime($store->discount->start_date)) <= now()->format('Y-m-d') && date('Y-m-d', strtotime($store->discount->end_date)) >= now()->format('Y-m-d') && date('H:i', strtotime($store->discount->start_time)) <= now()->format('H:i') && date('H:i', strtotime($store->discount->end_time)) >= now()->format('H:i')) {
                return [
                    'discount' => $store->discount->discount,
                    'min_purchase' => $store->discount->min_purchase,
                    'max_discount' => $store->discount->max_discount
                ];
            }
        }
        return null;
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    // public static function order_status_update_message($status)
    // {
    //     if ($status == 'pending') {
    //         $data = BusinessSetting::where('key', 'order_pending_message')->first()->value;
    //     } elseif ($status == 'confirmed') {
    //         $data = BusinessSetting::where('key', 'order_confirmation_msg')->first()->value;
    //     } elseif ($status == 'processing') {
    //         $data = BusinessSetting::where('key', 'order_processing_message')->first()->value;
    //     } elseif ($status == 'picked_up') {
    //         $data = BusinessSetting::where('key', 'out_for_delivery_message')->first()->value;
    //     } elseif ($status == 'handover') {
    //         $data = BusinessSetting::where('key', 'order_handover_message')->first()->value;
    //     } elseif ($status == 'delivered') {
    //         $data = BusinessSetting::where('key', 'order_delivered_message')->first()->value;
    //     } elseif ($status == 'delivery_boy_delivered') {
    //         $data = BusinessSetting::where('key', 'delivery_boy_delivered_message')->first()->value;
    //     } elseif ($status == 'accepted') {
    //         $data = BusinessSetting::where('key', 'delivery_boy_assign_message')->first()->value;
    //     } elseif ($status == 'canceled') {
    //         $data = BusinessSetting::where('key', 'order_cancled_message')->first()->value;
    //     } elseif ($status == 'refunded') {
    //         $data = BusinessSetting::where('key', 'order_refunded_message')->first()->value;
    //     }elseif ($status == 'refund_request_canceled') {
    //         $data = BusinessSetting::where('key', 'refund_request_canceled')->first()->value;
    //     } else {
    //         $data = '{"status":"0","message":""}';
    //     }

    //     $res = json_decode($data, true);

    //     if ($res['status'] == 0) {
    //         return 0;
    //     }
    //     return $res['message'];
    // }
    // public static function order_status_update_message($status , $module_type)
    // {
    //     if ($status == 'pending') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_pending_message')->first();
    //     } elseif ($status == 'confirmed') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_confirmation_msg')->first();
    //     } elseif ($status == 'processing') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_processing_message')->first();
    //     } elseif ($status == 'picked_up') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'out_for_delivery_message')->first();
    //     } elseif ($status == 'handover') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_handover_message')->first();
    //     } elseif ($status == 'delivered') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_delivered_message')->first();
    //     } elseif ($status == 'delivery_boy_delivered') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'delivery_boy_delivered_message')->first();
    //     } elseif ($status == 'accepted') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'delivery_boy_assign_message')->first();
    //     } elseif ($status == 'canceled') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_cancled_message')->first();
    //     } elseif ($status == 'refunded') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'order_refunded_message')->first();
    //     }elseif ($status == 'refund_request_canceled') {
    //         $data = NotificationMessage::where('module_type',$module_type)->where('key', 'refund_request_canceled')->first();
    //     } else {
    //         $data = '{"status":"0","message":""}';
    //     }

    //     $res = $data;

    //     if($res){
    //         if ($res['status'] == 0) {
    //             return 0;
    //         }
    //         return $res['message'];
    //     }else{
    //         return true;
    //     }

    // }

    public static function order_status_update_message($status,$module_type, $lang='en')
    {
        if ($status == 'pending') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_pending_message')->first();
        } elseif ($status == 'confirmed') {
            $data =  NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_confirmation_msg')->first();
        } elseif ($status == 'processing') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_processing_message')->first();
        } elseif ($status == 'picked_up') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'out_for_delivery_message')->first();
        } elseif ($status == 'handover') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_handover_message')->first();
        } elseif ($status == 'delivered') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_delivered_message')->first();
        } elseif ($status == 'delivery_boy_delivered') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'delivery_boy_delivered_message')->first();
        } elseif ($status == 'accepted') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'delivery_boy_assign_message')->first();
        } elseif ($status == 'canceled') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_cancled_message')->first();
        } elseif ($status == 'refunded') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'order_refunded_message')->first();
        } elseif ($status == 'refund_request_canceled') {
            $data = NotificationMessage::with(['translations'=>function($query)use($lang){
                $query->where('locale', $lang);
            }])->where('module_type',$module_type)->where('key', 'refund_request_canceled')->first();
        } else {
            $data = ["status"=>"0","message"=>"",'translations'=>[]];
        }

        if($data){
            if ($data['status'] == 0) {
                return 0;
            }
            return count($data->translations) > 0 ? $data->translations[0]->value : $data['message'];
        }else{
            return false;
        }
    }

    public static function send_order_notification($order)
    {

        try {

            if(($order->payment_method == 'cash_on_delivery' && $order->order_status == 'pending' )||($order->payment_method != 'cash_on_delivery' && $order->order_status == 'confirmed' )){
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'image' => '',
                    'module_id' => $order->module_id,
                    'order_type' => $order->order_type,
                    'type' => 'new_order',
                ];
                self::send_push_notif_to_topic($data, 'admin_message', 'order_request', url('/').'/admin/order/list/all');
            }

            $status = ($order->order_status == 'delivered' && $order->delivery_man) ? 'delivery_boy_delivered' : $order->order_status;
            $value = self::order_status_update_message($status,$order->module->module_type,$order->customer?
            $order->customer->current_language_key:'en');
            if ($value) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                ];
                self::send_push_notif_to_device($order->customer->cm_firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if ($status == 'picked_up') {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                ];
                if($order->store && $order->store->vendor){
                    self::send_push_notif_to_device($order->store->vendor->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $order->store->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            if ($order->order_type == 'delivery' && !$order->scheduled && $status == 'pending' && $order->payment_method == 'cash_on_delivery' && config('order_confirmation_model') == 'deliveryman') {
                if ($order->store->self_delivery_system) {
                    $data = [
                        'title' => translate('messages.order_push_title'),
                        'description' => translate('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'module_id' => $order->module_id,
                        'order_type' => $order->order_type,
                        'image' => '',
                        'type' => 'new_order',
                    ];
                    if($order->store && $order->store->vendor){
                        self::send_push_notif_to_device($order->store->vendor->firebase_token, $data);
                        $web_push_link = url('/').'/store-panel/order/list/all';
                        self::send_push_notif_to_topic($data, "store_panel_{$order->store_id}_message", 'new_order', $web_push_link);
                        DB::table('user_notifications')->insert([
                            'data' => json_encode($data),
                            'vendor_id' => $order->store->vendor_id,
                            'module_id' => $order->module_id,
                            'order_type' => $order->order_type,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                } else {
                    $data = [
                        'title' => translate('messages.order_push_title'),
                        'description' => translate('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'module_id' => $order->module_id,
                        'order_type' => $order->order_type,
                        'image' => '',
                    ];
                    if($order->zone){

                        self::send_push_notif_to_topic($data, $order->zone->deliveryman_wise_topic, 'order_request');
                    }
                }
                // self::send_push_notif_to_topic($data, 'admin_message', 'order_request', url('/').'/admin/order/list/all');
            }

            if ($order->order_type == 'parcel' && in_array($order->order_status, ['pending', 'confirmed'])) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'module_id' => $order->module_id,
                    'order_type' => 'parcel_order',
                    'image' => '',
                ];
                if($order->zone){

                    self::send_push_notif_to_topic($data, $order->zone->deliveryman_wise_topic, 'order_request');
                }
                // self::send_push_notif_to_topic($data, 'admin_message', 'order_request');
            }

            if ($order->order_type == 'delivery' && !$order->scheduled && $order->order_status == 'pending' && $order->payment_method == 'cash_on_delivery' && config('order_confirmation_model') == 'store') {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'module_id' => $order->module_id,
                    'order_type' => $order->order_type,
                    'image' => '',
                    'type' => 'new_order',
                ];
                if($order->store && $order->store->vendor){
                    self::send_push_notif_to_device($order->store->vendor->firebase_token, $data);
                    $web_push_link = url('/').'/store-panel/order/list/all';
                    self::send_push_notif_to_topic($data, "store_panel_{$order->store_id}_message", 'new_order', $web_push_link);
                    // self::send_push_notif_to_topic($data, 'admin_message', 'order_request');
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $order->store->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            if (!$order->scheduled && (($order->order_type == 'take_away' && $order->order_status == 'pending') || ($order->payment_method != 'cash_on_delivery' && $order->order_status == 'confirmed'))) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'new_order',
                ];
                if($order->store && $order->store->vendor){
                    self::send_push_notif_to_device($order->store->vendor->firebase_token, $data);
                    $web_push_link = url('/').'/store-panel/order/list/all';
                    self::send_push_notif_to_topic($data, "store_panel_{$order->store_id}_message", 'new_order', $web_push_link);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $order->store->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            if ($order->order_status == 'confirmed' && $order->order_type != 'take_away' && config('order_confirmation_model') == 'deliveryman' && $order->payment_method == 'cash_on_delivery') {
                if ($order->store->self_delivery_system) {
                    $data = [
                        'title' => translate('messages.order_push_title'),
                        'description' => translate('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'module_id' => $order->module_id,
                        'order_type' => $order->order_type,
                        'image' => '',
                    ];

                    self::send_push_notif_to_topic($data, "restaurant_dm_" . $order->store_id, 'new_order');
                } else {
                    $data = [
                        'title' => translate('messages.order_push_title'),
                        'description' => translate('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'module_id' => $order->module_id,
                        'order_type' => $order->order_type,
                        'image' => '',
                        'type' => 'new_order',
                    ];
                    if($order->store && $order->store->vendor){
                        self::send_push_notif_to_device($order->store->vendor->firebase_token, $data);
                        $web_push_link = url('/').'/store-panel/order/list/all';
                        self::send_push_notif_to_topic($data, "store_panel_{$order->store_id}_message", 'new_order', $web_push_link);
                        DB::table('user_notifications')->insert([
                            'data' => json_encode($data),
                            'vendor_id' => $order->store->vendor_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            if ($order->order_type == 'delivery' && !$order->scheduled && $order->order_status == 'confirmed'  && ($order->payment_method != 'cash_on_delivery' || config('order_confirmation_model') == 'store')) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'module_id' => $order->module_id,
                    'order_type' => $order->order_type,
                    'image' => '',
                ];
                if ($order->store->self_delivery_system) {
                    self::send_push_notif_to_topic($data, "restaurant_dm_" . $order->store_id, 'order_request');
                } else
                 {if($order->zone){

                     self::send_push_notif_to_topic($data, $order->zone->deliveryman_wise_topic, 'order_request');
                 }
                }
            }

            if (in_array($order->order_status, ['processing', 'handover']) && $order->delivery_man) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => $order->order_status == 'processing' ? translate('messages.Proceed_for_cooking') : translate('messages.ready_for_delivery'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status'
                ];
                self::send_push_notif_to_device($order->delivery_man->fcm_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'delivery_man_id' => $order->delivery_man->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            try {
                if ($order->order_status == 'confirmed' && $order->payment_method != 'cash_on_delivery' && config('mail.status')) {
                        Mail::to($order->customer->email)->send(new PlaceOrder($order->id));
                }
            } catch (\Exception $ex) {
                info($ex);
            }
            return true;
        } catch (\Exception $e) {
            info($e);
        }
        return false;
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function env_update($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key . '=' . env($key),
                $key . '=' . $value,
                file_get_contents($path)
            ));
        }
    }

    public static function env_key_replace($key_from, $key_to, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key_from . '=' . env($key_from),
                $key_to . '=' . $value,
                file_get_contents($path)
            ));
        }
    }

    public static  function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") Helpers::remove_dir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_store_id()
    {
        if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->store->id;
        }
        return auth('vendor')->user()->stores[0]->id;
    }

    public static function get_vendor_id()
    {
        if (auth('vendor')->check()) {
            return auth('vendor')->id();
        } else if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->vendor_id;
        }
        return 0;
    }

    public static function get_vendor_data()
    {
        if (auth('vendor')->check()) {
            return auth('vendor')->user();
        } else if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->vendor;
        }
        return 0;
    }

    public static function get_loggedin_user()
    {
        if (auth('vendor')->check()) {
            return auth('vendor')->user();
        } else if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user();
        }
        return 0;
    }

    public static function get_store_data()
    {
        if (auth('vendor_employee')->check()) {
            return auth('vendor_employee')->user()->store;
        }
        return auth('vendor')->user()->stores[0];
    }

    public static function upload(string $dir, string $format, $image = null)
    {
        if ($image != null) {
            $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->putFileAs($dir, $image, $imageName);
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if ($image == null) {
            return $old_image;
        }
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }

    public static function format_coordiantes($coordinates)
    {
        $data = [];
        foreach ($coordinates as $coord) {
            $data[] = (object)['lat' => $coord->getlat(), 'lng' => $coord->getlng()];
        }
        return $data;
    }

    public static function module_permission_check($mod_name)
    {
        if (!auth('admin')->user()->role) {
            return false;
        }

        if ($mod_name == 'zone' && auth('admin')->user()->zone_id) {
            return false;
        }

        $permission = auth('admin')->user()->role->modules;
        if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admin')->user()->role_id == 1) {
            return true;
        }
        return false;
    }

    public static function employee_module_permission_check($mod_name)
    {
        if (auth('vendor')->check()) {
            if ($mod_name == 'reviews') {
                return auth('vendor')->user()->stores[0]->reviews_section;
            } else if ($mod_name == 'deliveryman') {
                return auth('vendor')->user()->stores[0]->self_delivery_system;
            } else if ($mod_name == 'pos') {
                return auth('vendor')->user()->stores[0]->pos_system;
            } else if ($mod_name == 'addon') {
                return config('module.' . auth('vendor')->user()->stores[0]->module->module_type)['add_on'];
            }
            return true;
        } else if (auth('vendor_employee')->check()) {
            $permission = auth('vendor_employee')->user()->role->modules;
            if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
                if ($mod_name == 'reviews') {
                    return auth('vendor_employee')->user()->store->reviews_section;
                } else if ($mod_name == 'deliveryman') {
                    return auth('vendor_employee')->user()->store->self_delivery_system;
                } else if ($mod_name == 'pos') {
                    return auth('vendor_employee')->user()->store->pos_system;
                } else if ($mod_name == 'addon') {
                    return config('module.' . auth('vendor_employee')->user()->store->module->module_type)['add_on'];
                }
                return true;
            }
        }

        return false;
    }
    public static function calculate_addon_price($addons, $add_on_qtys)
    {
        $add_ons_cost = 0;
        $data = [];
        if ($addons) {
            foreach ($addons as $key2 => $addon) {
                if ($add_on_qtys == null) {
                    $add_on_qty = 1;
                } else {
                    $add_on_qty = $add_on_qtys[$key2];
                }
                $data[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price, 'quantity' => $add_on_qty];
                $add_ons_cost += $addon['price'] * $add_on_qty;
            }
            return ['addons' => $data, 'total_add_on_price' => $add_ons_cost];
        }
        return null;
    }

    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $oldValue = env($envKey);
        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender()
    {
        $class = new LaravelchkController();
        $response = $class->actch();
        return json_decode($response->getContent(), true);
    }

    public static function insert_business_settings_key($key, $value = null)
    {
        $data =  BusinessSetting::where('key', $key)->first();
        if (!$data) {
            DB::table('business_settings')->updateOrInsert(['key' => $key], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return true;
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "es-419" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    public static function get_view_keys()
    {
        $keys = BusinessSetting::whereIn('key', ['toggle_veg_non_veg', 'toggle_dm_registration', 'toggle_store_registration'])->get();
        $data = [];
        foreach ($keys as $key) {
            $data[$key->key] = (bool)$key->value;
        }
        return $data;
    }
    public static function default_lang()
    {
        if (strpos(url()->current(), '/api')) {
            $lang = App::getLocale();
        } elseif (session()->has('local')) {
            $lang = session('local');
        } else {
            $data = Helpers::get_business_settings('language');
            $code = 'en';
            $direction = 'ltr';
            foreach ($data as $ln) {
                if (is_array($ln) && array_key_exists('default', $ln) && $ln['default']) {
                    $code = $ln['code'];
                    if (array_key_exists('direction', $ln)) {
                        $direction = $ln['direction'];
                    }
                }
            }
            session()->put('local', $code);
            $lang = $code;
        }
        return $lang;
    }
    //Mail Config Check
    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', $str);
    }

    //Generate referer code

    // public static function generate_referer_code($user)
    // {
    //     $user_name = explode('@', $user->email)[0];
    //     $user_id = $user->id;
    //     //dd($user_id);
    //     $uid_length = strlen($user->id);
    //     if (strlen($user_name) > 10 - $uid_length) {
    //         $user_name = substr($user_name, 0, 10 - $uid_length);
    //     } else if (strlen($user_name) < 10 - $uid_length) {
    //         $user_id = $user_id * pow(10, ((10 - $uid_length) - strlen($user_name)));
    //     }
    //     return $user_name . $user_id;
    // }

    public static function generate_referer_code() {
        $ref_code = strtoupper(Str::random(10));

        if (self::referer_code_exists($ref_code)) {
            return self::generate_referer_code();
        }

        return $ref_code;
    }

    public static function referer_code_exists($ref_code) {
        return User::where('ref_code', '=', $ref_code)->exists();
    }

    public static function number_format_short( $n ) {
        if ($n < 900) {
            // 0 - 900
            $n = $n;
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n = $n / 1000;
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n = $n / 1000000;
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n = $n / 1000000000;
            $suffix = 'B';
        } else {
            // 0.9t+
            $n = $n / 1000000000000;
            $suffix = 'T';
        }

        if(!session()->has('currency_symbol_position')){
            $currency_symbol_position = BusinessSetting::where(['key' => 'currency_symbol_position'])->first()->value;
            session()->put('currency_symbol_position',$currency_symbol_position);
        }
        $currency_symbol_position = session()->get('currency_symbol_position');

        return $currency_symbol_position == 'right' ? number_format($n, config('round_up_to_digit')).$suffix . ' ' . self::currency_symbol() : self::currency_symbol() . ' ' . number_format($n, config('round_up_to_digit')).$suffix;
    }
    public static function export_attributes($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
            ];
        }
        return $data;
    }

    public static function push_notification_export_data($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.title') => $item['title'],
                 translate('messages.description') => $item['description'],
                 translate('messages.zone') => $item->zone?$item->zone->name: '',
                 translate('messages.status') => $item['status'],
                 translate('messages.tergat') => $item['tergat'],
            ];
        }
        return $data;
    }

    public static function export_categories($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
                 translate('messages.module') => $item->module->module_name,
                 translate('messages.status') => $item['status'],
                 translate('messages.priority') => $item['priority'],
            ];
        }
        return $data;
    }

    public static function export_store_withdraw($collection){
        $data = [];
        $status = ['pending','approved','denied'];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.amount') => $item->amount,
                 translate('messages.store') => isset($item->vendor) ? $item->vendor->stores[0]->name : '',
                 translate('messages.request_time') => date('Y-m-d '.config('timeformat'),strtotime($item->created_at)),
                 translate('messages.status') => isset($status[$item->approved])?translate("messages.".$status[$item->approved]):"",
            ];
        }
        return $data;
    }

    public static function export_account_transaction($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.collect_from') => $item->store ? $item->store->name : ($item->deliveryman ? $item->deliveryman->f_name . ' ' . $item->deliveryman->l_name : translate('messages.not_found')),
                 translate('messages.type') => $item->from_type,
                 translate('messages.received_at') => $item->created_at->format('Y-m-d '.config('timeformat')),
                 translate('messages.amount') => $item->amount,
                 translate('messages.reference') => $item->ref,
            ];
        }
        return $data;
    }

    public static function export_dm_earning($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                translate('messages.name') => isset($item->delivery_man) ? $item->delivery_man->f_name.' '.$item->delivery_man->l_name : translate('messages.not_found'),
                 translate('messages.received_at') => $item->created_at->format('Y-m-d '.config('timeformat')),
                 translate('messages.amount') => $item->amount,
                 translate('messages.method') => $item->method,
                 translate('messages.reference') => $item->ref,
            ];
        }
        return $data;
    }

    public static function export_items($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
                 translate('messages.category') => $item->category?$item->category->name:'',
                 translate('messages.store') => $item->store?$item->store->name:'',
                 translate('messages.price') => $item['price'],
                 translate('messages.status') => $item['status'],
            ];
        }
        return $data;
    }

    public static function export_store_item($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
                 translate('messages.type') => $item->category?$item->category->name:'',
                 translate('messages.price') => $item['price'],
                 translate('messages.status') => $item['status'],
            ];
        }
        return $data;
    }

    public static function export_stores($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.store_name') => $item['name'],
                 translate('messages.store_phone') => $item['phone'],
                 translate('messages.module') => $item->module->module_name,
                 translate('messages.owner_name') => $item->vendor->f_name.' '.$item->vendor->l_name,
                 translate('messages.owner_phone') => $item->vendor->phone,
                 translate('messages.zone') => $item->zone?$item->zone->name:'',
                 translate('messages.featured') => $item['featured'],
                 translate('messages.status') => $item['status'],
            ];
        }
        return $data;
    }

    public static function export_units($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.unit') => $item['unit'],
            ];
        }
        return $data;
    }

    public static function export_customers($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item->f_name.' '.$item->l_name,
                 translate('messages.phone') => $item['phone'],
                 translate('messages.email') => $item['email'],
                 translate('messages.total_order') => $item['order_count'],
                 translate('messages.status') => $item['status'],
            ];
        }
        return $data;
    }

    public static function export_day_wise_report($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.order_id') => $item['order_id'],
                 translate('messages.store')=>$item->order->store?$item->order->store->name:translate('messages.invalid'),
                 translate('messages.customer_name')=>$item->order->customer?$item->order->customer['f_name'].' '.$item->order->customer['l_name']:translate('messages.invalid').' '.translate('messages.customer').' '.translate('messages.data'),
                 translate('total_item_amount')=>\App\CentralLogics\Helpers::format_currency($item->order['order_amount'] - $item->order['dm_tips']-$item->order['delivery_charge'] - $item['tax'] + $item->order['coupon_discount_amount'] + $item->order['store_discount_amount']),
                 translate('item_discount')=>\App\CentralLogics\Helpers::format_currency($item->order->details->sum('discount_on_item')),
                 translate('coupon_discount')=>\App\CentralLogics\Helpers::format_currency($item->order['coupon_discount_amount']),
                 translate('discounted_amount')=>\App\CentralLogics\Helpers::format_currency($item->order['coupon_discount_amount'] + $item->order['store_discount_amount']),
                 translate('messages.tax')=>\App\CentralLogics\Helpers::format_currency($item->order['total_tax_amount']),
                 translate('messages.delivery_charge')=>\App\CentralLogics\Helpers::format_currency($item['delivery_charge']),
                 translate('messages.total_order_amount') => \App\CentralLogics\Helpers::format_currency($item['order_amount']),
                 translate('messages.admin_discount') => \App\CentralLogics\Helpers::format_currency($item['admin_expense']),
                 translate('messages.store_discount') => \App\CentralLogics\Helpers::format_currency($item->order['store_discount_amount']),
                 translate('messages.admin_commission') => \App\CentralLogics\Helpers::format_currency(($item->admin_commission + $item->admin_expense) - $item->delivery_fee_comission),
                 translate('Comission on delivery fee') => \App\CentralLogics\Helpers::format_currency($item['delivery_fee_comission']),
                 translate('admin_net_income') => \App\CentralLogics\Helpers::format_currency($item['admin_commission']),
                 translate('store_net_income') => \App\CentralLogics\Helpers::format_currency($item['store_amount'] - $item['tax']),
                 translate('messages.amount_received_by') => $item['received_by'],
                 translate('messages.payment_method')=>translate(str_replace('_', ' ', $item->order['payment_method'])),
                 translate('messages.payment_status') => $item->status ? translate("messages.refunded") : translate("messages.completed"),
            ];
        }
        return $data;
    }


    public static function export_expense_wise_report($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.type') => $item['type'],
                 translate('messages.amount') => $item['amount'],
                 translate('messages.order_id') => $item['order_id'],
                 translate('messages.expense_date') =>  $item['created_at']->format('Y/m/d ' . config('timeformat')),
            ];
        }
        return $data;
    }

    public static function export_item_wise_report($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
                 translate('messages.module') =>$item->module ? $item->module->module_name : '',
                 translate('messages.store') => $item->store ? $item->store->name : '',
                 translate('messages.order') => $item->orders_count,
                 translate('messages.price') => \App\CentralLogics\Helpers::format_currency($item->price),
                 translate('messages.total_amount_sold') => \App\CentralLogics\Helpers::format_currency($item->orders_sum_price),
                 translate('messages.total_discount_given') => \App\CentralLogics\Helpers::format_currency($item->orders_sum_discount_on_item),
                 translate('messages.average_sale_value') => $item->orders_count>0? \App\CentralLogics\Helpers::format_currency(($item->orders_sum_price-$item->orders_sum_discount_on_item)/$item->orders_count):0 ,
                 translate('messages.average_ratings') => round($item->avg_rating,1),
            ];
        }
        return $data;
    }

    public static function export_stock_wise_report($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
                 translate('messages.store') => $item->store?$item->store->name : '',
                 translate('messages.zone') => ($item->store && $item->store->zone) ? $item->store->zone->name:'',
                 translate('messages.stock') => $item['stock'],
            ];
        }
        return $data;
    }

    public static function export_delivery_men($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item->f_name.' '.$item->l_name,
                 translate('messages.phone') => $item['phone'],
                 translate('messages.zone') => $item->zone?$item->zone->name:'',
                 translate('messages.total_order') => $item['order_count'],
                 translate('messages.currently_assigned_orders') => (int) $item['current_orders'],
                 translate('messages.status') => $item['status'],
            ];
        }
        return $data;
    }

    public static function hex_to_rbg($color){
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        $output = "$r, $g, $b";
        return $output;
    }

    public static function expenseCreate($amount,$type,$datetime,$order_id,$description='')
    {
        $expense = new Expense();
        $expense->amount = $amount;
        $expense->order_id = $order_id;
        $expense->type = $type;
        $expense->description = $description;
        $expense->created_at = $datetime;
        $expense->updated_at = $datetime;
        return $expense->save();
    }

    public static function get_varient(array $product_variations, array $variations)
    {
        $result = [];
        $variation_price = 0;

        foreach($variations as $k=> $variation){
            foreach($product_variations as  $product_variation){
                if( isset($variation['values']) && isset($product_variation['values']) && $product_variation['name'] == $variation['name']  ){
                    $result[$k] = $product_variation;
                    $result[$k]['values'] = [];
                    foreach($product_variation['values'] as $key=> $option){
                        if(in_array($option['label'], $variation['values']['label'])){
                            $result[$k]['values'][] = $option;
                            $variation_price += $option['optionPrice'];
                        }
                    }
                }
            }
        }

        return ['price'=>$variation_price,'variations'=>$result];
    }

    public static function food_variation_price($product, $variations)
    {
        // $match = json_decode($variations, true)[0];
        $match = $variations;
        $result = 0;
        // foreach (json_decode($product['variations'], true) as $property => $value) {
        //     if ($value['type'] == $match['type']) {
        //         $result = $value['price'];
        //     }
        // }
            foreach($product as $product_variation){
                foreach($product_variation['values'] as $option){
                    foreach($match as $variation){
                        if($product_variation['name'] == $variation['name'] && isset($variation['values']) && in_array($option['label'], $variation['values']['label'])){
                            $result += $option['optionPrice'];
                        }
                    }
                }
            }

        return $result;
    }

    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/../../storage/tmp','default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        /* $mpdf->AddPage('XL', '', '', '', '', 10, 10, 10, '10', '270', '');*/
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($file_prefix . $file_postfix . '.pdf', 'D');
    }

    public static function auto_translator($q, $sl, $tl)
    {
        $res = file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=" . $sl . "&tl=" . $tl . "&hl=hl&q=" . urlencode($q), $_SERVER['DOCUMENT_ROOT'] . "/transes.html");
        $res = json_decode($res);
        return str_replace('_',' ',$res[0][0][0]);
    }

    public static function getLanguageCode(string $country_code): string
    {
        $locales = array(
            'en-English(default)',
            'af-Afrikaans',
            'sq-Albanian - shqip',
            'am-Amharic - አማርኛ',
            'ar-Arabic - العربية',
            'an-Aragonese - aragonés',
            'hy-Armenian - հայերեն',
            'ast-Asturian - asturianu',
            'az-Azerbaijani - azərbaycan dili',
            'eu-Basque - euskara',
            'be-Belarusian - беларуская',
            'bn-Bengali - বাংলা',
            'bs-Bosnian - bosanski',
            'br-Breton - brezhoneg',
            'bg-Bulgarian - български',
            'ca-Catalan - català',
            'ckb-Central Kurdish - کوردی (دەستنوسی عەرەبی)',
            'zh-Chinese - 中文',
            'zh-HK-Chinese (Hong Kong) - 中文（香港）',
            'zh-CN-Chinese (Simplified) - 中文（简体）',
            'zh-TW-Chinese (Traditional) - 中文（繁體）',
            'co-Corsican',
            'hr-Croatian - hrvatski',
            'cs-Czech - čeština',
            'da-Danish - dansk',
            'nl-Dutch - Nederlands',
            'en-AU-English (Australia)',
            'en-CA-English (Canada)',
            'en-IN-English (India)',
            'en-NZ-English (New Zealand)',
            'en-ZA-English (South Africa)',
            'en-GB-English (United Kingdom)',
            'en-US-English (United States)',
            'eo-Esperanto - esperanto',
            'et-Estonian - eesti',
            'fo-Faroese - føroyskt',
            'fil-Filipino',
            'fi-Finnish - suomi',
            'fr-French - français',
            'fr-CA-French (Canada) - français (Canada)',
            'fr-FR-French (France) - français (France)',
            'fr-CH-French (Switzerland) - français (Suisse)',
            'gl-Galician - galego',
            'ka-Georgian - ქართული',
            'de-German - Deutsch',
            'de-AT-German (Austria) - Deutsch (Österreich)',
            'de-DE-German (Germany) - Deutsch (Deutschland)',
            'de-LI-German (Liechtenstein) - Deutsch (Liechtenstein)
            ',
            'de-CH-German (Switzerland) - Deutsch (Schweiz)',
            'el-Greek - Ελληνικά',
            'gn-Guarani',
            'gu-Gujarati - ગુજરાતી',
            'ha-Hausa',
            'haw-Hawaiian - ʻŌlelo Hawaiʻi',
            'he-Hebrew - עברית',
            'hi-Hindi - हिन्दी',
            'hu-Hungarian - magyar',
            'is-Icelandic - íslenska',
            'id-Indonesian - Indonesia',
            'ia-Interlingua',
            'ga-Irish - Gaeilge',
            'it-Italian - italiano',
            'it-IT-Italian (Italy) - italiano (Italia)',
            'it-CH-Italian (Switzerland) - italiano (Svizzera)',
            'ja-Japanese - 日本語',
            'kn-Kannada - ಕನ್ನಡ',
            'kk-Kazakh - қазақ тілі',
            'km-Khmer - ខ្មែរ',
            'ko-Korean - 한국어',
            'ku-Kurdish - Kurdî',
            'ky-Kyrgyz - кыргызча',
            'lo-Lao - ລາວ',
            'la-Latin',
            'lv-Latvian - latviešu',
            'ln-Lingala - lingála',
            'lt-Lithuanian - lietuvių',
            'mk-Macedonian - македонски',
            'ms-Malay - Bahasa Melayu',
            'ml-Malayalam - മലയാളം',
            'mt-Maltese - Malti',
            'mr-Marathi - मराठी',
            'mn-Mongolian - монгол',
            'ne-Nepali - नेपाली',
            'no-Norwegian - norsk',
            'nb-Norwegian Bokmål - norsk bokmål',
            'nn-Norwegian Nynorsk - nynorsk',
            'oc-Occitan',
            'or-Oriya - ଓଡ଼ିଆ',
            'om-Oromo - Oromoo',
            'ps-Pashto - پښتو',
            'fa-Persian - فارسی',
            'pl-Polish - polski',
            'pt-Portuguese - português',
            'pt-BR-Portuguese (Brazil) - português (Brasil)',
            'pt-PT-Portuguese (Portugal) - português (Portugal)',
            'pa-Punjabi - ਪੰਜਾਬੀ',
            'qu-Quechua',
            'ro-Romanian - română',
            'mo-Romanian (Moldova) - română (Moldova)',
            'rm-Romansh - rumantsch',
            'ru-Russian - русский',
            'gd-Scottish Gaelic',
            'sr-Serbian - српски',
            'sh-Serbo-Croatian - Srpskohrvatski',
            'sn-Shona - chiShona',
            'sd-Sindhi',
            'si-Sinhala - සිංහල',
            'sk-Slovak - slovenčina',
            'sl-Slovenian - slovenščina',
            'so-Somali - Soomaali',
            'st-Southern Sotho',
            'es-Spanish - español',
            'es-AR-Spanish (Argentina) - español (Argentina)',
            'es-419-Spanish (Latin America) - español (Latinoamérica)
            ',
            'es-MX-Spanish (Mexico) - español (México)',
            'es-ES-Spanish (Spain) - español (España)',
            'es-US-Spanish (United States) - español (Estados Unidos)
            ',
            'su-Sundanese',
            'sw-Swahili - Kiswahili',
            'sv-Swedish - svenska',
            'tg-Tajik - тоҷикӣ',
            'ta-Tamil - தமிழ்',
            'tt-Tatar',
            'te-Telugu - తెలుగు',
            'th-Thai - ไทย',
            'ti-Tigrinya - ትግርኛ',
            'to-Tongan - lea fakatonga',
            'tr-Turkish - Türkçe',
            'tk-Turkmen',
            'tw-Twi',
            'uk-Ukrainian - українська',
            'ur-Urdu - اردو',
            'ug-Uyghur',
            'uz-Uzbek - o‘zbek',
            'vi-Vietnamese - Tiếng Việt',
            'wa-Walloon - wa',
            'cy-Welsh - Cymraeg',
            'fy-Western Frisian',
            'xh-Xhosa',
            'yi-Yiddish',
            'yo-Yoruba - Èdè Yorùbá',
            'zu-Zulu - isiZulu',
        );

        foreach ($locales as $locale) {
            $locale_region = explode('-',$locale);
            if (strtoupper($country_code) == $locale_region[1]) {
                return $locale_region[0];
            }
        }

        return "en";
    }
    // function getLanguageCode(string $country_code): string
    // {
    //     $locales = array('af-ZA',
    //         'am-ET',
    //         'ar-AE',
    //         'ar-BH',
    //         'ar-DZ',
    //         'ar-EG',
    //         'ar-IQ',
    //         'ar-JO',
    //         'ar-KW',
    //         'ar-LB',
    //         'ar-LY',
    //         'ar-MA',
    //         'ar-OM',
    //         'ar-QA',
    //         'ar-SA',
    //         'ar-SY',
    //         'ar-TN',
    //         'ar-YE',
    //         'az-Cyrl-AZ',
    //         'az-Latn-AZ',
    //         'be-BY',
    //         'bg-BG',
    //         'bn-BD',
    //         'bs-Cyrl-BA',
    //         'bs-Latn-BA',
    //         'cs-CZ',
    //         'da-DK',
    //         'de-AT',
    //         'de-CH',
    //         'de-DE',
    //         'de-LI',
    //         'de-LU',
    //         'dv-MV',
    //         'el-GR',
    //         'en-AU',
    //         'en-BZ',
    //         'en-CA',
    //         'en-GB',
    //         'en-IE',
    //         'en-JM',
    //         'en-MY',
    //         'en-NZ',
    //         'en-SG',
    //         'en-TT',
    //         'en-US',
    //         'en-ZA',
    //         'en-ZW',
    //         'es-AR',
    //         'es-BO',
    //         'es-CL',
    //         'es-CO',
    //         'es-CR',
    //         'es-DO',
    //         'es-EC',
    //         'es-ES',
    //         'es-GT',
    //         'es-HN',
    //         'es-MX',
    //         'es-NI',
    //         'es-PA',
    //         'es-PE',
    //         'es-PR',
    //         'es-PY',
    //         'es-SV',
    //         'es-US',
    //         'es-UY',
    //         'es-VE',
    //         'et-EE',
    //         'fa-IR',
    //         'fi-FI',
    //         'fil-PH',
    //         'fo-FO',
    //         'fr-BE',
    //         'fr-CA',
    //         'fr-CH',
    //         'fr-FR',
    //         'fr-LU',
    //         'fr-MC',
    //         'he-IL',
    //         'hi-IN',
    //         'hr-BA',
    //         'hr-HR',
    //         'hu-HU',
    //         'hy-AM',
    //         'id-ID',
    //         'ig-NG',
    //         'is-IS',
    //         'it-CH',
    //         'it-IT',
    //         'ja-JP',
    //         'ka-GE',
    //         'kk-KZ',
    //         'kl-GL',
    //         'km-KH',
    //         'ko-KR',
    //         'ky-KG',
    //         'lb-LU',
    //         'lo-LA',
    //         'lt-LT',
    //         'lv-LV',
    //         'mi-NZ',
    //         'mk-MK',
    //         'mn-MN',
    //         'ms-BN',
    //         'ms-MY',
    //         'mt-MT',
    //         'nb-NO',
    //         'ne-NP',
    //         'nl-BE',
    //         'nl-NL',
    //         'pl-PL',
    //         'prs-AF',
    //         'ps-AF',
    //         'pt-BR',
    //         'pt-PT',
    //         'ro-RO',
    //         'ru-RU',
    //         'rw-RW',
    //         'sv-SE',
    //         'si-LK',
    //         'sk-SK',
    //         'sl-SI',
    //         'sq-AL',
    //         'sr-Cyrl-BA',
    //         'sr-Cyrl-CS',
    //         'sr-Cyrl-ME',
    //         'sr-Cyrl-RS',
    //         'sr-Latn-BA',
    //         'sr-Latn-CS',
    //         'sr-Latn-ME',
    //         'sr-Latn-RS',
    //         'sw-KE',
    //         'tg-Cyrl-TJ',
    //         'th-TH',
    //         'tk-TM',
    //         'tr-TR',
    //         'uk-UA',
    //         'ur-PK',
    //         'uz-Cyrl-UZ',
    //         'uz-Latn-UZ',
    //         'vi-VN',
    //         'wo-SN',
    //         'yo-NG',
    //         'zh-CN',
    //         'zh-HK',
    //         'zh-MO',
    //         'zh-SG',
    //         'zh-TW');

    //     foreach ($locales as $locale) {
    //         $locale_region = explode('-',$locale);
    //         if (strtoupper($country_code) == $locale_region[1]) {
    //             return $locale_region[0];
    //         }
    //     }

    //     return "en";
    // }

    public static function pagination_limit()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        if ($pagination_limit != null) {
            return $pagination_limit->value;
        } else {
            return 25;
        }
    }

    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('key', 'system_language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }


    public static function product_tax($price , $tax, $is_include=false){
        $price_tax = ($price * $tax) / (100 + ($is_include?$tax:0)) ;
        return $price_tax;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\ItemCampaign;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use App\Models\Translation;

class CampaignController extends Controller
{
    function index($type)
    {
        return view('admin-views.campaign.'.$type.'.index');
    }

    function list($type)
    {
        if($type=='basic')
        {
            $campaigns=Campaign::with('module')->where('module_id', Config::get('module.current_module_id'))->latest()->paginate(config('default_pagination'));
        }
        else{
            $campaigns=ItemCampaign::where('module_id', Config::get('module.current_module_id'))->latest()->paginate(config('default_pagination'));
        }
        
        return view('admin-views.campaign.'.$type.'.list', compact('campaigns'));
    }

    public function storeBasic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:campaigns|max:191',
            'description'=>'max:1000',
            'image' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign = new Campaign;
        $campaign->title = $request->title[array_search('en', $request->lang)];
        $campaign->description = $request->description[array_search('en', $request->lang)];
        $campaign->image = Helpers::upload('campaign/', 'png', $request->file('image'));
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->module_id = Config::get('module.current_module_id');
        $campaign->save();
        
        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->title[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\Campaign',
                    'translationable_id' => $campaign->id,
                    'locale' => $key,
                    'key' => 'title',
                    'value' => $request->title[$index],
                ));
            }
            if ($request->description[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\Campaign',
                    'translationable_id' => $campaign->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                ));
            }
        }

        Translation::insert($data);

        return response()->json([], 200);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
            'description' => 'max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        
        $campaign->title = $request->title[array_search('en', $request->lang)];
        $campaign->description = $request->description[array_search('en', $request->lang)];
        $campaign->image = $request->has('image') ? Helpers::update('campaign/', $campaign->image, 'png', $request->file('image')) : $campaign->image;;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->save();

        foreach ($request->lang as $index => $key) {
            if ($request->title[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\Campaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'title'],
                    ['value' => $request->title[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\Campaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'description'],
                    ['value' => $request->description[$index]]
                );
            }
        }

        return response()->json([], 200);
    }
    
    public function storeItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191|unique:item_campaigns',
            'image' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric|between:0.01,999999999999.99',
            'store_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required',
            'start_date' => 'required',
            'veg' => 'required',
            'description'=>'max:1000'
        ], [
            'category_id.required' => translate('messages.select_category'),
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign = new ItemCampaign;

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $campaign->category_ids = json_encode($category);
        $campaign->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $campaign->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        // food variation
        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {

                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                $temp_variation['required'] = $option['required'] ?? 'off';
                if ($option['min'] > 0 &&  $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_value = [];

                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value, $temp_option);
                }
                $temp_variation['values'] = $temp_value;
                array_push($food_variations, $temp_variation);
            }
        }

        $campaign->admin_id = auth('admin')->id();
        $campaign->title = $request->title[array_search('en', $request->lang)];
        $campaign->description = $request->description[array_search('en', $request->lang)];
        $campaign->image = Helpers::upload('campaign/', 'png', $request->file('image'));
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->variations = json_encode($variations);
        $campaign->food_variations = json_encode($food_variations);
        $campaign->price = $request->price;
        $campaign->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $campaign->discount_type = $request->discount_type;
        $campaign->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $campaign->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $campaign->store_id = $request->store_id;
        $campaign->veg = $request->veg;
        $campaign->module_id= Config::get('module.current_module_id');
        $campaign->stock= $request->current_stock;
        $campaign->unit_id = $request->unit;
        $campaign->save();
        
        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->title[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\ItemCampaign',
                    'translationable_id' => $campaign->id,
                    'locale' => $key,
                    'key' => 'title',
                    'value' => $request->title[$index],
                ));
            }
            if ($request->description[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\ItemCampaign',
                    'translationable_id' => $campaign->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                ));
            }
        }
        Translation::insert($data);

        return response()->json([], 200);
    }

    public function updateItem(ItemCampaign $campaign, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|array',
            'category_id' => 'required',
            'price' => 'required|numeric|between:0.01,999999999999.99',
            'veg' => 'required',
            'description.*'=>'max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $campaign->category_ids = json_encode($category);
        $campaign->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $campaign->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {
                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                if ($option['min'] > 0 &&  $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_variation['required'] = $option['required'] ?? 'off';
                $temp_value = [];
                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value, $temp_option);
                }
                $temp_variation['values'] = $temp_value;
                array_push($food_variations, $temp_variation);
            }
        }

        $campaign->title = $request->title[array_search('en', $request->lang)];
        $campaign->description = $request->description[array_search('en', $request->lang)];
        $campaign->image = $request->has('image') ? Helpers::update('campaign/', $campaign->image, 'png', $request->file('image')) : $campaign->image;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->variations = json_encode($variations);
        $campaign->food_variations = json_encode($food_variations);
        $campaign->price = $request->price;
        $campaign->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $campaign->discount_type = $request->discount_type;
        $campaign->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $campaign->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $campaign->veg = $request->veg;
        $campaign->unit_id = $request->unit;
        $campaign->stock= $request->current_stock;
        $campaign->save();

        foreach ($request->lang as $index => $key) {
            if ($request->title[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\ItemCampaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'title'],
                    ['value' => $request->title[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\ItemCampaign',
                        'translationable_id' => $campaign->id,
                        'locale' => $key,
                        'key' => 'description'],
                    ['value' => $request->description[$index]]
                );
            }
        }

        return response()->json([], 200);
    }

    public function edit($type, $campaign)
    {
        if($type=='basic')
        {
            $campaign = Campaign::withoutGlobalScope('translate')->findOrFail($campaign);
            return view('admin-views.campaign.'.$type.'.edit', compact('campaign'));
        }
        else
        {
            $campaign = ItemCampaign::withoutGlobalScope('translate')->findOrFail($campaign);
            $temp = $campaign->category;
            if($temp->position)
            {
                $sub_category = $temp;
                $category = $temp->parent;
            }
            else
            {
                $category = $temp;
                $sub_category = null;
            }
            return view('admin-views.campaign.'.$type.'.edit', compact('campaign','sub_category','category'));
        }
        
    }

    public function view($type, $campaign)
    {   
        if($type=='basic')
        {
            $campaign = Campaign::findOrFail($campaign);
            $stores = $campaign->stores()->paginate(config('default_pagination'));
            $store_ids = []; 
            foreach($campaign->stores as $store)
            {
                $store_ids[] = $store->id;
            }
            return view('admin-views.campaign.basic.view', compact('campaign', 'stores', 'store_ids'));
        }
        else
        {
            $campaign = ItemCampaign::findOrFail($campaign);
        }
        return view('admin-views.campaign.item.view', compact('campaign'));
        
    }

    public function status($type, $id, $status)
    {
        if($type=='item')
        {
            $campaign = ItemCampaign::findOrFail($id);
        }
        else{
            $campaign = Campaign::findOrFail($id);
        }
        $campaign->status = $status;
        $campaign->save();
        Toastr::success(translate('messages.campaign_status_updated'));
        return back();
    }

    public function delete(Campaign $campaign)
    {
        if (Storage::disk('public')->exists('campaign/' . $campaign->image)) {
            Storage::disk('public')->delete('campaign/' . $campaign->image);
        }
        $campaign->translations()->delete();
        $campaign->delete();
        Toastr::success(translate('messages.campaign_deleted_successfully'));
        return back();
    }
    public function delete_item(ItemCampaign $campaign)
    {
        if (Storage::disk('public')->exists('campaign/' . $campaign->image)) {
            Storage::disk('public')->delete('campaign/' . $campaign->image);
        }
        $campaign->translations()->delete();
        $campaign->delete();
        Toastr::success(translate('messages.campaign_deleted_successfully'));
        return back();
    }

    public function remove_store(Campaign $campaign, $store)
    {
        $campaign->stores()->detach($store);
        $campaign->save();
        Toastr::success(translate('messages.store_remove_from_campaign'));
        return back();
    }
    public function addstore(Request $request, Campaign $campaign)
    {
        $campaign->stores()->attach($request->store_id);
        $campaign->save();
        Toastr::success(translate('messages.store_added_to_campaign'));
        return back();
    }

    public function searchBasic(Request $request){
        $key = explode(' ', $request['search']);
        $campaigns=Campaign::where('module_id', Config::get('module.current_module_id'))->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.campaign.basic.partials._table',compact('campaigns'))->render(),
            'count'=>$campaigns->count()
        ]);
    }
    public function searchItem(Request $request){
        $key = explode(' ', $request['search']);
        $campaigns=ItemCampaign::where('module_id', Config::get('module.current_module_id'))->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.campaign.item.partials._table',compact('campaigns'))->render()
        ]);
    }
}
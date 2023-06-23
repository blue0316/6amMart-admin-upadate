<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Translation;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use App\Models\Tag;

class ItemController extends Controller
{

    public function store(Request $request)
    {
        if(!$request->vendor->stores[0]->item_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>translate('messages.permission_denied')]
                ]
            ],403);
        }
        
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'image' => 'required',
            'price' => 'required|numeric|min:0.01',
            'discount' => 'required|numeric|min:0',
            'translations'=>'required',
        ], [
            'category_id.required' => translate('messages.category_required'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        $data = json_decode($request->translations, true);

        if (count($data) < 1) {
            $validator->getMessageBag()->add('translations', translate('messages.Name and description in english is required'));
        }

        if ($request['price'] <= $dis || count($data) < 1 || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 402);
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if(isset($tags)){
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids,$tag->id);
            }
        }
        
        $item = new Item;
        $item->name = $data[0]['value'];

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
        $item->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $item->category_ids = json_encode($category);
        $item->description = $data[1]['value'];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach (json_decode($request->choice_no) as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $i['name'] = 'choice_' . $no;
                $i['title'] = json_decode($request->choice)[$key];
                $i['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', json_decode($request[$str]))));
                array_push($choice_options, $i);
            }
        }
        $item->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach (json_decode($request->choice_no) as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', json_decode($request[$name]));
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $i) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $i);
                    } else {
                        $str .= str_replace(' ', '', $i);
                    }
                }
                $i = [];
                $i['type'] = $str;
                $i['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $i['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $i);
            }
        }

        $images = [];
        if (!empty($request->file('item_images'))) {
            foreach ($request->item_images as $img) {
                $image_name = Helpers::upload('product/', 'png', $img);
                array_push($images, $image_name);
            }
        }

        //combinations end
        $item->variations = json_encode($variations);

        $food_variations = [];
        if(isset($request->options))
        {
            foreach(json_decode($request->options, true) as $option)
            {
                $temp_variation['name']= $option['name'];
                $temp_variation['type']= $option['type'];
                $temp_variation['min']= $option['min'] ?? 0;
                $temp_variation['max']= $option['max'] ?? 0;
                $temp_variation['required']= $option['required']??'off';
                $temp_value = [];
                foreach($option['values'] as $value)
                {
                    if(isset($value['label'])){
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value,$temp_option);
                }
                $temp_variation['values']= $temp_value;
                array_push($food_variations,$temp_variation);
            }
        }
        //combinations end
        $item->food_variations = json_encode($food_variations);
        $item->price = $request->price;
        $item->image = Helpers::upload('product/', 'png', $request->file('image'));
        $item->available_time_starts = $request->available_time_starts;
        $item->available_time_ends = $request->available_time_ends;
        $item->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $item->discount_type = $request->discount_type;
        $item->attributes = $request->has('attribute_id') ? $request->attribute_id : json_encode([]);
        $item->add_ons = $request->has('addon_ids') ? json_encode(explode(',',$request->addon_ids)) : json_encode([]);
        $item->store_id = $request['vendor']->stores[0]->id;
        $item->veg = $request->veg;
        $item->module_id = $request['vendor']->stores[0]->module_id;
        $item->stock= $request->current_stock;
        $item->images = $images;
        $item->unit_id = $request->unit;
        $item->save();
        $item->tags()->sync($tag_ids);

        unset($data[1]);        
        unset($data[0]);        
        foreach ($data as $key=>$i) {
            $data[$key]['translationable_type'] = 'App\Models\Item';
            $data[$key]['translationable_id'] = $item->id;
        }
        Translation::insert($data);

        return response()->json(['message'=>translate('messages.product_added_successfully')], 200);
    }

    public function status(Request $request)
    {
        if(!$request->vendor->stores[0]->item_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>translate('messages.permission_denied')]
                ]
            ],403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $product = Item::find($request->id);
        $product->status = $request->status;
        $product->save();

        return response()->json(['message' => translate('messages.product_status_updated')], 200);
    }

    public function update(Request $request)
    {
        if(!$request->vendor->stores[0]->item_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>translate('messages.permission_denied')]
                ]
            ],403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric|min:0.01',
            'discount' => 'required|numeric|min:0',
            
        ], [
            'category_id.required' => translate('messages.category_required'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }
        $data = json_decode($request->translations, true);

        if (count($data) < 1) {
            $validator->getMessageBag()->add('translations', translate('messages.Name and description in english is required'));
        }

        if ($request['price'] <= $dis || count($data) < 1 || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 402);
        }
        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if(isset($tags)){
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids,$tag->id);
            }
        }

        $p = Item::findOrFail($request->id);

        $p->name = $data[0]['value'];

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

        $p->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $p->category_ids = json_encode($category);
        $p->description = $data[1]['value'];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach (json_decode($request->choice_no) as $key => $no) {
                $str = 'choice_options_' . $no;
                if (json_decode($request[$str])[0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = json_decode($request->choice)[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', json_decode($request[$str]))));
                array_push($choice_options, $item);
            }
        }
        $p->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach (json_decode($request->choice_no) as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', json_decode($request[$name]));
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $i) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $i);
                    } else {
                        $str .= str_replace(' ', '', $i);
                    }
                }
                $i = [];
                $i['type'] = $str;
                $i['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $i['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $i);
            }
        }
        //combinations end

        $images = $p['images'];

        foreach ($p['images'] as $img) {
            if (!in_array($img, json_decode($request->images, true))) {
                if(Storage::disk('public')->exists('product/' . $img))
                {
                    Storage::disk('public')->delete('product/' . $img);
                }
                $key = array_search($img, $images);
                unset($images[$key]);
            }
        }            
        if ($request->has('item_images')){
            foreach ($request->item_images as $img) {
                $image = Helpers::upload('product/', 'png', $img);
                array_push($images, $image);
            }
        } 

        $p->variations = json_encode($variations);

        $food_variations = [];
        if(isset($request->options))
        {
            foreach(json_decode($request->options,true) as $key=>$option)
            {
                $temp_variation['name']= $option['name'];
                $temp_variation['type']= $option['type'];
                $temp_variation['min']= $option['min'] ?? 0;
                $temp_variation['max']= $option['max'] ?? 0;
                $temp_variation['required']= $option['required']??'off';
                $temp_value = [];
                foreach($option['values'] as $value)
                {
                    if(isset($value['label'])){
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value,$temp_option);
                }
                $temp_variation['values']= $temp_value;
                array_push($food_variations,$temp_variation);
            }
        }
        $p->food_variations = json_encode($food_variations);
        $p->price = $request->price;
        $p->image = $request->has('image') ? Helpers::update('product/', $p->image, 'png', $request->file('image')) : $p->image;
        $p->available_time_starts = $request->available_time_starts;
        $p->available_time_ends = $request->available_time_ends;
        $p->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->attributes = $request->has('attribute_id') ? $request->attribute_id : json_encode([]);
        $p->add_ons = $request->has('addon_ids') ? json_encode(explode(',',$request->addon_ids)) : json_encode([]);
        $p->stock= $request->current_stock??0;
        $p->veg = $request->veg??0;
        $p->images = array_values($images);
        $p->unit_id = $request->unit;
        $p->save();
        $p->tags()->sync($tag_ids);

        unset($data[1]);        
        unset($data[0]);   
        foreach ($data as $key=>$item) {
            Translation::updateOrInsert(
                ['translationable_type' => 'App\Models\Item',
                    'translationable_id' => $p->id,
                    'locale' => $item['locale'],
                    'key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return response()->json(['message'=>translate('messages.product_updated_successfully')], 200);
    }

    public function delete(Request $request)
    {
        if(!$request->vendor->stores[0]->item_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>translate('messages.permission_denied')]
                ]
            ],403);
        }
        $product = Item::findOrFail($request->id);

        if($product->image)
        {
            if (Storage::disk('public')->exists('product/' . $product['image'])) {
                Storage::disk('public')->delete('product/' . $product['image']);
            }
        }
        $product->translations()->delete();
        $product->delete();

        return response()->json(['message'=>translate('messages.product_deleted_successfully')], 200);
    }

    public function search(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $key = explode(' ', $request['name']);

        $products = Item::active()
        ->with(['rating'])
        ->where('store_id', $request['vendor']->stores[0]->id)
        ->when($request->category_id, function($query)use($request){
            $query->whereHas('category',function($q)use($request){
                return $q->whereId($request->category_id)->orWhere('parent_id', $request->category_id);
            });
        })
        ->when($request->store_id, function($query) use($request){
            return $query->where('store_id', $request->store_id);
        })
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
            $q->orWhereHas('tags',function($query)use($key){
                $query->where(function($q)use($key){
                    foreach ($key as $value) {
                        $q->where('tag', 'like', "%{$value}%");
                    };
                });
            });
        })
        ->limit(50)
        ->get();

        $data = Helpers::product_data_formatting($products, true, false, app()->getLocale());
        return response()->json($data, 200);
    }

    public function reviews(Request $request)
    {
        $id = $request['vendor']->stores[0]->id;;

        $reviews = Review::with(['customer', 'item'])
        ->whereHas('item', function($query)use($id){
            return $query->where('store_id', $id);
        })
        ->latest()->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            $item['item_name'] = null;
            $item['item_image'] = null;
            $item['customer_name'] = null;
            if($item->item)
            {
                $item['item_name'] = $item->item->name;
                $item['item_image'] = $item->item->image;
                if(count($item->item->translations)>0)
                {
                    $translate = array_column($item->item->translations->toArray(), 'value', 'key');
                    $item['item_name'] = $translate['name'];
                }
            }
            
            if($item->customer)
            {
                $item['customer_name'] = $item->customer->f_name.' '.$item->customer->l_name;
            }
            
            unset($item['item']);
            unset($item['customer']);
            array_push($storage, $item);
        }

        return response()->json($storage, 200);
    }
}

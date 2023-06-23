<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;
use App\Models\Store;
use App\Models\Review;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Models\ItemCampaign;
use App\Models\Tag;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Scopes\StoreScope;
use App\Models\Translation;
use Illuminate\Support\Facades\Config;

class ItemController extends Controller
{
    public function index()
    {
        $categories = Category::where(['position' => 0])->get();
        return view('admin-views.product.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'image' => 'required',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'discount' => 'required|numeric|min:0',
            'store_id' => 'required',
            'description.*' => 'max:1000',
        ], [
            'description.*.max' => translate('messages.description_length_warning'),
            'name.0.required' => translate('messages.item_name_required'),
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

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
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
        $item->name = $request->name[array_search('en', $request->lang)];

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
        $item->category_ids = json_encode($category);
        $item->category_id = $request->sub_category_id ? $request->sub_category_id : $request->category_id;
        $item->description =  $request->description[array_search('en', $request->lang)];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp['name'] = 'choice_' . $no;
                $temp['title'] = $request->choice[$key];
                $temp['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $temp);
            }
        }
        $item->choice_options = json_encode($choice_options);
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
                foreach ($combination as $k => $temp) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $temp);
                    } else {
                        $str .= str_replace(' ', '', $temp);
                    }
                }
                $temp = [];
                $temp['type'] = $str;
                $temp['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $temp['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $temp);
            }
        }
        //combinations end

        $img_names = [];
        $images = [];
        if (!empty($request->file('item_images'))) {
            foreach ($request->item_images as $img) {
                $image_name = Helpers::upload('product/', 'png', $img);
                array_push($img_names, $image_name);
            }
            $images = $img_names;
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

        $item->food_variations = json_encode($food_variations);
        $item->variations = json_encode($variations);
        $item->price = $request->price;
        $item->image = Helpers::upload('product/', 'png', $request->file('image'));
        $item->available_time_starts = $request->available_time_starts ?? '00:00:00';
        $item->available_time_ends = $request->available_time_ends ?? '23:59:59';
        $item->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $item->discount_type = $request->discount_type;
        $item->unit_id = $request->unit;
        $item->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $item->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $item->store_id = $request->store_id;
        $item->veg = $request->veg;
        $item->module_id = Config::get('module.current_module_id');
        $item->stock = $request->current_stock ?? 0;
        $item->images = $images;
        $item->save();
        $item->tags()->sync($tag_ids);

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\Item',
                    'translationable_id' => $item->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                ));
            }
            if ($request->description[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\Item',
                    'translationable_id' => $item->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                ));
            }
        }
        Translation::insert($data);

        return response()->json([], 200);
    }

    public function view($id)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->where(['id' => $id])->first();
        $reviews = Review::where(['item_id' => $id])->latest()->paginate(config('default_pagination'));
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    public function edit($id)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->with('store', 'category', 'module')->findOrFail($id);
        if (!$product) {
            Toastr::error(translate('messages.item') . ' ' . translate('messages.not_found'));
            return back();
        }
        $temp = $product->category;
        if ($temp->position) {
            $sub_category = $temp;
            $category = $temp->parent;
        } else {
            $category = $temp;
            $sub_category = null;
        }

        return view('admin-views.product.edit', compact('product', 'sub_category', 'category'));
    }

    public function status(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->findOrFail($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success(translate('messages.item_status_updated'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'array',
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'store_id' => 'required',
            'description' => 'array',
            'description.*' => 'max:1000',
            'discount' => 'required|numeric|min:0',
        ], [
            'description.*.max' => translate('messages.description_length_warning'),
            'name.0.required' => translate('messages.item_name_required'),
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

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
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

        $item = Item::withoutGlobalScope(StoreScope::class)->find($id);

        $item->name = $request->name[array_search('en', $request->lang)];

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

        $item->category_id = $request->sub_category_id ? $request->sub_category_id : $request->category_id;
        $item->category_ids = json_encode($category);
        $item->description =  $request->description[array_search('en', $request->lang)];

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', translate('messages.attribute_choice_option_value_can_not_be_null'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp['name'] = 'choice_' . $no;
                $temp['title'] = $request->choice[$key];
                $temp['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $temp);
            }
        }
        $item->choice_options = $request->has('attribute_id') ? json_encode($choice_options) : json_encode([]);
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
                foreach ($combination as $k => $temp) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $temp);
                    } else {
                        $str .= str_replace(' ', '', $temp);
                    }
                }
                $temp = [];
                $temp['type'] = $str;
                $temp['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $temp['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $temp);
            }
        }
        //combinations end
        $images = $item['images'];
        if ($request->has('item_images')) {
            foreach ($request->item_images as $img) {
                $image = Helpers::upload('product/', 'png', $img);
                array_push($images, $image);
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

        $item->food_variations = json_encode($food_variations);
        $item->variations = $request->has('attribute_id') ? json_encode($variations) : json_encode([]);
        $item->price = $request->price;
        $item->image = $request->has('image') ? Helpers::update('product/', $item->image, 'png', $request->file('image')) : $item->image;
        $item->available_time_starts = $request->available_time_starts ?? '00:00:00';
        $item->available_time_ends = $request->available_time_ends ?? '23:59:59';

        $item->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $item->discount_type = $request->discount_type;
        $item->unit_id = $request->unit;
        $item->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $item->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $item->store_id = $request->store_id;
        // $item->module_id= $request->module_id;
        $item->stock = $request->current_stock ?? 0;
        $item->veg = $request->veg;
        $item->images = $images;
        $item->save();
        $item->tags()->sync($tag_ids);

        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Models\Item',
                        'translationable_id' => $item->id,
                        'locale' => $key,
                        'key' => 'name'
                    ],
                    ['value' => $request->name[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Models\Item',
                        'translationable_id' => $item->id,
                        'locale' => $key,
                        'key' => 'description'
                    ],
                    ['value' => $request->description[$index]]
                );
            }
        }

        return response()->json([], 200);
    }

    public function delete(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->find($request->id);

        if ($product->image) {
            if (Storage::disk('public')->exists('product/' . $product['image'])) {
                Storage::disk('public')->delete('product/' . $product['image']);
            }
        }
        $product->translations()->delete();
        $product->delete();
        Toastr::success(translate('messages.product_deleted_successfully'));
        return back();
    }

    public function variant_combination(Request $request)
    {
        $options = [];
        $price = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        $stock = $request->stock == 'true' ? true : false;
        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'product_name', 'stock'))->render(),
            'length' => count($combinations),
            'stock' => $stock,
        ]);
    }

    public function variant_price(Request $request)
    {
        if ($request->item_type == 'item') {
            $product = Item::withoutGlobalScope(StoreScope::class)->find($request->id);
        } else {
            $product = ItemCampaign::find($request->id);
        }
        // $product = Item::withoutGlobalScope(StoreScope::class)->find($request->id);
        if (isset($product->module_id) && $product->module->module_type == 'food' && $product->food_variations) {
            $price = $product->price;
            $addon_price = 0;
            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                }
            }
            $product_variations = json_decode($product->food_variations, true);
            if ($request->variations && count($product_variations)) {

                $price += Helpers::food_variation_price($product_variations, $request->variations);
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price, $product->store);
            }
        } else {
            $str = '';
            $quantity = 0;
            $price = 0;
            $addon_price = 0;

            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request[$choice->name]);
                } else {
                    $str .= str_replace(' ', '', $request[$choice->name]);
                }
            }

            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                }
            }

            if ($str != null) {
                $count = count(json_decode($product->variations));
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variations)[$i]->type == $str) {
                        $price = json_decode($product->variations)[$i]->price - Helpers::product_discount_calculate($product, json_decode($product->variations)[$i]->price, $product->store);
                    }
                }
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price, $product->store);
            }
        }

        return array('price' => Helpers::format_currency(($price * $request->quantity) + $addon_price));
    }
    public function get_categories(Request $request)
    {
        $cat = Category::when(isset($request->module_id), function ($query) use ($request) {
            $query->where('module_id', $request->module_id);
        })
            ->when($request->sub_category, function ($query) {
                $query->where('position', '>', '0');
            })
            ->where(['parent_id' => $request->parent_id])->get([DB::raw('id, name as text')]);

        return response()->json($cat);
    }

    public function get_items(Request $request)
    {
        $items = Item::withoutGlobalScope(StoreScope::class)->with('store')
            ->when($request->zone_id, function ($q) use ($request) {
                $q->whereHas('store', function ($query) use ($request) {
                    $query->where('zone_id', $request->zone_id);
                });
            })
            ->when($request->module_id, function ($q) use ($request) {
                $q->where('module_id', $request->module_id);
            })->get();
        $res = '';
        if (count($items) > 0 && !$request->data) {
            $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        }

        foreach ($items as $row) {
            $res .= '<option value="' . $row->id . '" ';
            if ($request->data) {
                $res .= in_array($row->id, $request->data) ? 'selected ' : '';
            }
            $res .= '>' . $row->name . ' (' . $row->store->name . ')' . '</option>';
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    public function list(Request $request)
    {
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $type = $request->query('type', 'all');
        $items = Item::withoutGlobalScope(StoreScope::class)
            ->when($request->query('module_id', null), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->module(Config::get('module.current_module_id'))
            ->type($type)
            ->latest()->paginate(config('default_pagination'));
        $store = $store_id != 'all' ? Store::findOrFail($store_id) : null;
        $category = $category_id != 'all' ? Category::findOrFail($category_id) : null;
        return view('admin-views.product.list', compact('items', 'store', 'category', 'type'));
    }

    public function remove_image(Request $request)
    {
        if (Storage::disk('public')->exists('product/' . $request['name'])) {
            Storage::disk('public')->delete('product/' . $request['name']);
        }
        $item = Item::withoutGlobalScope(StoreScope::class)->find($request['id']);
        $array = [];
        if (count($item['images']) < 2) {
            Toastr::warning(translate('all_image_delete_warning'));
            return back();
        }
        foreach ($item['images'] as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Item::withoutGlobalScope(StoreScope::class)->where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success(translate('item_image_removed_successfully'));
        return back();
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $items = Item::withoutGlobalScope(StoreScope::class)->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->module(Config::get('module.current_module_id'))->limit(50)->get();
        return response()->json([
            'count' => count($items),
            'view' => view('admin-views.product.partials._table', compact('items'))->render()
        ]);
    }

    public function review_list(Request $request)
    {
        $reviews = Review::with(['item'=>function($query){
            $query->withOutGlobalScope(StoreScope::class);
        }, 'customer'])->whereHas('item', function ($q) use ($request) {
            return $q->where('module_id', Config::get('module.current_module_id'))->withOutGlobalScope(StoreScope::class);
        })->latest()->paginate(config('default_pagination'));   
        return view('admin-views.product.reviews-list', compact('reviews'));
    }

    public function reviews_status(Request $request)
    {
        $review = Review::find($request->id);
        $review->status = $request->status;
        $review->save();
        Toastr::success(translate('messages.review_visibility_updated'));
        return back();
    }

    public function review_search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $reviews = Review::with('item')->whereHas('item', function ($query) use ($key) {
            foreach ($key as $value) {
                $query->where('name', 'like', "%{$value}%");
            }
        })->whereHas('item', function ($q) use ($request) {
            return $q->where('module_id', Config::get('module.current_module_id'))->withoutGlobalScope(StoreScope::class);
        })->limit(50)->get();
        return response()->json([
            'count' => count($reviews),
            'view' => view('admin-views.product.partials._review-table', compact('reviews'))->render()
        ]);
    }

    public function bulk_import_index()
    {
        return view('admin-views.product.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }

        $data = [];
        foreach ($collections as $collection) {
            if ($collection['name'] === "" || $collection['category_id'] === "" || $collection['sub_category_id'] === "" || $collection['price'] === "" || $collection['store_id'] === "" || $collection['module_id'] === "") {
                Toastr::error(translate('messages.please_fill_all_required_fields'));
                return back();
            }


            array_push($data, [
                'name' => $collection['name'],
                'category_id' => $collection['sub_category_id'] ? $collection['sub_category_id'] : $collection['category_id'],
                'category_ids' => json_encode([['id' => $collection['category_id'], 'position' => 0], ['id' => $collection['sub_category_id'], 'position' => 1]]),
                'veg' => $collection['veg'] ?? 0,  //$request->item_type;
                'price' => $collection['price'],
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'description' => $collection['description'],
                'available_time_starts' => $collection['available_time_starts'] ?? '00:00:00',
                'available_time_ends' => $collection['available_time_ends'] ?? '23:59:59',
                'unit_id' => is_int($collection['unit_id']) ? $collection['unit_id'] : null,
                'image' => $collection['image'],
                'images' => $collection['images'] ?? json_encode([]),
                'store_id' => $collection['store_id'],
                'module_id' => $collection['module_id'],
                'stock' => is_numeric($collection['stock']) ? abs($collection['stock']) : 0,
                'add_ons' => $collection['add_ons'] ?? json_encode([]),
                'attributes' => $collection['attributes'] ??  json_encode([]),
                'choice_options' => $collection['choice_options'] ?? json_encode([]),
                'variations' => $collection['variations'] ?? json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        try {
            DB::beginTransaction();
            DB::table('items')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Toastr::error(translate('messages.failed_to_import_data'));
            return back();
        }

        Toastr::success(translate('messages.product_imported_successfully', ['count' => count($data)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.product.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'start_id' => 'required_if:type,id_wise',
            'end_id' => 'required_if:type,id_wise',
            'from_date' => 'required_if:type,date_wise',
            'to_date' => 'required_if:type,date_wise'
        ]);
        $products = Item::when($request['type'] == 'date_wise', function ($query) use ($request) {
            $query->whereBetween('created_at', [$request['from_date'] . ' 00:00:00', $request['to_date'] . ' 23:59:59']);
        })
            ->when($request['type'] == 'id_wise', function ($query) use ($request) {
                $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
            })
            ->module(Config::get('module.current_module_id'))
            ->withoutGlobalScope(StoreScope::class)->get();
        return (new FastExcel(ProductLogic::format_export_items($products)))->download('Items.xlsx');
    }

    public function get_variations(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->find($request['id']);

        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    public function stock_update(Request $request)
    {
        $variations = [];
        $stock_count = $request['current_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }


        $product = Item::withoutGlobalScope(StoreScope::class)->find($request['product_id']);

        $product->stock = $stock_count ?? 0;
        $product->variations = json_encode($variations);
        $product->save();
        Toastr::success(translate("messages.product_updated_successfully"));
        return back();
    }

    public function search_vendor(Request $request)
    {
        $key = explode(' ', $request['search']);
        if ($request->has('store_id')) {

            $foods = Item::withoutGlobalScope(StoreScope::class)
                ->where('store_id', $request->store_id)
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                })->limit(50)->get();
            return response()->json([
                'count' => count($foods),
                'view' => view('admin-views.vendor.view.partials._product', compact('foods'))->render()
            ]);
        }
        $foods = Item::withoutGlobalScope(StoreScope::class)->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'count' => count($foods),
            'view' => view('admin-views.vendor.view.partials._product', compact('foods'))->render()
        ]);
    }

    public function store_item_export($type, $store_id)
    {
        $item = Item::withoutGlobalScope(StoreScope::class)->with('category')->where('store_id', $store_id)->get();
        if ($type == 'excel') {
            return (new FastExcel(Helpers::export_store_item($item)))->download('Items.xlsx');
        } elseif ($type == 'csv') {
            return (new FastExcel(Helpers::export_store_item($item)))->download('Items.csv');
        }
    }

    public function export(Request $request, $types)
    {
        $store_id = $request->query('store_id', 'all');
        $category_id = $request->query('category_id', 'all');
        $type = $request->query('type', 'all');
        $item = Item::withoutGlobalScope(StoreScope::class)
            ->when($request->query('module_id', null), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(is_numeric($store_id), function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->when(is_numeric($category_id), function ($query) use ($category_id) {
                return $query->whereHas('category', function ($q) use ($category_id) {
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->module(Config::get('module.current_module_id'))
            ->with('category', 'store')
            ->type($type)->latest()->get();
        if ($types == 'excel') {
            return (new FastExcel(Helpers::export_items($item)))->download('Items.xlsx');
        } elseif ($types == 'csv') {
            return (new FastExcel(Helpers::export_items($item)))->download('Items.csv');
        }
    }

    public function search_store(Request $request, $store_id)
    {
        $key = explode(' ', $request['search']);
        $foods = Item::withoutGlobalScope(StoreScope::class)
            ->where('store_id', $store_id)
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->limit(50)->get();
        return response()->json([
            'count' => count($foods),
            'view' => view('admin-views.vendor.view.partials._product', compact('foods'))->render()
        ]);
    }
}

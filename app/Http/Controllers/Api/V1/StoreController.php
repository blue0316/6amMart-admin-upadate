<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\StoreLogic;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function get_stores(Request $request, $filter_data="all")
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_stores( $zone_id, $filter_data, $type, $request['limit'], $request['offset'], $request->query('featured'),$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores, 200);
    }

    public function get_latest_stores(Request $request, $filter_data="all")
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $type = $request->query('type', 'all');

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_latest_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores['stores'], 200);
    }

    public function get_popular_stores(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_popular_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores['stores'], 200);
    }

    public function get_details($id)
    {
        $store = StoreLogic::get_store_details($id);
        if($store)
        {
            $category_ids = DB::table('items')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->selectRaw('IF((categories.position = "0"), categories.id, categories.parent_id) as categories')
            ->where('items.store_id', $id)
            ->where('categories.status',1)
            ->groupBy('categories')
            ->get();
            // dd($category_ids->pluck('categories'));
            $store = Helpers::store_data_formatting($store);
            $store['category_ids'] = array_map('intval', $category_ids->pluck('categories')->toArray());
        }
        return response()->json($store, 200);
    }

    public function get_searched_stores(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $type = $request->query('type', 'all');

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::search_stores($request['name'], $zone_id, $request->category_id,$request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);
        return response()->json($stores, 200);
    }

    public function reviews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $id = $request['store_id'];


        $reviews = Review::with(['customer', 'item'])
        ->whereHas('item', function($query)use($id){
            return $query->where('store_id', $id);
        })
        ->active()->latest()->get();

        $storage = [];
        foreach ($reviews as $temp) {
            $temp['attachment'] = json_decode($temp['attachment']);
            $temp['item_name'] = null;
            $temp['item_image'] = null;
            $temp['customer_name'] = null;
            if($temp->item)
            {
                $temp['item_name'] = $temp->item->name;
                $temp['item_image'] = $temp->item->image;
                if(count($temp->item->translations)>0)
                {
                    $translate = array_column($temp->item->translations->toArray(), 'value', 'key');
                    $temp['item_name'] = $translate['name'];
                }
            }
            if($temp->customer)
            {
                $temp['customer_name'] = $temp->customer->f_name.' '.$temp->customer->l_name;
            }

            unset($temp['item']);
            unset($temp['customer']);
            array_push($storage, $temp);
        }

        return response()->json($storage, 200);
    }
}

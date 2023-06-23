<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Scopes\StoreScope;
use App\Models\Translation;

class AddOnController extends Controller
{
    public function list(Request $request)
    {
        $vendor = $request['vendor'];

        $addons = AddOn::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->with('translations')->where('store_id', $vendor->stores[0]->id)->latest()->get();

        return response()->json(Helpers::addon_data_formatting($addons, true, true, app()->getLocale()),200);
    }

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
            'name' => 'required',
            'price' => 'required|numeric',
            'translations' => 'array'
        ]);

        $data = $request->translations;

        if (count($data) < 1) {
            $validator->getMessageBag()->add('translations', translate('messages.Name and description in english is required'));
        }

        if ($validator->fails() || count($data) < 1 ) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];


        $addon = new AddOn();
        $addon->name = $data[0]['value'];
        $addon->price = $request->price;
        $addon->store_id = $vendor->stores[0]->id;
        $addon->save();

        foreach ($data as $key=>$item) {
            Translation::updateOrInsert(
                ['translationable_type' => 'App\Models\AddOn',
                    'translationable_id' => $addon->id,
                    'locale' => $item['locale'],
                    'key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return response()->json(['message' => translate('messages.addon_added_successfully')], 200);
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
            'name' => 'required',
            'price' => 'required',
            'translations' => 'array'
        ]);

        $data = $request->translations;

        if (count($data) < 1) {
            $validator->getMessageBag()->add('translations', translate('messages.Name and description in english is required'));
        }

        if ($validator->fails() || count($data) < 1 ) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $addon = AddOn::withoutGlobalScope(StoreScope::class)->find($request->id);
        $addon->name = $data[0]['value'];;
        $addon->price = $request->price;
        $addon->save();

        foreach ($data as $key=>$item) {
            Translation::updateOrInsert(
                ['translationable_type' => 'App\Models\AddOn',
                    'translationable_id' => $addon->id,
                    'locale' => $item['locale'],
                    'key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return response()->json(['message' => translate('messages.addon_updated_successfully')], 200);
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
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $addon = AddOn::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->findOrFail($request->id);
        $addon->translations()->delete();
        $addon->delete();

        return response()->json(['message' => translate('messages.addon_deleted_successfully')], 200);
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

        $addon_data = AddOn::withoutGlobalScope(StoreScope::class)->findOrFail($request->id);
        $addon_data->status = $request->status;
        $addon_data->save();

        return response()->json(['message' => translate('messages.addon_status_updated')], 200);
    }

    public function search(Request $request){

        $vendor = $request['vendor'];
        $limit = $request['limite']??25;
        $offset = $request['offset']??1;

        $key = explode(' ', $request['search']);
        $addons=AddOn::withoutGlobalScope(StoreScope::class)->whereHas('store',function($query)use($vendor){
            return $query->where('vendor_id', $vendor['id']);
        })->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->orderBy('name')->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total_size' => $addons->total(),
            'limit' => $limit,
            'offset' => $offset,
            'addons' => $addons->items()
        ];

        return response()->json([$data],200);
    }
}

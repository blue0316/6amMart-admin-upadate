<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Store;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Scopes\StoreScope;
use Illuminate\Support\Facades\Config;

class AddOnController extends Controller
{
    public function index(Request $request)
    {
        $store_id = $request->query('store_id', 'all');
        $addons = AddOn::withoutGlobalScope(StoreScope::class)
        ->when(is_numeric($store_id), function($query)use($store_id){
            return $query->where('store_id', $store_id);
        })->whereHas('store', function ($q) use ($request) {
            return $q->where('module_id', Config::get('module.current_module_id'));
        })
        ->orderBy('name')->paginate(config('default_pagination'));
        $store =$store_id !='all'? Store::findOrFail($store_id):null;
        return view('admin-views.addon.index', compact('addons', 'store'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.*' => 'max:191',
            'name'=>'array|required',
            'store_id' => 'required',
            'price' => 'required|numeric|between:0,999999999999.99',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'store_id.required' => translate('messages.please_select_store'),
        ]);

        $addon = new AddOn();
        $addon->name = $request->name[array_search('en', $request->lang)];
        $addon->price = $request->price;
        $addon->store_id = $request->store_id;
        $addon->save();

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                array_push($data, Array(
                    'translationable_type'  => 'App\Models\AddOn',
                    'translationable_id'    => $addon->id,
                    'locale'                => $key,
                    'key'                   => 'name',
                    'value'                 => $request->name[$index],
                ));
            }
        }
        if(count($data))
        {
            Translation::insert($data);
        }
        Toastr::success(translate('messages.addon_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $addon = AddOn::withoutGlobalScope(StoreScope::class)->withoutGlobalScope('translate')->findOrFail($id);
        return view('admin-views.addon.edit', compact('addon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:191',
            'store_id' => 'required',
            'price' => 'required|numeric|between:0,999999999999.99',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'store_id.required' => translate('messages.please_select_store'),
        ]);

        $addon = AddOn::withoutGlobalScope(StoreScope::class)->find($id);
        $addon->name = $request->name[array_search('en', $request->lang)];
        $addon->price = $request->price;
        $addon->store_id = $request->store_id;
        $addon->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\AddOn',
                        'translationable_id'    => $addon->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }

        Toastr::success(translate('messages.addon_updated_successfully'));
        return redirect(route('admin.addon.add-new'));
    }

    public function delete(Request $request)
    {
        $addon = AddOn::withoutGlobalScope(StoreScope::class)->find($request->id);
        $addon->delete();
        Toastr::success(translate('messages.addon_deleted_successfully'));
        return back();
    }

    public function status($addon, Request $request)
    {
        $addon_data = AddOn::withoutGlobalScope(StoreScope::class)->find($addon);
        $addon_data->status = $request->status;
        $addon_data->save();
        Toastr::success(translate('messages.addon_status_updated'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $addons=AddOn::whereHas('store', function ($q) use ($request) {
            return $q->where('module_id', Config::get('module.current_module_id'));
        })->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.addon.partials._table',compact('addons'))->render()
        ]);
    }
    public function bulk_import_index()
    {
        return view('admin-views.addon.bulk-import');
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
                if ($collection['name'] === "" || !is_numeric($collection['store_id'])) {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));
                    return back();
                }


            array_push($data, [
                'name' => $collection['name'],
                'price' => $collection['price'],
                'store_id' => $collection['store_id'],
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
        }
        DB::table('add_ons')->insert($data);
        Toastr::success(translate('messages.addon_imported_successfully', ['count'=>count($data)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.addon.bulk-export');
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
        $addons = AddOn::when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })->whereHas('store', function ($q) use ($request) {
            return $q->where('module_id', Config::get('module.current_module_id'));
        })
        ->withoutGlobalScope(StoreScope::class)->get();
        return (new FastExcel($addons))->download('Addons.xlsx');
    }
}

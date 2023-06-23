<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ParcelCategory;
use App\Models\Translation;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ParcelCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $module_id = Config::get('module.current_module_id');
        $parcel_categories = ParcelCategory::
        when($module_id, function($query)use($module_id){
            $query->Module($module_id);
        })
        ->orderBy('name')->paginate(config('default_pagination'));
        return view('admin-views.parcel.category.index',compact('parcel_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|array',
            'name.0'=>'unique:parcel_categories,name',
            'name.*'=>'max:191|unique:parcel_categories,name',
            'image'=>'required|image',
            'description'=>'required|array',
            'description.0'=>'required',
            'parcel_per_km_shipping_charge'=>'required_with:parcel_minimum_shipping_charge',
            'parcel_minimum_shipping_charge'=>'required_with:parcel_per_km_shipping_charge'
        ]);

        $parcel_category = new ParcelCategory;
        $parcel_category->module_id = Config::get('module.current_module_id');
        $parcel_category->name = $request->name[array_search('en', $request->lang)];
        $parcel_category->description =  $request->description[array_search('en', $request->lang)];
        $parcel_category->image = Helpers::upload('parcel_category/', 'png', $request->file('image'));
        $parcel_category->parcel_per_km_shipping_charge = $request->parcel_per_km_shipping_charge;
        $parcel_category->parcel_minimum_shipping_charge = $request->parcel_minimum_shipping_charge;
        $parcel_category->save();

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\ParcelCategory',
                    'translationable_id' => $parcel_category->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                ));
            }
            if ($request->description[$index] && $key != 'en') {
                array_push($data, array(
                    'translationable_type' => 'App\Models\ParcelCategory',
                    'translationable_id' => $parcel_category->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                ));
            }
        }
        Translation::insert($data);

        Toastr::success(translate('messages.parcel_category_added_successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parcel_category= ParcelCategory::withoutGlobalScope('translate')->findOrFail($id);
        return view('admin-views.parcel.category.edit',compact('parcel_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required|array',
            'name.0'=>'unique:parcel_categories,name,'.$id,
            'name.*'=>'max:191',
            'description'=>'required|array',
            'parcel_per_km_shipping_charge'=>'required_with:parcel_minimum_shipping_charge',
            'parcel_minimum_shipping_charge'=>'required_with:parcel_per_km_shipping_charge'
        ]);

        $parcel_category = ParcelCategory::findOrFail($id);
        // $parcel_category->module_id = $request->module_id;
        $parcel_category->name = $request->name[array_search('en', $request->lang)];
        $parcel_category->description =  $request->description[array_search('en', $request->lang)];
        $parcel_category->image = Helpers::update('parcel_category/', $parcel_category->image, 'png', $request->file('image'));
        $parcel_category->parcel_per_km_shipping_charge = $request->parcel_per_km_shipping_charge;
        $parcel_category->parcel_minimum_shipping_charge = $request->parcel_minimum_shipping_charge;
        $parcel_category->save();

        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\ParcelCategory',
                        'translationable_id' => $parcel_category->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\ParcelCategory',
                        'translationable_id' => $parcel_category->id,
                        'locale' => $key,
                        'key' => 'description'],
                    ['value' => $request->description[$index]]
                );
            }
        }
        Toastr::success(translate('messages.parcel_category_updated_successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $parcel_category = ParcelCategory::findOrFail($id);
        if($parcel_category->image)
        {
            if (Storage::disk('public')->exists('parcel_category/' . $parcel_category['image'])) {
                Storage::disk('public')->delete('parcel_category/' . $parcel_category['image']);
            }
        }
        $parcel_category->translations()->delete();
        $parcel_category->delete();
        Toastr::success(translate('messages.parcel_category_deleted_successfully'));
        return back();
    }

    public function status(Request $request)
    {
        $parcel_category = ParcelCategory::findOrFail($request->id);
        $parcel_category->status = $request->status;
        $parcel_category->save();
        Toastr::success(translate('messages.parcel_category_status_updated'));
        return back();
    }
}

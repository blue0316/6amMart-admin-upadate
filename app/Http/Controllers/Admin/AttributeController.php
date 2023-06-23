<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    function index()
    {
        $attributes = Attribute::orderBy('name')->paginate(config('default_pagination'));
        return view('admin-views.attribute.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:attributes|max:100',
        ], [
            'name.required' => translate('messages.Name is required!'),
        ]);

        $attribute = new Attribute;
        $attribute->name = $request->name;
        $attribute->save();

        Toastr::success(translate('messages.attribute_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin-views.attribute.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100|unique:attributes,name,'.$id,
        ], [
            'name.required' => translate('messages.Name is required!'),
        ]);

        $attribute = Attribute::findOrFail($id);
        $attribute->name = $request->name;
        $attribute->save();
        Toastr::success(translate('messages.attribute_updated_successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $attribute = Attribute::findOrFail($request->id);
        $attribute->delete();
        Toastr::success(translate('messages.attribute_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $attributes=Attribute::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.attribute.partials._table',compact('attributes'))->render(),
            'count'=>$attributes->count()

        ]);
    }

    public function bulk_import_index()
    {
        return view('admin-views.attribute.bulk-import');
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
        $skip = ['youtube_video_url'];
        foreach ($collections as $collection) {
                if ($collection['name'] === "" ) {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));
                    return back();
                }


            array_push($data, [
                'name' => $collection['name'],
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
        }
        DB::table('attributes')->insert($data);
        Toastr::success(translate('messages.attribute_imported_successfully',['count'=>count($data)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.attribute.bulk-export');
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
        $attributes = Attribute::when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })
        ->get();
        return (new FastExcel($attributes))->download('Attributes.xlsx');
    }

    public function export_attributes($type){
        $collection = Attribute::all();

        if($type == 'excel'){
            return (new FastExcel(Helpers::export_attributes($collection)))->download('Attributes.xlsx');
        }elseif($type == 'csv'){
            return (new FastExcel(Helpers::export_attributes($collection)))->download('Attributes.csv');
        }
    }
}

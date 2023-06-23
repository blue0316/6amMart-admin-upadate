<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class CategoryController extends Controller
{
    function index()
    {
        $categories=Category::where(['position'=>0])->module(Helpers::get_store_data()->module_id)->latest()->paginate(config('default_pagination'));
        return view('vendor-views.category.index',compact('categories'));
    }

    public function get_all(Request $request){
        $data = Category::where('name', 'like', '%'.$request->q.'%')->module(Helpers::get_store_data()->module_id)->limit(8)->get([DB::raw('id, CONCAT(name, " (", if(position = 0, "'.translate('messages.main').'", "'.translate('messages.sub').'"),")") as text')]);
        if(isset($request->all))
        {
            $data[]=(object)['id'=>'all', 'text'=>'All'];
        }
        return response()->json($data);
    }

    function sub_index()
    {
        $categories=Category::with(['parent'])
        ->whereHas('parent',function($query){
            $query->module(Helpers::get_store_data()->module_id);
        })
        ->where(['position'=>1])->latest()->paginate(config('default_pagination'));
        return view('vendor-views.category.sub-index',compact('categories'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $categories=Category::where(['position'=>0])
        ->module(Helpers::get_store_data()->module_id)
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })
        ->latest()
        ->limit(50)->get();
        return response()->json([
            'view'=>view('vendor-views.category.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
    }

    public function sub_search(Request $request){
        $key = explode(' ', $request['search']);
        $categories=Category::with(['parent'])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->where(['position'=>1])->limit(50)->get();

        return response()->json([
            'view'=>view('vendor-views.category.partials._sub_table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
    }

    public function export_categories($type){
        $categories=Category::with('module')->where(['position'=>0])->module(Helpers::get_store_data()->module_id)->get();

        if($type == 'excel'){
            return (new FastExcel(Helpers::export_categories($categories)))->download('Categories.xlsx');
        }elseif($type == 'csv'){
            return (new FastExcel(Helpers::export_categories($categories)))->download('Categories.csv');
        }
    }

    public function export_sub_categories($type){
        $categories=Category::with(['parent'])
        ->whereHas('parent',function($query){
            $query->module(Helpers::get_store_data()->module_id);
        })
        ->where(['position'=>1])->get();

        if($type == 'excel'){
            return (new FastExcel(Helpers::export_sub_categories($categories)))->download('Categories.xlsx');
        }elseif($type == 'csv'){
            return (new FastExcel(Helpers::export_sub_categories($categories)))->download('Categories.csv');
        }
    }
}

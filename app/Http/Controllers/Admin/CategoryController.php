<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Models\Translation;
use Illuminate\Support\Facades\Config;

class CategoryController extends Controller
{
    function index(Request $request)
    {
        $categories=Category::with('module')->where(['position'=>0])->module(Config::get('module.current_module_id'))->latest()->paginate(config('default_pagination'));
        return view('admin-views.category.index',compact('categories'));
    }

    function sub_index()
    {
        $categories=Category::with(['parent'])->where(['position'=>1])->module(Config::get('module.current_module_id'))->latest()->paginate(config('default_pagination'));
        return view('admin-views.category.sub-index',compact('categories'));
    }

    function sub_sub_index()
    {
        return view('admin-views.category.sub-sub-index');
    }

    function sub_category_index()
    {
        return view('admin-views.category.index');
    }

    function sub_sub_category_index()
    {
        return view('admin-views.category.index');
    }

    function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
        ], [
            'name.required' => translate('messages.Name is required!'),
        ]);

        $category = new Category();
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->image = Helpers::upload('category/', 'png', $request->file('image'));
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->position = $request->position;
        $category->module_id = isset($request->parent_id)?Category::where('id', $request->parent_id)->first('module_id')->module_id:Config::get('module.current_module_id');
        $category->save();

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                array_push($data, Array(
                    'translationable_type'  => 'App\Models\Category',
                    'translationable_id'    => $category->id,
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

        Toastr::success(translate('messages.category_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $category = Category::withoutGlobalScope('translate')->findOrFail($id);
        return view('admin-views.category.edit', compact('category'));
    }

    public function status(Request $request)
    {
        $category = Category::find($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success(translate('messages.category_status_updated'));
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);
        $category = Category::find($id);

        $category->name = $request->name[array_search('en', $request->lang)];
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, 'png', $request->file('image')) : $category->image;
        $category->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\Category',
                        'translationable_id'    => $category->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }
        Toastr::success(translate('messages.category_updated_successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $category = Category::findOrFail($request->id);
        if ($category->childes->count()==0){
            $category->delete();
            Toastr::success('Category removed!');
        }else{
            Toastr::warning(translate('messages.remove_sub_categories_first'));
        }
        return back();
    }

    public function get_all(Request $request){
        $data = Category::where('name', 'like', '%'.$request->q.'%')
        ->when($request->module_id, function($query)use($request){
            $query->where('module_id', $request->module_id);
        })
        ->limit(8)->get([DB::raw('id, CONCAT(name, " (", if(position = 0, "'.translate('messages.main').'", "'.translate('messages.sub').'"),")") as text')]);
        if(isset($request->all))
        {
            $data[]=(object)['id'=>'all', 'text'=>'All'];
        }
        return response()->json($data);
    }

    public function update_priority(Category $category, Request $request)
    {
        $priority = $request->priority??0;
        $category->priority = $priority;
        $category->save();
        Toastr::success(translate('messages.category_priority_updated successfully'));
        return back();

    }

    public function bulk_import_index()
    {
        return view('admin-views.category.bulk-import');
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
            if ($collection['name'] === "" || !is_numeric($collection['module_id'])) {

                Toastr::error(translate('messages.please_fill_all_required_fields'));
                return back();
            }
            $parent_id = is_numeric($collection['parent_id'])?$collection['parent_id']:0;
            array_push($data, [
                'name' => $collection['name'],
                'image' => $collection['image'],
                'parent_id' => $parent_id,
                'module_id' => $collection['module_id'],
                'position' => $collection['position'],
                'priority'=>is_numeric($collection['priority']) ? $collection['priority']:0,
                'status' => empty($collection['status'])?1:$collection['status'],
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
        }
        DB::table('categories')->insert($data);
        Toastr::success(translate('messages.category_imported_successfully', ['count'=>count($data)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.category.bulk-export');
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
        $categories = Category::when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })->module(Config::get('module.current_module_id'))
        ->get();
        return (new FastExcel($categories))->download('Categories.xlsx');
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $categories=Category::when($request->sub_category, function($query){
            return $query->where('position','1');
        })->module(Config::get('module.current_module_id'))
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        if($request->sub_category)
        {
            return response()->json([
                'view'=>view('admin-views.category.partials._sub_category_table',compact('categories'))->render(),
                'count'=>$categories->count()
            ]);
        }
        return response()->json([
            'view'=>view('admin-views.category.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
    }

    public function export_categories(Request $request, $type){
        $collection = Category::with('module')->where(['position'=>0])->module(Config::get('module.current_module_id'))
        ->latest()->get();

        if($type == 'excel'){
            return (new FastExcel(Helpers::export_categories($collection)))->download('Categories.xlsx');
        }elseif($type == 'csv'){
            return (new FastExcel(Helpers::export_categories($collection)))->download('Categories.csv');
        }
    }
}

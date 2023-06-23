<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\EmployeeRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\Helpers;
use Illuminate\Validation\Rule; 

class CustomRoleController extends Controller
{
    public function create()
    {
        $rl=EmployeeRole::where('store_id',Helpers::get_store_id())->orderBy('name')->paginate(config('default_pagination'));
        return view('vendor-views.custom-role.create',compact('rl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modules'=>'required|array|min:1',
            'name' => [
                'required','string',Rule::unique('employee_roles')->where(function($query) {
                  $query->where('store_id', Helpers::get_store_id());
              })
            ],
        ],[
            'name.required'=>translate('messages.Role name is required!'),
            'modules.required'=>translate('messages.Please select atleast one module')
        ]);
        DB::table('employee_roles')->insert([
            'name'=>$request->name,
            'modules'=>json_encode($request['modules']),
            'status'=>1,
            'store_id'=>Helpers::get_store_id(),
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

        Toastr::success(translate('messages.role_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $role=EmployeeRole::where('store_id',Helpers::get_store_id())->where(['id'=>$id])->first(['id','name','modules']);
        return view('vendor-views.custom-role.edit',compact('role'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'modules'=>'required|array|min:1',
            'name' => [
                'required','string',Rule::unique('employee_roles')->where(function($query)use($id) {
                  $query->where('store_id', Helpers::get_store_id())->where('id','<>', $id);
              })
            ]
        ],[
            'name.required'=>translate('messages.Role name is required!'),
            'name.unique'=>translate('messages.Role name already taken!'),
            'modules.required'=>translate('messages.Please select atleast one module')
        ]);

        DB::table('employee_roles')->where('store_id',Helpers::get_store_id())->where(['id'=>$id])->update([
            'name'=>$request->name,
            'modules'=>json_encode($request['modules']),
            'status'=>1,
            'store_id'=>Helpers::get_store_id(),
            'updated_at'=>now()
        ]);

        Toastr::success(translate('messages.role_updated_successfully'));
        return redirect()->route('vendor.custom-role.create');
    }

    public function distroy($id)
    {
        $role=EmployeeRole::where('store_id',Helpers::get_store_id())->where(['id'=>$id])->delete();
        Toastr::success(translate('messages.role_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $rl=EmployeeRole::where('store_id',Helpers::get_store_id())->
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->orderBy('name')->limit(50)->get();
        return response()->json([
            'view'=>view('vendor-views.custom-role.partials._table',compact('rl'))->render()
        ]);
    }
}

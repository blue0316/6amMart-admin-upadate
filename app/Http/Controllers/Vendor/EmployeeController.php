<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\VendorEmployee;
use App\Models\EmployeeRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class EmployeeController extends Controller
{

    public function add_new()
    {
        $rls = EmployeeRole::where('store_id',Helpers::get_store_id())->get();
        return view('vendor-views.employee.add-new', compact('rls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'nullable|max:100',
            'role_id' => 'required',
            'image' => 'required',
            'email' => 'required|unique:vendor_employees',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:20|unique:vendor_employees',
            'password' => 'required|min:8',
        ]);

        DB::table('vendor_employees')->insert([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'employee_role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'vendor_id'=> Helpers::get_vendor_id(),
            'store_id'=>Helpers::get_store_id(),
            'image' => Helpers::upload('vendor/', 'png', $request->file('image')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success('Employee added successfully!');
        return redirect()->route('vendor.employee.list');
    }

    function list()
    {
        $em = VendorEmployee::where('store_id', Helpers::get_store_id())->with(['role'])->latest()->paginate(config('default_pagination'));
        return view('vendor-views.employee.list', compact('em'));
    }

    public function edit($id)
    {
        $e = VendorEmployee::where('store_id', Helpers::get_store_id())->where(['id' => $id])->first();
        $rls = EmployeeRole::where('store_id',Helpers::get_store_id())->get();
        return view('vendor-views.employee.edit', compact('rls', 'e'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'nullable|max:100',
            'role_id' => 'required',
            'email' => 'required|unique:vendor_employees,email,'.$id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:20|unique:vendor_employees,phone,'.$id,
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
        ]);

        $e = VendorEmployee::where('store_id', Helpers::get_store_id())->find($id);
        if ($request['password'] == null) {
            $pass = $e['password'];
        } else {
            if (strlen($request['password']) < 7) {
                Toastr::warning(translate('messages.password_length_warning',['length'=>'8']));
                return back();
            }
            $pass = bcrypt($request['password']);
            $e->remember_token=null;
        }

        if ($request->has('image')) {
            $e['image'] = Helpers::update('vendor/', $e->image, 'png', $request->file('image'));
        }
            $e->f_name = $request->f_name;
            $e->l_name = $request->l_name;
            $e->phone = $request->phone;
            $e->email = $request->email;
            $e->employee_role_id = $request->role_id;
            $e->vendor_id = Helpers::get_vendor_id();
            $e->store_id =Helpers::get_store_id();
            $e->password = $pass;
            $e->image = $e['image'];
            $e->updated_at = now();
            $e->is_logged_in = 0;
            $e->save();

        Toastr::success('Employee updated successfully!');
        return redirect()->route('vendor.employee.list');
    }

    public function distroy($id)
    {
        $role=VendorEmployee::where('store_id', Helpers::get_store_id())->where(['id'=>$id])->delete();
        Toastr::info(translate('messages.employee_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $employees=VendorEmployee::where('store_id', Helpers::get_store_id())->
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%");
                $q->orWhere('l_name', 'like', "%{$value}%");
                $q->orWhere('phone', 'like', "%{$value}%");
                $q->orWhere('email', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('vendor-views.employee.partials._table',compact('employees'))->render(),
            'count'=>$employees->count()
        ]);
    }

    public function list_export(Request $request){
        $em = VendorEmployee::where('store_id', Helpers::get_store_id())->with(['role'])->get();
        if($request->type == 'excel'){
            return (new FastExcel($em))->download('Employee.xlsx');
        }elseif($request->type == 'csv'){
            return (new FastExcel($em))->download('Employee.csv');
        }
    }
}

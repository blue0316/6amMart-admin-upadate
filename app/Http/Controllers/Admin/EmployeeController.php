<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{

    public function add_new()
    {
        $rls = AdminRole::whereNotIn('id', [1])->get();
        return view('admin-views.employee.add-new', compact('rls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'nullable|max:100',
            'role_id' => 'required',
            'image' => 'required',
            'email' => 'required|unique:admins',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:admins',
            'password' =>'required|min:8'

        ]);

        if ($request->role_id == 1) {
            Toastr::warning(translate('messages.access_denied'));
            return back();
        }

        DB::table('admins')->insert([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'zone_id' => $request->zone_id,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'image' => Helpers::upload('admin/', 'png', $request->file('image')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('messages.employee_added_successfully'));
        return redirect()->route('admin.users.employee.list');
    }

    function list()
    {
        $em = Admin::zone()->with(['role'])->where('role_id', '!=','1')->latest()->paginate(config('default_pagination'));
        return view('admin-views.employee.list', compact('em'));
    }

    public function edit($id)
    {
        $e = Admin::zone()->where('role_id', '!=','1')->where(['id' => $id])->first();
        $rls = AdminRole::whereNotIn('id', [1])->get();
        return view('admin-views.employee.edit', compact('rls', 'e'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'role_id' => 'required',
            'email' => 'required|unique:admins,email,'.$id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:admins,phone,'.$id,
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
        ]);

        if ($request->role_id == 1) {
            Toastr::warning(translate('messages.access_denied'));
            return back();
        }

        $e = Admin::where('role_id','!=',1)->findOrFail($id);
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
            $e['image'] = Helpers::update('admin/', $e->image, 'png', $request->file('image'));
        }


        $e->f_name = $request->f_name;
        $e->l_name = $request->l_name;
        $e->phone = $request->phone;
        $e->zone_id = $request->zone_id;
        $e->email = $request->email;
        $e->role_id = $request->role_id;
        $e->password = $pass;
        $e->image = $e['image'];
        $e->updated_at = now();
        $e->is_logged_in = 0;
        $e->save();

        Toastr::success(translate('messages.employee_updated_successfully'));
        return redirect()->route('admin.users.employee.list');
    }

    public function distroy($id)
    {
        $role=Admin::zone()->where('role_id', '!=','1')->where(['id'=>$id])->delete();
        Toastr::info(translate('messages.employee_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $employees=Admin::zone()->where('role_id', '!=','1')
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%");
                $q->orWhere('l_name', 'like', "%{$value}%");
                $q->orWhere('phone', 'like', "%{$value}%");
                $q->orWhere('email', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.employee.partials._table',compact('employees'))->render(),
            'count'=>$employees->count()
        ]);
    }
}

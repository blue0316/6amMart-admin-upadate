<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{

    public function store_data()
    {
        $new_order = Order::StoreOrder()->where(['checked' => 0])->count();
        $new_parcel_order = Order::ParcelOrder()->where(['checked' => 0])->count();
        return response()->json([
            'success' => 1,

            'data' => ['new_order' => $new_order > 0 ? $new_order : $new_parcel_order, 'type' => $new_order > 0 ? 'store_order' : 'parcel_order',]
        ]);
    }

    public function settings()
    {
        return view('admin-views.settings');
    }

    public function settings_update(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:admins,email,' . auth('admin')->id(),
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:admins,phone,' . auth('admin')->id(),
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'l_name.required' => translate('messages.Last name is required!'),
        ]);

        $admin = Admin::find(auth('admin')->id());

        if ($request->has('image')) {
            $image_name = Helpers::update('admin/', $admin->image, 'png', $request->file('image'));
        } else {
            $image_name = $admin['image'];
        }


        $admin->f_name = $request->f_name;
        $admin->l_name = $request->l_name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->image = $image_name;
        $admin->save();
        Toastr::success(translate('messages.admin_updated_successfully'));
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        $admin = Admin::find(auth('admin')->id());
        $admin->password = bcrypt($request['password']);
        $admin->save();
        Toastr::success(translate('messages.admin_password_updated_successfully'));
        return back();
    }

    public function maintenance_mode()
    {
        $maintenance_mode = BusinessSetting::where('key', 'maintenance_mode')->first();
        if (isset($maintenance_mode) == false) {
            DB::table('business_settings')->insert([
                'key' => 'maintenance_mode',
                'value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('business_settings')->where(['key' => 'maintenance_mode'])->update([
                'key' => 'maintenance_mode',
                'value' => $maintenance_mode->value == 1 ? 0 : 1,
                'updated_at' => now(),
            ]);
        }

        if (isset($maintenance_mode) && $maintenance_mode->value) {
            return response()->json(['message' => translate('Maintenance is off.')]);
        }
        return response()->json(['message' => translate('Maintenance is on.')]);
    }
}

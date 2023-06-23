<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Zone;
use App\Models\Store;
use App\CentralLogics\StoreLogic;
use App\Models\VendorEmployee;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Mail;

class VendorLoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor_type= $request->vendor_type;

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if($vendor_type == 'owner'){
            if (auth('vendor')->attempt($data)) {
                $token = $this->genarate_token($request['email']);
                $vendor = Vendor::where(['email' => $request['email']])->first();
                if(!$vendor->stores[0]->status)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'auth-002', 'message' => translate('messages.inactive_vendor_warning')]
                        ]
                    ], 403);
                }
                $vendor->auth_token = $token;
                $vendor->save();
                return response()->json(['token' => $token, 'zone_wise_topic'=> $vendor->stores[0]->zone->store_wise_topic], 200);
            }  else {
                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }
        }elseif($vendor_type == 'employee'){

            if (auth('vendor_employee')->attempt($data)) {
                $token = $this->genarate_token($request['email']);
                $vendor = VendorEmployee::where(['email' => $request['email']])->first();
                if($vendor->store->status == 0)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'auth-002', 'message' => translate('messages.inactive_vendor_warning')]
                        ]
                    ], 403);
                }
                $vendor->auth_token = $token;
                $vendor->save();
                $role = $vendor->role ? json_decode($vendor->role->modules):[];
                return response()->json(['token' => $token, 'zone_wise_topic'=> $vendor->store->zone->store_wise_topic, 'role'=>$role], 200);
            } else {
                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }

    }

    private function genarate_token($email)
    {
        $token = Str::random(120);
        $is_available = Vendor::where('auth_token', $token)->where('email', '!=', $email)->count();
        if($is_available)
        {
            $this->genarate_token($email);
        }
        return $token;
    }

    public function register(Request $request)
    {
        $status = BusinessSetting::where('key', 'toggle_store_registration')->first();
        if(!isset($status) || $status->value == '0')
        {
            return response()->json(['errors' => Helpers::error_formater('self-registration', translate('messages.store_self_registration_disabled'))]);
        }

        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'name' => 'required|max:191',
            'address' => 'required|max:1000',
            'latitude' => 'required',
            'longitude' => 'required',
            'email' => 'required|unique:vendors',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors',
            'minimum_delivery_time' => 'required',
            'maximum_delivery_time' => 'required',
            'delivery_time_type'=>'required',
            'password' => 'required|min:6',
            'zone_id' => 'required',
            'module_id' => 'required',
            'logo' => 'required',
            'tax' => 'required'
        ]);

        if($request->zone_id)
        {
            $point = new Point($request->latitude, $request->longitude);
            $zone = Zone::contains('coordinates', $point)->where('id', $request->zone_id)->first();
            if(!$zone){
                $validator->getMessageBag()->add('latitude', translate('messages.coordinates_out_of_zone'));
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
        }
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor = new Vendor();
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = bcrypt($request->password);
        $vendor->status = null;
        $vendor->save();

        $store = new Store;
        $store->name = $request->name;
        $store->phone = $request->phone;
        $store->email = $request->email;
        $store->logo = Helpers::upload('store/', 'png', $request->file('logo'));
        $store->cover_photo = Helpers::upload('store/cover/', 'png', $request->file('cover_photo'));
        $store->address = $request->address;
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->vendor_id = $vendor->id;
        $store->zone_id = $request->zone_id;
        $store->tax = $request->tax;
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->module_id = $request->module_id;
        $store->status = 0;
        $store->save();
        $store->module->increment('stores_count');
        if(config('module.'.$store->module->module_type)['always_open'])
        {
            StoreLogic::insert_schedule($store->id);
        }

        try{
            if(config('mail.status')){
                Mail::to($request['email'])->send(new \App\Mail\SelfRegistration('pending', $vendor->f_name.' '.$vendor->l_name));
            }
        }catch(\Exception $ex){
            info($ex);
        }

        return response()->json(['message'=>translate('messages.application_placed_successfully')],200);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeliveryManLoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = [
            'phone' => $request->phone,
            'password' => $request->password
        ];

        if (auth('delivery_men')->attempt($data)) {
            $token = Str::random(120);

            if(auth('delivery_men')->user()->application_status != 'approved')
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'auth-003', 'message' => translate('messages.your_application_is_not_approved_yet')]
                    ]
                ], 401);
            }
            else if(!auth('delivery_men')->user()->status)
            {
                $errors = [];
                array_push($errors, ['code' => 'auth-003', 'message' => translate('messages.your_account_has_been_suspended')]);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }

            $delivery_man =  DeliveryMan::where(['phone' => $request['phone']])->first();
            $delivery_man->auth_token = $token;
            $delivery_man->save();

            return response()->json(['token' => $token, 'zone_wise_topic'=>$delivery_man->type=='zone_wise'?$delivery_man->zone->deliveryman_wise_topic:'restaurant_dm_'.$delivery_man->store_id], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'identity_type' => 'required|in:passport,driving_license,nid',
            'identity_number' => 'required',
            'email' => 'required|unique:delivery_men',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:delivery_men',
            'password'=>'required|min:6',
            'zone_id' => 'required',
            'earning' => 'required'
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'zone_id.required' => translate('messages.select_a_zone'),
            'earning.required' => translate('messages.select_dm_type')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],403);
        }

        if ($request->has('image')) {
            $image_name = Helpers::upload('delivery-man/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload('delivery-man/', 'png', $img);
                array_push($id_img_names, $identity_image);
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = New DeliveryMan();
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->identity_image = $identity_image;
        $dm->image = $image_name;
        $dm->status = 0;
        $dm->active = 0;
        $dm->application_status = 'pending';
        $dm->zone_id = $request->zone_id;
        $dm->earning = $request->earning;
        $dm->password = bcrypt($request->password);
        $dm->save();

        return response()->json(['message' => translate('messages.deliveryman_added_successfully')], 200);
    }
}

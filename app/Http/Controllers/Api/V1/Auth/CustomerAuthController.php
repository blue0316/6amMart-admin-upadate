<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\CustomerLogic;
use App\Models\User;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Mail\EmailVerification;
use App\Models\BusinessSetting;
use App\CentralLogics\SMS_module;
use App\Models\EmailVerifications;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    public function verify_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:11|max:14',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            if ($user->is_phone_verified) {
                return response()->json([
                    'message' => translate('messages.phone_number_is_already_varified')
                ], 200);
            }

            if (env('APP_MODE') == 'demo') {
                if ($request['otp'] == "1234") {
                    $user->is_phone_verified = 1;
                    $user->save();

                    return response()->json([
                        'message' => translate('messages.phone_number_varified_successfully'),
                        'otp' => 'inactive'
                    ], 200);
                }
                return response()->json([
                    'message' => translate('messages.phone_number_and_otp_not_matched')
                ], 404);
            }

            $data = DB::table('phone_verifications')->where([
                'phone' => $request['phone'],
                'token' => $request['otp'],
            ])->first();

            if ($data) {
                DB::table('phone_verifications')->where([
                    'phone' => $request['phone'],
                    'token' => $request['otp'],
                ])->delete();

                $user->is_phone_verified = 1;
                $user->save();

                return response()->json([
                    'message' => translate('messages.phone_number_varified_successfully'),
                    'otp' => 'inactive'
                ], 200);
            } else {
                return response()->json([
                    'message' => translate('messages.phone_number_and_otp_not_matched')
                ], 404);
            }
        }
        return response()->json([
            'message' => translate('messages.not_found')
        ], 404);
    }

    public function check_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        if (BusinessSetting::where(['key' => 'email_verification'])->first()->value) {
            $token = rand(1000, 9999);
            DB::table('email_verifications')->insert([
                'email' => $request['email'],
                'token' => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (config('mail.status')) {
                Mail::to($request['email'])->send(new EmailVerification($token));
            }
            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'active'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'inactive'
            ], 200);
        }
    }

    public function verify_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $verify = EmailVerifications::where(['email' => $request['email'], 'token' => $request['token']])->first();

        if (isset($verify)) {
            $verify->delete();
            return response()->json([
                'message' => translate('messages.token_varified'),
            ], 200);
        }

        $errors = [];
        array_push($errors, ['code' => 'token', 'message' => translate('messages.token_not_found')]);
        return response()->json(
            ['errors' => $errors],
            404
        );
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $customer_verification = BusinessSetting::where('key', 'customer_verification')->first()->value;

        //Check Exists Ref Code
        $check_duplicate_ref = WalletTransaction::where('reference', $request->phone)->first();

        //Check Exists Ref Code Condition
        if ($check_duplicate_ref) {
            return response()->json(['errors'=>['code'=>'ref_code','message'=>'Referral code already used']]);
        } else {

            //User Creation
            $user = User::create([
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
            ]);
            $user->ref_code = Helpers::generate_referer_code($user);
            $user->save();

            //Save point to refeer
            if ($request->ref_code) {
                $checkRefCode = $request->ref_code;
                $referar_user = User::where('ref_code', '=', $checkRefCode)->first();
                $ref_status = BusinessSetting::where('key', 'ref_earning_status')->first()->value;
                if ($ref_status != '1') {
                    $errors = [];
                    array_push($errors, ['code' => 'ref_code', 'message' => translate('messages.referer_disable')]);
                    return response()->json([
                        'errors' => $errors
                    ], 405);
                }

                if (!$referar_user) {
                    $errors = [];
                    array_push($errors, ['code' => 'ref_code', 'message' => translate('messages.referer_code_not_found')]);
                    return response()->json([
                        'errors' => $errors
                    ], 405);
                }

                $ref_code_exchange_amt = BusinessSetting::where('key', 'ref_earning_exchange_rate')->first()->value;

                $refer_wallet_transaction = CustomerLogic::create_wallet_transaction($referar_user->id, $ref_code_exchange_amt, 'referrer', $user->phone);
                //dd($refer_wallet_transaction);
                try {
                    if (config('mail.status')) {
                        Mail::to($referar_user->email)->send(new \App\Mail\AddFundToWallet($refer_wallet_transaction));
                    }
                } catch (\Exception $ex) {
                    info($ex);
                }
            }
        }



        $token = $user->createToken('RestaurantCustomerAuth')->accessToken;

        if ($customer_verification && env('APP_MODE') != 'demo') {
            $otp = rand(1000, 9999);
            DB::table('phone_verifications')->updateOrInsert(
                ['phone' => $request['phone']],
                [
                    'token' => $otp,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            try {
                if (config('mail.status')) {
                    Mail::to($request['email'])->send(new EmailVerification($otp));
                }
            } catch (\Exception $ex) {
                info($ex);
            }

            $response = SMS_module::send($request['phone'], $otp);
            if ($response != 'success') {
                $errors = [];
                array_push($errors, [
                    'code' => 'otp',
                    'message' => translate('messages.faield_to_send_sms')
                ]);
                return response()->json([
                    'errors' => $errors
                ], 405);
            }
        }
        try {
            if (config('mail.status')) {
                Mail::to($request->email)->send(new \App\Mail\CustomerRegistration($request->f_name . ' ' . $request->l_name));
            }
        } catch (\Exception $ex) {
            info($ex);
        }
        return response()->json(['token' => $token, 'is_phone_verified' => 0, 'phone_verify_end_url' => "api/v1/auth/verify-phone"], 200);
    }

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
        $customer_verification = BusinessSetting::where('key', 'customer_verification')->first()->value;
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('RestaurantCustomerAuth')->accessToken;
            if (!auth()->user()->status) {
                $errors = [];
                array_push($errors, ['code' => 'auth-003', 'message' => translate('messages.your_account_is_blocked')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
            if ($customer_verification && !auth()->user()->is_phone_verified && env('APP_MODE') != 'demo') {
                $otp = rand(1000, 9999);
                DB::table('phone_verifications')->updateOrInsert(
                    ['phone' => $request['phone']],
                    [
                        'token' => $otp,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $response = SMS_module::send($request['phone'], $otp);
                if ($response != 'success') {

                    $errors = [];
                    array_push($errors, ['code' => 'otp', 'message' => translate('messages.faield_to_send_sms')]);
                    return response()->json([
                        'errors' => $errors
                    ], 405);
                }
            }
            $user = auth()->user();
            if($user->ref_code == null && isset($user->id)){
                $ref_code = Helpers::generate_referer_code($user);
                DB::table('users')->where('phone', $user->phone)->update(['ref_code' => $ref_code]);
            }
            return response()->json(['token' => $token, 'is_phone_verified' => auth()->user()->is_phone_verified], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('messages.Unauthorized')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }
}

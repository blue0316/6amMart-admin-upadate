<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\CouponLogic;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function list(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $zone_id= $request->header('zoneId');
        $data = [];
        // try {
            $coupons = Coupon::active()
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->whereDate('expire_date', '>=', date('Y-m-d'))->whereDate('start_date', '<=', date('Y-m-d'))->get();
            foreach($coupons as $key=>$coupon)
            {
                if($coupon->coupon_type == 'store_wise')
                {
                    $temp = Store::active()
                    ->when(config('module.current_module_data'), function($query)use($zone_id){
                        if(!config('module.current_module_data')['all_zone_service']) {
                            $query->whereIn('zone_id', json_decode($zone_id, true));
                        }
                    })
                    ->whereIn('id', json_decode($coupon->data, true))->first();
                    if($temp)
                    {
                        $coupon->data = $temp->name;
                        $data[] = $coupon;
                    }
                }
                else if($coupon->coupon_type == 'zone_wise')
                {
                    if(count(array_intersect(json_decode($zone_id, true), json_decode($coupon->data,true))))
                    {
                        $data[] = $coupon;
                    }
                }
                else{
                    $data[] = $coupon;
                }
            }

            return response()->json($data, 200);
        // } catch (\Exception $e) {
        //     return response()->json(['errors' => $e], 403);
        // }
    }

    public function apply(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'store_id' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        
        try {
            $coupon = Coupon::active()->where(['code' => $request['code']])->first();
            if (isset($coupon)) {
                $staus = CouponLogic::is_valide($coupon, $request->user()->id ,$request['store_id']);

                switch ($staus) {
                case 200:
                    return response()->json($coupon, 200); 
                case 406:
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.coupon_usage_limit_over')]
                        ]
                    ], 406);
                case 407:
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.coupon_expire')]
                        ]
                    ], 407);
                default:
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.not_found')]                            
                        ]
                    ], 404);
                }
            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.not_found')]
                    ]
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }
}

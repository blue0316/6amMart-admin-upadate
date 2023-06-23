<?php

namespace App\CentralLogics;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\AdminWallet;
use App\Models\BusinessSetting;
use App\Models\StoreWallet;
use App\Models\DeliveryManWallet;
use App\CentralLogics\CustomerLogic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderLogic
{
    public static function gen_unique_id()
    {
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }

    public static function track_order($order_id)
    {
        return Helpers::order_data_formatting(Order::with(['details', 'delivery_man.rating'])->where(['id' => $order_id])->first(), false);
    }

    public static function updated_order_calculation($order)
    {
        return true;
    }
    public static function create_transaction($order, $received_by=false, $status = null)
    {
        $type = $order->order_type;
        $dm_tips_manage_status = BusinessSetting::where('key', 'dm_tips_status')->first()->value;
        $admin_subsidy = 0;
        if($type=='parcel')
        {
            $comission = \App\Models\BusinessSetting::where('key','parcel_commission_dm')->first();
            $dm_tips = $dm_tips_manage_status ? $order->dm_tips : 0;
            $comission = isset($comission) ? $comission->value : 0;
            $order_amount = $order->order_amount - $dm_tips;
            $dm_commission = $comission?($order_amount/ 100) * $comission:0;
            $comission_amount = $order_amount - $dm_commission;
        }
        else
        {
            $comission = isset($order->store->comission) == null?\App\Models\BusinessSetting::where('key','admin_commission')->first()->value:$order->store->comission;
            $dm_tips = $dm_tips_manage_status ? $order->dm_tips : 0;
            $order_amount = $order->order_amount - $order->delivery_charge - $order->total_tax_amount - $dm_tips;

            // comission in delivery charge
            $delivery_charge_comission = BusinessSetting::where('key', 'delivery_charge_comission')->first();
            $delivery_charge_comission_percentage = $delivery_charge_comission ? $delivery_charge_comission->value : 0;
            $comission_on_delivery = $delivery_charge_comission_percentage * ( $order->original_delivery_charge / 100 );

            if($order->store->self_delivery_system)
            {
                $comission_on_actual_delivery_fee = 0;
            }else{

                $comission_on_actual_delivery_fee = ($order->delivery_charge > 0) ? $comission_on_delivery : 0;
            }

            //final comission
            $comission_on_store_amount = ($comission?($order_amount/ 100) * $comission:0);
            $comission_amount = $comission_on_store_amount + $comission_on_actual_delivery_fee;
            $dm_commission = $order->original_delivery_charge - $comission_on_actual_delivery_fee;

            if($order->free_delivery_by == 'admin')
            {
                if($order->store->self_delivery_system)
                {
                    $comission_on_actual_delivery_fee = 0;
                }else{
    
                    $comission_on_actual_delivery_fee = ($order->original_delivery_charge > 0) ? $comission_on_delivery : 0;
                }
    
                //final comission
                $comission_on_store_amount = ($comission?($order_amount/ 100) * $comission:0);
                $comission_amount = $comission_on_store_amount + $comission_on_actual_delivery_fee;
                $dm_commission = $order->original_delivery_charge - $comission_on_actual_delivery_fee;

                $admin_subsidy = $order->original_delivery_charge;
                Helpers::expenseCreate($order->original_delivery_charge,'free_delivery',now(),$order->id);
            }
        }

        try{
            OrderTransaction::insert([
                'vendor_id' =>$type=='parcel'?null:$order->store->vendor->id,
                'delivery_man_id'=>$order->delivery_man_id,
                'order_id' =>$order->id,
                'order_amount'=>$order->order_amount,
                'store_amount'=>$type=='parcel' ? 0 : $order_amount + $order->total_tax_amount - $comission_on_store_amount,
                'admin_commission'=>$comission_amount - $admin_subsidy,
                'delivery_charge'=>$order->delivery_charge,
                'original_delivery_charge'=>$dm_commission,
                'tax'=>$order->total_tax_amount,
                'received_by'=> $received_by?$received_by:'admin',
                'zone_id'=>$order->zone_id,
                'module_id'=>$order->module_id,
                'admin_expense'=>$admin_subsidy,
                'status'=> $status,
                'dm_tips'=> $dm_tips,
                'created_at' => now(),
                'updated_at' => now(),
                'delivery_fee_comission'=>isset($comission_on_actual_delivery_fee)?$comission_on_actual_delivery_fee: 0
            ]);
            $adminWallet = AdminWallet::firstOrNew(
                ['admin_id' => Admin::where('role_id', 1)->first()->id]
            );

            $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $comission_amount - $admin_subsidy;

            if($type != 'parcel')
            {
                $vendorWallet = StoreWallet::firstOrNew(
                    ['vendor_id' => $order->store->vendor->id]
                );
                if($order->store->self_delivery_system)
                {
                    $vendorWallet->total_earning = $vendorWallet->total_earning + $order->delivery_charge + $dm_tips;
                }
                else{
                    $adminWallet->delivery_charge = $adminWallet->delivery_charge+$order->delivery_charge;
                }
                $vendorWallet->total_earning = $vendorWallet->total_earning+($order_amount + $order->total_tax_amount - $comission_on_store_amount);
            }
            if($order->delivery_man && ($type == 'parcel' || ($order->store && !$order->store->self_delivery_system))){
                $dmWallet = DeliveryManWallet::firstOrNew(
                    ['delivery_man_id' => $order->delivery_man_id]
                );
                if($order->delivery_man->earning == 1){
                    $dmWallet->total_earning = $dmWallet->total_earning + $dm_commission+ $dm_tips;
                }else {
                    $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $dm_commission + $dm_tips;
                }
            } else {
                $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $dm_commission + $dm_tips;
            }

            try
            {
                DB::beginTransaction();
                if($received_by=='admin')
                {
                    $adminWallet->digital_received = $adminWallet->digital_received+$order->order_amount;
                }
                else if($received_by=='store' && $type != 'parcel' && $order->payment_method == "cash_on_delivery")
                {
                    $vendorWallet->collected_cash = $vendorWallet->collected_cash+$order->order_amount;
                }
                else if($received_by==false)
                {
                    $adminWallet->manual_received = $adminWallet->manual_received+$order->order_amount;
                }
                else if($received_by=='deliveryman' && $order->delivery_man && $order->delivery_man->type == 'zone_wise')
                {
                    $dmWallet->collected_cash = $dmWallet->collected_cash+$order->order_amount;
                }

                $adminWallet->save();
                if($type != 'parcel')
                {
                    $vendorWallet->save();
                }
                if(isset($dmWallet)){
                    $dmWallet->save();
                }

                DB::commit();
                if($order->user_id) CustomerLogic::create_loyalty_point_transaction($order->user_id, $order->id, $order->order_amount, 'order_place');

            }
            catch(\Exception $e)
            {
                DB::rollBack();
                info($e);
                return false;
            }
        }
        catch(\Exception $e){
            info($e);
            return false;
        }

        return true;
    }

    public static function refund_before_delivered($order){
        $adminWallet = AdminWallet::firstOrNew(
            ['admin_id' => Admin::where('role_id', 1)->first()->id]
        );
        if ($order->payment_method == 'cash_on_delivery') {
            return false;
        }
        $adminWallet->digital_received = $adminWallet->digital_received - $order->order_amount;
        $adminWallet->save();
        if ($order->payment_status == "paid" && BusinessSetting::where('key', 'wallet_add_refund')->first()->value == 1) {
            CustomerLogic::create_wallet_transaction($order->user_id, $order->order_amount, 'order_refund', $order->id);
        }
        return true;
    }

    public static function refund_order($order)
    {
        $order_transaction = $order->transaction;
        if($order_transaction == null || $order->store == null)
        {
            return false;
        }
        $received_by = $order_transaction->received_by;

        $adminWallet = AdminWallet::firstOrNew(
            ['admin_id' => Admin::where('role_id', 1)->first()->id]
        );

        $vendorWallet = StoreWallet::firstOrNew(
            ['vendor_id' => $order->store->vendor->id]
        );

        $adminWallet->total_commission_earning = $adminWallet->total_commission_earning - $order_transaction->admin_commission + $order_transaction->delivery_fee_comission;

        $vendorWallet->total_earning = $vendorWallet->total_earning - $order_transaction->store_amount;

        $refund_amount = $order->order_amount;

        $status = 'refunded_with_delivery_charge';
        if($order->order_status == 'delivered' || $order->order_status == 'refund_requested')
        {
            $refund_amount = $order->order_amount - $order->delivery_charge -$order->dm_tips;
            $status = 'refunded_without_delivery_charge';
        }
        else
        {
            $adminWallet->delivery_charge = $adminWallet->delivery_charge - $order_transaction->delivery_charge;
        }
        try
        {
            DB::beginTransaction();
            if($received_by=='admin')
            {
                if($order->delivery_man_id && $order->payment_method != "cash_on_delivery")
                {
                    $adminWallet->digital_received = $adminWallet->digital_received - $refund_amount;
                }
                else
                {
                    $adminWallet->manual_received = $adminWallet->manual_received - $refund_amount;
                }

            }
            else if($received_by=='store')
            {
                $vendorWallet->collected_cash = $vendorWallet->collected_cash - $refund_amount;
            }

            // else if($received_by=='deliveryman')
            // {
            //     $dmWallet = DeliveryManWallet::firstOrNew(
            //         ['delivery_man_id' => $order->delivery_man_id]
            //     );
            //     $dmWallet->collected_cash=$dmWallet->collected_cash - $refund_amount;
            //     $dmWallet->save();
            // }
            $order_transaction->status = $status;
            $order_transaction->save();
            $adminWallet->save();
            $vendorWallet->save();
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            info($e->getMessage());
            return false;
        }
        return true;

    }

    public static function format_export_data($orders, $type='order')
    {
        $data = [];
        foreach($orders as $key=>$order)
        {

            $data[]=[
                '#'=>$key+1,
                translate('messages.order')=>$order['id'],
                translate('messages.date')=>date('d M Y',strtotime($order['created_at'])),
                translate('messages.customer')=>$order->customer?$order->customer['f_name'].' '.$order->customer['l_name']:translate('messages.invalid').' '.translate('messages.customer').' '.translate('messages.data'),
                translate($type=='order'?'messages.store':'messages.parcel_category')=>Str::limit($type=='order'?($order->store?$order->store->name:translate('messages.store deleted!')):($order->parcel_category?$order->parcel_category->name:translate('messages.not_found')),20,'...'),
                translate('messages.payment').' '.translate('messages.status')=>$order->payment_status=='paid'?translate('messages.paid'):translate('messages.unpaid'),
                translate('messages.total')=>\App\CentralLogics\Helpers::format_currency($order['order_amount']),
                translate('messages.order').' '.translate('messages.status')=>translate('messages.'. $order['order_status']),
                translate('messages.order').' '.translate('messages.type')=>translate('messages.'.$order['order_type'])
            ];
        }
        return $data;
    }
    public static function format_store_order_export_data($orders)
    {
        $data = [];
        foreach($orders as $key=>$order)
        {

            $data[]=[
                '#'=>$key+1,
                translate('messages.order')=>$order['id'],
                translate('messages.date')=>date('d M Y',strtotime($order['created_at'])),
                translate('messages.customer')=>$order->customer?$order->customer['f_name'].' '.$order->customer['l_name']:translate('messages.invalid').' '.translate('messages.customer').' '.translate('messages.data'),
                translate('messages.payment').' '.translate('messages.status')=>$order->payment_status=='paid'?translate('messages.paid'):translate('messages.unpaid'),
                translate('messages.total')=>\App\CentralLogics\Helpers::format_currency($order['order_amount']),
                translate('messages.order').' '.translate('messages.status')=>translate('messages.'. $order['order_status']),
                translate('messages.order').' '.translate('messages.type')=>translate('messages.'.$order['order_type']),
                translate('messages.discount_amount')=>$order['coupon_discount_amount']+$order['store_discount_amount'],
                translate('messages.total_tax_amount')=>$order['total_tax_amount'],
                translate('messages.delivery_charge')=>$order['original_delivery_charge']
            ];
        }
        return $data;
    }

    public static function format_order_report_export_data($orders)
    {
        $data = [];
        foreach($orders as $key=>$order)
        {

            $data[]=[
                '#'=>$key+1,
                translate('messages.order')=>$order['id'],
                translate('messages.store')=>$order->store?$order->store->name:translate('messages.invalid'),
                translate('messages.customer_name')=>$order->customer?$order->customer['f_name'].' '.$order->customer['l_name']:translate('messages.invalid').' '.translate('messages.customer').' '.translate('messages.data'),
                translate('Total Item Amount')=>\App\CentralLogics\Helpers::format_currency($order['order_amount']-$order['dm_tips']-$order['total_tax_amount']-$order['delivery_charge']+$order['coupon_discount_amount'] + $order['store_discount_amount']),
                translate('Item Discount')=>\App\CentralLogics\Helpers::format_currency($order->details->sum('discount_on_item')),
                translate('Coupon Discount')=>\App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount']),
                translate('Discounted Amount')=>\App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount'] + $order['store_discount_amount']),
                translate('messages.tax')=>\App\CentralLogics\Helpers::format_currency($order['total_tax_amount']),
                translate('messages.delivery_charge')=>\App\CentralLogics\Helpers::format_currency($order['original_delivery_charge']),
                translate('messages.order_amount')=>\App\CentralLogics\Helpers::format_currency($order['order_amount']),
                translate('messages.amount_received_by')=>isset($order->transaction) ? $order->transaction->received_by : translate('messages.unpaid'),
                translate('messages.payment_method')=>translate(str_replace('_', ' ', $order['payment_method'])),
                translate('messages.order').' '.translate('messages.status')=>translate('messages.'. $order['order_status']),
                translate('messages.order').' '.translate('messages.type')=>translate('messages.'.$order['order_type']),
            ];
        }
        return $data;
    }
}

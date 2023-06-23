<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\BusinessSetting;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Illuminate\Support\Facades\DB;

class FlutterwaveController extends Controller
{
    /**
     * Initialize Rave payment process
     * @return void
     */
    public function initialize(Request $request)
    {
        //This generates a payment reference
        $reference = Flutterwave::generateReference();
        $order = Order::with(['details','customer'])->where(['id' => $request->order_id, 'user_id'=>$request->customer_id])->first();
        $user_data = $order->customer;
        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $order->order_amount,
            'email' => $user_data['email'],
            'tx_ref' => $reference,
            'currency' => Helpers::currency_code(),
            'redirect_url' => route('flutterwave_callback',['order_id'=>$order->id]),
            'customer' => [
                'email' => $user_data['email'],
                "phone_number" => $user_data['phone'],
                "name" => $user_data['f_name'].''.$user_data['l_name']
            ],

            "customizations" => [
                "title" => BusinessSetting::where(['key'=>'business_name'])->first()->value??'6amMart',
                "description" => $order->id,
            ]
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            $order->order_status = 'failed';
            $order->failed = now();
            $order->save();
            if ($order->callback != null) {
                return redirect($order->callback . '&status=fail');
            }else{
                return \redirect()->route('payment-fail');
            }
        }
        return redirect($payment['data']['link']);

    }

    /**
     * Obtain Rave callback information
     * @return void
     */
    public function callback(Request $request, $order_id)
    {

        $order = Order::with(['details'])->where(['id' => $order_id])->first();

        if (in_array($request->status, ['successful','completed'])) {

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            try {
                $order->transaction_reference = $transactionID;
                $order->payment_method = 'flutterwave';
                $order->payment_status = 'paid';
                $order->order_status = 'confirmed';
                $order->confirmed = now();
                $order->save();
                Helpers::send_order_notification($order);
            } catch (\Exception $e) {
            }

            if ($order->callback != null) {
                return redirect($order->callback . '&status=success');
            }else{
                return \redirect()->route('payment-success');
            }
        }
        // elseif ($status ==  'cancelled'){
        //     //Put desired action/code after transaction has been cancelled here
        // }
        else{
            $order->order_status = 'failed';
            $order->failed = now();
            $order->save();
            if ($order->callback != null) {
                return redirect($order->callback . '&status=fail');
            }else{
                return \redirect()->route('payment-fail');
            }
        }
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (including parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here

    }
}

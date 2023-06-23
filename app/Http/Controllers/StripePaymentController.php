<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\Stripe;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use PHPUnit\Exception;


class StripePaymentController extends Controller
{
    public function payment_process_3d(Request $request)
    {
        $tran = Str::random(6) . '-' . rand(1, 1000);
        $order_id = $request->order_id;
        $order = Order::with(['details'])->where(['id' => $order_id])->first();
        $config = Helpers::get_business_settings('stripe');
        Stripe::setApiKey($config['api_key']);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = url('/');

        $products = [];
        foreach ($order->details as $detail) {
            array_push($products, [
                'name' => $detail->item?$detail->item['name']:$detail->campaign['name'],
                'image' => 'def.png'
            ]);
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => Helpers::currency_code(),
                    'unit_amount' => $order->order_amount * 100,
                    'product_data' => [
                        'name' => BusinessSetting::where(['key' => 'business_name'])->first()->value,
                        'images' => [asset('storage/app/public/business') . '/' . BusinessSetting::where(['key' => 'logo'])->first()->value],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => (String)route('pay-stripe.success',['order_id'=>$order->id,'transaction_ref'=>$tran]),
            'cancel_url' => url()->previous(),
        ]);

        return response()->json(['id' => $checkout_session->id]);
    }

    public function success($order_id,$transaction_ref)
    {
        $order = Order::find($order_id);
        $order->order_status='confirmed';
        $order->payment_method='stripe';
        $order->transaction_reference=$transaction_ref;
        $order->payment_status='paid';
        $order->confirmed=now();
        $order->save();
        try {
            Helpers::send_order_notification($order);
        } catch (\Exception $e) {

        }

        if ($order->callback != null) {
            return redirect($order->callback . '&status=success');
        }

        return \redirect()->route('payment-success');
    }

    public function fail()
    {
        DB::table('orders')
        ->where('id', session('order_id'))
        ->update(['order_status' => 'failed',  'payment_status' => 'unpaid', 'failed'=>now()]);
        $order = Order::find(session('order_id'));
        if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }
        return \redirect()->route('payment-fail');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class BkashPaymentController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;

    public function __construct()
    {
        $config=\App\CentralLogics\Helpers::get_business_settings('bkash');
        // You can import it from your Database
        $bkash_app_key = $config['api_key']; // bKash Merchant API APP KEY
        $bkash_app_secret = $config['api_secret']; // bKash Merchant API APP SECRET
        $bkash_username = $config['username']; // bKash Merchant API USERNAME
        $bkash_password = $config['password']; // bKash Merchant API PASSWORD
        $bkash_base_url = (env('APP_MODE') == 'live') ? 'https://tokenized.pay.bka.sh/v1.2.0-beta' : 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';

        // $this->app_key = $bkash_app_key;
        // $this->app_secret = $bkash_app_secret;
        // $this->username = $bkash_username;
        // $this->password = $bkash_password;
        // $this->base_url = $bkash_base_url;

        $this->app_key = '4f6o0cjiki2rfm34kfdadl1eqq';
        $this->app_secret = '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b';
        $this->username = 'sandboxTokenizedUser02';
        $this->password = 'sandboxTokenizedUser02@12345';
        $this->base_url = $bkash_base_url;
    }

    public function getToken()
    {
        session()->forget('bkash_token');

        $request_data = array(
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret
        );
        $url = curl_init('https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant');
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:'.$this->username,
            'password:'.$this->password
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        $response = json_decode($resultdata, true);

        if (array_key_exists('msg', $response)) {
            return $response;
        }

        session()->put('bkash_token', $response['id_token']);

        return $response;
    }

    public function make_tokenize_payment(Request $request)
    {
        $order = Order::with(['details','customer'])->where(['id' => $request->order_id])->first();
        $user_data = User::find($request->customer_id);
        $response = self::getToken();
        $auth = $response['id_token'];
        session()->put('token', $auth);
        $callbackURL = route('bkash-callback', ['order_id' => $request->order_id, 'token' => $auth]);

        $requestbody = array(
            'mode' => '0011',
            'amount' => (string)$order->order_amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'payerReference' => $user_data->phone,
            'merchantInvoiceNumber' => 'invoice_' . Str::random('15'),
            'callbackURL' => $callbackURL
        );

        $url = curl_init($this->base_url . '/tokenized/checkout/create');
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . $this->app_key
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        //echo $resultdata;

        $obj = json_decode($resultdata);
        return redirect()->away($obj->{'bkashURL'});
    }

    public function callback(Request $request)
    {
        $paymentID = $_GET['paymentID'];
        $auth = $_GET['token'];

        $request_body = array(
            'paymentID' => $paymentID
        );
        $url = curl_init($this->base_url . '/tokenized/checkout/execute');

        $request_body_json = json_encode($request_body);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . $this->app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_body_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        info($resultdata);
        curl_close($url);
        $obj = json_decode($resultdata);

        $order = Order::find($request['order_id']);
        $order->payment_method = 'bkash';
        $order->order_status = 'confirmed';
        $order->payment_status = 'paid';
        $order->transaction_reference = $obj->trxID ?? null;

        if ($obj->statusCode == '0000') {
            $order->save();
            if ($order->callback != null) {
                return redirect($order->callback . '&status=success');
            }else{
                return \redirect()->route('payment-success');
            }
        } else {
            if ($order->callback != null) {
                return redirect($order->callback . '&status=fail');
            }else{
                return \redirect()->route('payment-fail');
            }
        }
    }
}


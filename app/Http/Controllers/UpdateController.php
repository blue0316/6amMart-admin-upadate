<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Filesystem;
use App\Traits\ActivationClass;

ini_set('max_execution_time', 180);

use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class UpdateController extends Controller
{
    use ActivationClass;

    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        if (env('SOFTWARE_VERSION') == '1.0') {
            $filesystem = new Filesystem;
            $filesystem->cleanDirectory('database/migrations');
        }

        Helpers::setEnvironmentValue('BUYER_USERNAME', $request['username']);
        Helpers::setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        Helpers::setEnvironmentValue('APP_MODE', 'live');
        Helpers::setEnvironmentValue('SOFTWARE_VERSION', '2.0.0');
        Helpers::setEnvironmentValue('APP_NAME', '6amMart' . time());

        // $data = Helpers::requestSender();
        // if (!$data['active']) {
        if (!$this->actch()) {
            return redirect(base64_decode('aHR0cHM6Ly82YW10ZWNoLmNvbS9zb2Z0d2FyZS1hY3RpdmF0aW9u'));
        }

        Artisan::call('migrate', ['--force' => true]);
        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Helpers::insert_business_settings_key("mobile_app_section_heading" , "Download the App for Enjoy Best Restaurant Test");
        Helpers::insert_business_settings_key("mobile_app_section_text" , "Default Text Mobile App Section");
        Helpers::insert_business_settings_key("feature_section_description" , "Feature section description");
        Helpers::insert_business_settings_key("Feature section description", json_encode([
            "app_url_android_status" => "0",
            "app_url_android" => "https://play.google.com",
            "app_url_ios_status" => "0",
            "app_url_ios" => "https://www.apple.com/app-store",
            "web_app_url_status" => "0",
            "web_app_url" => "https://stackfood.6amtech.com/"
        ]));

        //version 1.5.0
        Helpers::insert_business_settings_key("wallet_status" , "0");
        Helpers::insert_business_settings_key("loyalty_point_status" , "0");
        Helpers::insert_business_settings_key("ref_earning_status" , "0");
        Helpers::insert_business_settings_key("wallet_add_refund" , "0");
        Helpers::insert_business_settings_key("loyalty_point_exchange_rate" , "0");
        Helpers::insert_business_settings_key("ref_earning_exchange_rate" , "0");
        Helpers::insert_business_settings_key("loyalty_point_item_purchase_point" , "0");
        Helpers::insert_business_settings_key("loyalty_point_minimum_point" , "0");
        Helpers::insert_business_settings_key("dm_tips_status" , "0");
        Helpers::insert_business_settings_key('tax_included', '0');
        Helpers::insert_business_settings_key('refund_active_status', '1');
        Helpers::insert_business_settings_key('social_login','[{"login_medium":"google","client_id":"","client_secret":"","status":"0"},{"login_medium":"facebook","client_id":"","client_secret":"","status":""}]');
        Helpers::insert_business_settings_key('system_language','[{"id":1,"direction":"ltr","code":"en","status":1,"default":true}]');

        return redirect('/admin/auth/login');
    }
}

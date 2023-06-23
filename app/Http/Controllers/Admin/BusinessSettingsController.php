<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\NotificationMessage;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BusinessSettingsController extends Controller
{
    public function business_index($tab='business')
    {
        if(!Helpers::module_permission_check('settings')){
            Toastr::error(translate('messages.access_denied'));
            return back();
        }
        if($tab == 'business')
        {
            return view('admin-views.business-settings.business-index');
        }else if($tab == 'customer')
        {
            $data = BusinessSetting::where('key','like','wallet_%')
            ->orWhere('key','like','loyalty_%')
            ->orWhere('key','like','ref_earning_%')
            ->orWhere('key','like','ref_earning_%')->get();
            $data = array_column($data->toArray(), 'value','key');
            return view('admin-views.business-settings.customer-index', compact('data'));
        }else if($tab == 'deliveryman')
        {
            return view('admin-views.business-settings.deliveryman-index');
        }
    }

    public function update_dm(Request $request)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }
        DB::table('business_settings')->updateOrInsert(['key' => 'dm_tips_status'], [
            'value' => $request['dm_tips_status']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'dm_maximum_orders'], [
            'value' => $request['dm_maximum_orders']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'canceled_by_deliveryman'], [
            'value' => $request['canceled_by_deliveryman']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'show_dm_earning'], [
            'value' => $request['show_dm_earning']
        ]);
        Toastr::success(translate('messages.successfully_updated_to_changes_restart_app'));
        return back();
    }

    public function business_setup(Request $request)
    {

        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'business_name'], [
            'value' => $request['store_name']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'currency'], [
            'value' => $request['currency']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'timezone'], [
            'value' => $request['timezone']
        ]);

        $curr_logo = BusinessSetting::where(['key' => 'logo'])->first();
        if ($request->has('logo')) {
            $image_name = Helpers::update('business/', $curr_logo->value, 'png', $request->file('logo'));
        } else {
            $image_name = $curr_logo['value'];
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'logo'], [
            'value' => $image_name
        ]);

        $fav_icon = BusinessSetting::where(['key' => 'icon'])->first();
        if ($request->has('icon')) {
            $image_name = Helpers::update('business/', $fav_icon->value, 'png', $request->file('icon'));
        } else {
            $image_name = $fav_icon['value'];
        }

        if(session()->has('currency_symbol')){
            session()->forget('currency_symbol');
        }
        if(session()->has('currency_code')){
            session()->forget('currency_code');
        }
        if(session()->has('currency_symbol_position')){
            session()->forget('currency_symbol_position');
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'site_direction'], [
            'value' => $request['site_direction']
        ]);
        
        DB::table('business_settings')->updateOrInsert(['key' => 'icon'], [
            'value' => $image_name
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'phone'], [
            'value' => $request['phone']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'email_address'], [
            'value' => $request['email']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'address'], [
            'value' => $request['address']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'footer_text'], [
            'value' => $request['footer_text']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'customer_verification'], [
            'value' => $request['customer_verification']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'order_delivery_verification'], [
            'value' => $request['odc']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'minimum_shipping_charge'], [
            'value' => $request['minimum_shipping_charge']
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'per_km_shipping_charge'], [
            'value' => $request['per_km_shipping_charge']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'currency_symbol_position'], [
            'value' => $request['currency_symbol_position']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'schedule_order'], [
            'value' => $request['schedule_order']
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'tax_included'], [
            'value' => $request['tax_included']
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'order_confirmation_model'], [
            'value' => $request['order_confirmation_model']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'tax'], [
            'value' => $request['tax']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'admin_commission'], [
            'value' => $request['admin_commission']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'country'], [
            'value' => $request['country']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'default_location'], [
            'value' => json_encode(['lat' => $request['latitude'], 'lng' => $request['longitude']])
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'admin_order_notification'], [
            'value' => $request['admin_order_notification']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'free_delivery_over_status'], [
            'value' => $request['free_delivery_over_status'] ? $request['free_delivery_over_status'] : null
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'free_delivery_over'], [
            'value' => $request['free_delivery_over_status'] ? $request['free_delivery_over'] : null
        ]);

        // $languages = $request['language'];

        // if (in_array('en', $languages)) {
        //     unset($languages[array_search('en', $languages)]);
        // }
        // array_unshift($languages, 'en');

        // DB::table('business_settings')->updateOrInsert(['key' => 'language'], [
        //     'value' => json_encode($languages),
        // ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'timeformat'], [
            'value' => $request['time_format']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'canceled_by_store'], [
            'value' => $request['canceled_by_store']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'toggle_veg_non_veg'], [
            'value' => $request['vnv']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'toggle_dm_registration'], [
            'value' => $request['dm_self_registration']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'prescription_order_status'], [
            'value' => $request['prescription_order_status']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'toggle_store_registration'], [
            'value' => $request['store_self_registration']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'schedule_order_slot_duration'], [
            'value' => $request['schedule_order_slot_duration']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'digit_after_decimal_point'], [
            'value' => $request['digit_after_decimal_point']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'delivery_charge_comission'], [
            'value' => $request['admin_comission_in_delivery_charge']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'opening_time'], [
            'value' => $request['opening_time']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'closing_time'], [
            'value' => $request['closing_time']
        ]);

        if ($request->opening_day == $request->closing_day) {
            Toastr::error(translate('messages.the_start_day_and_end_day_is_same'));
        } else {
            DB::table('business_settings')->updateOrInsert(['key' => 'opening_day'], [
                'value' => $request['opening_day']
            ]);

            DB::table('business_settings')->updateOrInsert(['key' => 'closing_day'], [
                'value' => $request['closing_day']
            ]);
        }


        Toastr::success(translate('messages.successfully_updated_to_changes_restart_app'));
        return back();
    }

    public function mail_index()
    {
        return view('admin-views.business-settings.mail-index');
    }

    public function mail_config(Request $request)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }
        BusinessSetting::updateOrInsert(
            ['key' => 'mail_config'],
            [
                'value' => json_encode([
                    "status" => $request['status'] ?? 0,
                    "name" => $request['name'],
                    "host" => $request['host'],
                    "driver" => $request['driver'],
                    "port" => $request['port'],
                    "username" => $request['username'],
                    "email_id" => $request['email'],
                    "encryption" => $request['encryption'],
                    "password" => $request['password']
                ]),
                'updated_at' => now()
            ]
        );
        Toastr::success(translate('messages.configuration_updated_successfully'));
        return back();
    }

    public function payment_index()
    {
        return view('admin-views.business-settings.payment-index');
    }

    public function payment_update(Request $request, $name)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }
        if ($name == 'cash_on_delivery') {
            $payment = BusinessSetting::where('key', 'cash_on_delivery')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'cash_on_delivery'])->update([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'digital_payment') {
            $payment = BusinessSetting::where('key', 'digital_payment')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'digital_payment'])->update([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'ssl_commerz_payment') {
            $payment = BusinessSetting::where('key', 'ssl_commerz_payment')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => 1,
                        'store_id'       => '',
                        'store_password' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'ssl_commerz_payment'])->update([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => $request['status'],
                        'store_id'       => $request['store_id'],
                        'store_password' => $request['store_password'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'razor_pay') {
            $payment = BusinessSetting::where('key', 'razor_pay')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => 1,
                        'razor_key'    => '',
                        'razor_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'razor_pay'])->update([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => $request['status'],
                        'razor_key'    => $request['razor_key'],
                        'razor_secret' => $request['razor_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'paypal') {
            $payment = BusinessSetting::where('key', 'paypal')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => 1,
                        'paypal_client_id' => '',
                        'paypal_secret'    => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'paypal'])->update([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => $request['status'],
                        'paypal_client_id' => $request['paypal_client_id'],
                        'paypal_secret'    => $request['paypal_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'stripe') {
            $payment = BusinessSetting::where('key', 'stripe')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => 1,
                        'api_key'       => '',
                        'published_key' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'stripe'])->update([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'api_key'       => $request['api_key'],
                        'published_key' => $request['published_key'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'senang_pay') {
            $payment = BusinessSetting::where('key', 'senang_pay')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([

                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'        => 1,
                        'secret_key'    => '',
                        'published_key' => '',
                        'merchant_id' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'senang_pay'])->update([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'secret_key'    => $request['secret_key'],
                        'published_key' => $request['publish_key'],
                        'merchant_id' => $request['merchant_id'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'paystack') {
            $payment = BusinessSetting::where('key', 'paystack')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'paystack',
                    'value'      => json_encode([
                        'status'        => 1,
                        'publicKey'     => '',
                        'secretKey'     => '',
                        'paymentUrl'    => '',
                        'merchantEmail' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'paystack'])->update([
                    'key'        => 'paystack',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'publicKey'     => $request['publicKey'],
                        'secretKey'     => $request['secretKey'],
                        'paymentUrl'    => $request['paymentUrl'],
                        'merchantEmail' => $request['merchantEmail'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'flutterwave') {
            $payment = BusinessSetting::where('key', 'flutterwave')->first();
            if (isset($payment) == false) {
                DB::table('business_settings')->insert([
                    'key'        => 'flutterwave',
                    'value'      => json_encode([
                        'status'        => 1,
                        'public_key'     => '',
                        'secret_key'     => '',
                        'hash'    => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'flutterwave'])->update([
                    'key'        => 'flutterwave',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'public_key'     => $request['public_key'],
                        'secret_key'     => $request['secret_key'],
                        'hash'    => $request['hash'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'mercadopago') {
            $payment = BusinessSetting::updateOrInsert(
                ['key' => 'mercadopago'],
                [
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'public_key'     => $request['public_key'],
                        'access_token'     => $request['access_token'],
                    ]),
                    'updated_at' => now()
                ]
            );
        } elseif ($name == 'paymob_accept') {
            DB::table('business_settings')->updateOrInsert(['key' => 'paymob_accept'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'iframe_id' => $request['iframe_id'],
                    'integration_id' => $request['integration_id'],
                    'hmac' => $request['hmac'],
                ]),
                'updated_at' => now()
            ]);
        } elseif ($name == 'liqpay') {
            DB::table('business_settings')->updateOrInsert(['key' => 'liqpay'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'private_key' => $request['private_key']
                ]),
                'updated_at' => now()
            ]);
        } elseif ($name == 'paytm') {
            DB::table('business_settings')->updateOrInsert(['key' => 'paytm'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'paytm_merchant_key' => $request['paytm_merchant_key'],
                    'paytm_merchant_mid' => $request['paytm_merchant_mid'],
                    'paytm_merchant_website' => $request['paytm_merchant_website'],
                    'paytm_refund_url' => $request['paytm_refund_url'],
                ]),
                'updated_at' => now()
            ]);
        } elseif ($name == 'bkash') {
            DB::table('business_settings')->updateOrInsert(['key' => 'bkash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'api_secret' => $request['api_secret'],
                    'username' => $request['username'],
                    'password' => $request['password'],
                ]),
                'updated_at' => now()
            ]);
        } elseif ($name == 'paytabs') {
            DB::table('business_settings')->updateOrInsert(['key' => 'paytabs'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'profile_id' => $request['profile_id'],
                    'server_key' => $request['server_key'],
                    'base_url' => $request['base_url']
                ]),
                'updated_at' => now()
            ]);
        }

        Toastr::success(translate('messages.payment_settings_updated'));
        return back();
    }

    public function app_settings()
    {
        return view('admin-views.business-settings.app-settings');
    }

    public function update_app_settings(Request $request)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }
        DB::table('business_settings')->updateOrInsert(['key' => 'app_minimum_version_android'], [
            'value' => $request['app_minimum_version_android']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'app_minimum_version_ios'], [
            'value' => $request['app_minimum_version_ios']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'app_url_android'], [
            'value' => $request['app_url_android']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'app_url_ios'], [
            'value' => $request['app_url_ios']
        ]);
        Toastr::success(translate('messages.app_settings_updated'));
        return back();
    }

    public function landing_page_settings($tab)
    {
        if ($tab == 'index') {
            return view('admin-views.business-settings.landing-page-settings.index');
        } else if ($tab == 'links') {
            return view('admin-views.business-settings.landing-page-settings.links');
        } else if ($tab == 'speciality') {
            return view('admin-views.business-settings.landing-page-settings.speciality');
        } else if ($tab == 'testimonial') {
            return view('admin-views.business-settings.landing-page-settings.testimonial');
        } else if ($tab == 'feature') {
            return view('admin-views.business-settings.landing-page-settings.feature');
        } else if ($tab == 'joinas') {
            return view('admin-views.business-settings.landing-page-settings.join-as');
        } else if ($tab == 'download-section') {
            return view('admin-views.business-settings.landing-page-settings.download-app-section');
        } else if ($tab == 'promotion-banner') {
            return view('admin-views.business-settings.landing-page-settings.promotion-banner');
        } else if ($tab == 'module-section') {
            $module = Helpers::get_business_settings('module_section');
            return view('admin-views.business-settings.landing-page-settings.module-section', compact('module'));
        } else if ($tab == 'image') {
            return view('admin-views.business-settings.landing-page-settings.image');
        } else if ($tab == 'background-change') {
            return view('admin-views.business-settings.landing-page-settings.backgroundChange');
        } else if ($tab == 'web-app') {
            return view('admin-views.business-settings.landing-page-settings.web-app');
        }
    }

    public function update_landing_page_settings(Request $request, $tab)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }

        if ($tab == 'text') {
            DB::table('business_settings')->updateOrInsert(['key' => 'landing_page_text'], [
                'value' => json_encode([
                    'header_title_1' => $request['header_title_1'],
                    'header_title_2' => $request['header_title_2'],
                    'header_title_3' => $request['header_title_3'],
                    'about_title' => $request['about_title'],
                    'why_choose_us' => $request['why_choose_us'],
                    'why_choose_us_title' => $request['why_choose_us_title'],
                    'module_section_title' => $request['module_section_title'],
                    'module_section_sub_title' => $request['module_section_sub_title'],
                    'refer_section_title' => $request['refer_section_title'],
                    'refer_section_sub_title' => $request['refer_section_sub_title'],
                    'refer_section_description' => $request['refer_section_description'],
                    'joinus_section_title' => $request['joinus_section_title'],
                    'joinus_section_sub_title' => $request['joinus_section_sub_title'],
                    'download_app_section_title' => $request['download_app_section_title'],
                    'download_app_section_sub_title' => $request['download_app_section_sub_title'],
                    'testimonial_title' => $request['testimonial_title'],
                    'mobile_app_section_heading' => $request['mobile_app_section_heading'],
                    'mobile_app_section_text' => $request['mobile_app_section_text'],
                    'feature_section_description' => $request['feature_section_description'],
                    'feature_section_title' => $request['feature_section_title'],
                    'newsletter_title' => $request['newsletter_title'],
                    'newsletter_sub_title' => $request['newsletter_sub_title'],
                    'contact_us_title' => $request['contact_us_title'],
                    'contact_us_sub_title' => $request['contact_us_sub_title'],
                    'footer_article' => $request['footer_article']
                ])
            ]);
            Toastr::success(translate('messages.landing_page_text_updated'));
        } else if ($tab == 'links') {
            DB::table('business_settings')->updateOrInsert(['key' => 'landing_page_links'], [
                'value' => json_encode([
                    'app_url_android_status' => $request['app_url_android_status'],
                    'app_url_android' => $request['app_url_android'],
                    'app_url_ios_status' => $request['app_url_ios_status'],
                    'app_url_ios' => $request['app_url_ios'],
                    'web_app_url_status' => $request['web_app_url_status'],
                    'web_app_url' => $request['web_app_url'],
                    'seller_app_url_status' => $request['seller_app_url_status'],
                    'seller_app_url' => $request['seller_app_url'],
                    'deliveryman_app_url_status' => $request['deliveryman_app_url_status'],
                    'deliveryman_app_url' => $request['deliveryman_app_url']
                ])
            ]);
            Toastr::success(translate('messages.landing_page_links_updated'));
        } else if ($tab == 'speciality') {
            $data = [];
            $imageName = null;
            $speciality = BusinessSetting::where('key', 'speciality')->first();
            if ($speciality) {
                $data = json_decode($speciality->value, true);
            }
            if ($request->has('image')) {
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->image->move(public_path('assets/landing/image'), $imageName);
            }
            array_push($data, [
                'img' => $imageName,
                'title' => $request->speciality_title
            ]);

            DB::table('business_settings')->updateOrInsert(['key' => 'speciality'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_speciality_updated'));
        } else if ($tab == 'joinas') {
            $data = [];
            $joinas = BusinessSetting::where('key', 'join_as_images')->first();
            if ($joinas) {
                $data = json_decode($joinas->value, true);
            }
            if ($request->has('seller_banner_bg')) {
                if (isset($data['seller_banner_bg']) && file_exists(public_path('assets/landing/image/' . $data['seller_banner_bg']))) {
                    unlink(public_path('assets/landing/image/' . $data['seller_banner_bg']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->seller_banner_bg->move(public_path('assets/landing/image'), $imageName);
                $data['seller_banner_bg'] = $imageName;
            }

            if ($request->has('deliveryman_banner_bg')) {
                if (isset($data['deliveryman_banner_bg']) && file_exists(public_path('assets/landing/image/' . $data['deliveryman_banner_bg']))) {
                    unlink(public_path('assets/landing/image/' . $data['deliveryman_banner_bg']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->deliveryman_banner_bg->move(public_path('assets/landing/image'), $imageName);
                $data['deliveryman_banner_bg'] = $imageName;
            }
            DB::table('business_settings')->updateOrInsert(['key' => 'join_as_images'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_image_updated'));
        } else if ($tab == 'download-section') {
            $data = [];
            $imageName = null;
            $download = BusinessSetting::where('key', 'download_app_section')->first();
            if ($download) {
                $data = json_decode($download->value, true);
            }
            if ($request->has('image')) {
                if (isset($data['img']) && file_exists(public_path('assets/landing/image/' . $data['img']))) {
                    unlink(public_path('assets/landing/image/' . $data['img']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->image->move(public_path('assets/landing/image'), $imageName);
                $data['img'] = $imageName;
            }

            if ($request->has('description')) {
                $data['description'] = $request->description;
            }

            DB::table('business_settings')->updateOrInsert(['key' => 'download_app_section'], [
                'value' => json_encode($data)
            ]);

            Toastr::success(translate('messages.landing_page_download_app_section_updated'));
        } else if ($tab == 'counter-section') {
            DB::table('business_settings')->updateOrInsert(['key' => 'counter_section'], [
                'value' => json_encode([
                    'app_download_count_numbers' => $request['app_download_count_numbers'],
                    'seller_count_numbers' => $request['seller_count_numbers'],
                    'deliveryman_count_numbers' => $request['deliveryman_count_numbers'],
                ])
            ]);

            Toastr::success(translate('messages.landing_page_counter_section_updated'));
        } else if ($tab == 'promotion-banner') {
            $data = [];
            $imageName = null;
            $promotion_banner = BusinessSetting::where('key', 'promotion_banner')->first();
            if ($promotion_banner) {
                $data = json_decode($promotion_banner->value, true);
            }
            if (count($data) >= 6) {
                Toastr::error(translate('messages.you_have_already_added_maximum_banner_image'));
                return back();
            }
            if ($request->has('image')) {
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->image->move(public_path('assets/landing/image'), $imageName);
            }
            array_push($data, [
                'img' => $imageName,
                'title' => $request->title,
                'sub_title' => $request->sub_title,
            ]);

            DB::table('business_settings')->updateOrInsert(['key' => 'promotion_banner'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_promotion_banner_updated'));
        } else if ($tab == 'module-section') {
            $request->validate([
                'module' => 'required',
                'description' => 'required'
            ]);
            $data = [];
            $imageName = null;
            $preImageName = null;
            $module_section = BusinessSetting::where('key', 'module_section')->first();
            if ($module_section) {
                $data = json_decode($module_section->value, true);
                if (isset($data[$request->module]['img'])) {
                    $preImageName = $data[$request->module]['img'];
                }
            }

            if ($request->has('image')) {
                if ($preImageName && file_exists(public_path('assets/landing/image') . $preImageName)) {
                    unlink(public_path('assets/landing/image') . $preImageName);
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->image->move(public_path('assets/landing/image'), $imageName);
            }

            $data[$request->module] = [
                'description' => $request->description,
                'img' => $imageName ?? $preImageName
            ];

            DB::table('business_settings')->updateOrInsert(['key' => 'module_section'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_module_section_updated'));
        } else if ($tab == 'feature') {
            $data = [];
            $imageName = null;
            $feature = BusinessSetting::where('key', 'feature')->first();
            if ($feature) {
                $data = json_decode($feature->value, true);
            }
            if ($request->has('image')) {
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->image->move(public_path('assets/landing/image'), $imageName);
            }
            array_push($data, [
                'img' => $imageName,
                'title' => $request->feature_title,
                'feature_description' => $request->feature_description
            ]);

            DB::table('business_settings')->updateOrInsert(['key' => 'feature'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_feature_updated'));
        } else if ($tab == 'testimonial') {
            $data = [];
            $imageName = null;
            $brandImageName = null;
            $testimonial = BusinessSetting::where('key', 'testimonial')->first();
            if ($testimonial) {
                $data = json_decode($testimonial->value, true);
            }
            if ($request->has('image')) {
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->image->move(public_path('assets/landing/image'), $imageName);
            }
            if ($request->has('brand_image')) {
                $brandImageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->brand_image->move(public_path('assets/landing/image'), $brandImageName);
            }
            array_push($data, [
                'img' => $imageName,
                'brand_image' => $brandImageName,
                'name' => $request->reviewer_name,
                'position' => $request->reviewer_designation,
                'detail' => $request->review,
            ]);

            DB::table('business_settings')->updateOrInsert(['key' => 'testimonial'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_testimonial_updated'));
        } else if ($tab == 'image') {
            $data = [];
            $images = BusinessSetting::where('key', 'landing_page_images')->first();
            if ($images) {
                $data = json_decode($images->value, true);
            }
            if ($request->has('top_content_image')) {
                if (isset($data['top_content_image']) && file_exists(public_path('assets/landing/image/' . $data['top_content_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['top_content_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->top_content_image->move(public_path('assets/landing/image'), $imageName);
                $data['top_content_image'] = $imageName;
            }
            if ($request->has('about_us_image')) {
                if (isset($data['about_us_image']) && file_exists(public_path('assets/landing/image/' . $data['about_us_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['about_us_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->about_us_image->move(public_path('assets/landing/image'), $imageName);
                $data['about_us_image'] = $imageName;
            }

            if ($request->has('feature_section_image')) {
                if (isset($data['feature_section_image']) && file_exists(public_path('assets/landing/image/' . $data['feature_section_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['feature_section_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->feature_section_image->move(public_path('assets/landing/image'), $imageName);
                $data['feature_section_image'] = $imageName;
            }
            if ($request->has('mobile_app_section_image')) {
                if (isset($data['mobile_app_section_image']) && file_exists(public_path('assets/landing/image/' . $data['mobile_app_section_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['mobile_app_section_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->mobile_app_section_image->move(public_path('assets/landing/image'), $imageName);
                $data['mobile_app_section_image'] = $imageName;
            }

            if ($request->has('contact_us_image')) {
                if (isset($data['contact_us_image']) && file_exists(public_path('assets/landing/image/' . $data['contact_us_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['contact_us_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->contact_us_image->move(public_path('assets/landing/image'), $imageName);
                $data['contact_us_image'] = $imageName;
            }

            DB::table('business_settings')->updateOrInsert(['key' => 'landing_page_images'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.landing_page_image_updated'));
        } else if ($tab == 'background-change') {
            DB::table('business_settings')->updateOrInsert(['key' => 'backgroundChange'], [
                'value' => json_encode([
                    'primary_1_hex' => $request['header-bg'],
                    'primary_1_rgb' => Helpers::hex_to_rbg($request['header-bg']),
                    'primary_2_hex' => $request['footer-bg'],
                    'primary_2_rgb' => Helpers::hex_to_rbg($request['footer-bg']),
                ])
            ]);
            Toastr::success(translate('messages.background_updated'));
        } else if ($tab == 'web-app') {
            $data = [];
            $images = BusinessSetting::where('key', 'web_app_landing_page_settings')->first();
            if ($images) {
                $data = json_decode($images->value, true);
            }
            if ($request->has('top_content_image')) {
                if (isset($data['top_content_image']) && file_exists(public_path('assets/landing/image/' . $data['top_content_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['top_content_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->top_content_image->move(public_path('assets/landing/image'), $imageName);
                $data['top_content_image'] = $imageName;
            }

            if ($request->has('mobile_app_section_image')) {
                if (isset($data['mobile_app_section_image']) && file_exists(public_path('assets/landing/image/' . $data['mobile_app_section_image']))) {
                    unlink(public_path('assets/landing/image/' . $data['mobile_app_section_image']));
                }
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . ".png";
                $request->mobile_app_section_image->move(public_path('assets/landing/image'), $imageName);
                $data['mobile_app_section_image'] = $imageName;
            }
            DB::table('business_settings')->updateOrInsert(['key' => 'web_app_landing_page_settings'], [
                'value' => json_encode($data)
            ]);
            Toastr::success(translate('messages.web_app_landing_page_settings'));
        }
        return back();
    }

    public function delete_landing_page_settings($tab, $key)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }

        $item = BusinessSetting::where('key', $tab)->first();
        $data = $item ? json_decode($item->value, true) : null;
        if ($data && array_key_exists($key, $data)) {
            if ($data[$key]['img'] && file_exists(public_path('assets/landing/image') . $data[$key]['img'])) {
                unlink(public_path('assets/landing/image') . $data[$key]['img']);
            }
            array_splice($data, $key, 1);

            $item->value = json_encode($data);
            $item->save();
            Toastr::success(translate('messages.' . $tab) . ' ' . translate('messages.deleted'));
            return back();
        }
        Toastr::error(translate('messages.not_found'));
        return back();
    }

    public function currency_index()
    {
        return view('admin-views.business-settings.currency-index');
    }

    public function currency_store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        Currency::create([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);
        Toastr::success(translate('messages.currency_added_successfully'));
        return back();
    }

    public function currency_edit($id)
    {
        $currency = Currency::find($id);
        return view('admin-views.business-settings.currency-update', compact('currency'));
    }

    public function currency_update(Request $request, $id)
    {
        Currency::where(['id' => $id])->update([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);
        Toastr::success(translate('messages.currency_updated_successfully'));
        return redirect('store-panel/business-settings/currency-add');
    }

    public function currency_delete($id)
    {
        Currency::where(['id' => $id])->delete();
        Toastr::success(translate('messages.currency_deleted_successfully'));
        return back();
    }

    public function terms_and_conditions()
    {
        $tnc = BusinessSetting::where(['key' => 'terms_and_conditions'])->first();
        if ($tnc == false) {
            BusinessSetting::insert([
                'key' => 'terms_and_conditions',
                'value' => ''
            ]);
        }
        return view('admin-views.business-settings.terms-and-conditions', compact('tnc'));
    }

    public function terms_and_conditions_update(Request $request)
    {
        BusinessSetting::where(['key' => 'terms_and_conditions'])->update([
            'value' => $request->tnc
        ]);

        Toastr::success(translate('messages.terms_and_condition_updated'));
        return back();
    }

    public function privacy_policy()
    {
        $data = BusinessSetting::where(['key' => 'privacy_policy'])->first();
        if ($data == false) {
            $data = [
                'key' => 'privacy_policy',
                'value' => '',
            ];
            BusinessSetting::insert($data);
        }
        return view('admin-views.business-settings.privacy-policy', compact('data'));
    }

    public function privacy_policy_update(Request $request)
    {
        BusinessSetting::where(['key' => 'privacy_policy'])->update([
            'value' => $request->privacy_policy,
        ]);

        Toastr::success(translate('messages.privacy_policy_updated'));
        return back();
    }

    public function about_us()
    {
        $data = BusinessSetting::where(['key' => 'about_us'])->first();
        if ($data == false) {
            $data = [
                'key' => 'about_us',
                'value' => '',
            ];
            BusinessSetting::insert($data);
        }
        return view('admin-views.business-settings.about-us', compact('data'));
    }

    public function about_us_update(Request $request)
    {
        BusinessSetting::where(['key' => 'about_us'])->update([
            'value' => $request->about_us,
        ]);

        Toastr::success(translate('messages.about_us_updated'));
        return back();
    }

    public function refund()
    {
        $data = BusinessSetting::where(['key' => 'refund'])->first();
        if ($data == false) {
            $data = [
                'key' => 'refund',
                'value' => json_encode([
                    'status' => 0,
                    'value' => '',
                ]),
            ];
            BusinessSetting::insert($data);
        }
        return view('admin-views.business-settings.refund', compact('data'));
    }

    public function refund_update(Request $request)
    {
        BusinessSetting::where(['key' => 'refund'])->update([
            'value' => json_encode([
                'status' => $request->status,
                'value' => $request->refund,
            ]),
        ]);

        Toastr::success(translate('messages.refund_updated'));
        return back();
    }

    public function cancelation()
    {
        $data = BusinessSetting::where(['key' => 'cancelation'])->first();
        if ($data == false) {
            $data = [
                'key' => 'cancelation',
                'value' => json_encode([
                    'status' => 0,
                    'value' => '',
                ]),
            ];
            BusinessSetting::insert($data);
        }
        return view('admin-views.business-settings.cancelation', compact('data'));
    }

    public function cancelation_update(Request $request)
    {
        BusinessSetting::where(['key' => 'cancelation'])->update([
            'value' => json_encode([
                'status' => $request->status,
                'value' => $request->cancelation,
            ]),
        ]);

        Toastr::success(translate('messages.cancelation_updated'));
        return back();
    }

    public function shipping_policy()
    {
        $data = BusinessSetting::where(['key' => 'shipping_policy'])->first();
        if ($data == false) {
            $data = [
                'key' => 'shipping_policy',
                'value' => json_encode([
                    'status' => 0,
                    'value' => '',
                ]),
            ];
            BusinessSetting::insert($data);
        }
        return view('admin-views.business-settings.shipping_policy', compact('data'));
    }

    public function shipping_policy_update(Request $request)
    {
        BusinessSetting::where(['key' => 'shipping_policy'])->update([
            'value' => json_encode([
                'status' => $request->status,
                'value' => $request->shipping_policy,
            ]),
        ]);

        Toastr::success(translate('messages.shipping_policy_updated'));
        return back();
    }

    public function fcm_index()
    {
        $fcm_credentials = Helpers::get_business_settings('fcm_credentials');
        return view('admin-views.business-settings.fcm-index', compact('fcm_credentials'));
    }

    public function update_fcm(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'fcm_project_id'], [
            'value' => $request['projectId']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'push_notification_key'], [
            'value' => $request['push_notification_key']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'fcm_credentials'], [
            'value' => json_encode([
                'apiKey' => $request->apiKey,
                'authDomain' => $request->authDomain,
                'projectId' => $request->projectId,
                'storageBucket' => $request->storageBucket,
                'messagingSenderId' => $request->messagingSenderId,
                'appId' => $request->appId,
                'measurementId' => $request->measurementId
            ])
        ]);
        Toastr::success(translate('messages.settings_updated'));
        return back();
    }

    public function update_fcm_messages(Request $request)
    {
        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_pending_message')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'order_pending_message';
        $notification->module_type = $request->module_type;
        $notification->message = $request->pending_message[array_search('en', $request->lang)];
        $notification->status = $request['pending_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->pending_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->pending_message[$index]]
                );
            }
        }

        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_confirmation_msg')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'order_confirmation_msg';
        $notification->module_type = $request->module_type;
        $notification->message = $request->confirm_message[array_search('en', $request->lang)];
        $notification->status = $request['confirm_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->confirm_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->confirm_message[$index]]
                );
            }
        }
        if($request->module_type != 'parcel'){

        
            $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_processing_message')->first();
            if($notification == null){
                $notification = new NotificationMessage();
            }
    
            $notification->key = 'order_processing_message';
            $notification->module_type = $request->module_type;
            $notification->message = $request->processing_message[array_search('en', $request->lang)];
            $notification->status = $request['processing_status'] == 1 ? 1 : 0;
            $notification->save();
            foreach($request->lang as $index=>$key)
            {
                if($request->processing_message[$index] && $key != 'en')
                {
                    Translation::updateOrInsert(
                        ['translationable_type'  => 'App\Models\NotificationMessage',
                            'translationable_id'    => $notification->id,
                            'locale'                => $key,
                            'key'                   => $notification->key],
                        ['value'                 => $request->processing_message[$index]]
                    );
                }
            }

            $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_handover_message')->first();
            if($notification == null){
                $notification = new NotificationMessage();
            }

            $notification->key = 'order_handover_message';
            $notification->module_type = $request->module_type;
            $notification->message = $request->order_handover_message[array_search('en', $request->lang)];
            $notification->status = $request['order_handover_message_status'] == 1 ? 1 : 0;
            $notification->save();
            foreach($request->lang as $index=>$key)
            {
                if($request->order_handover_message[$index] && $key != 'en')
                {
                    Translation::updateOrInsert(
                        ['translationable_type'  => 'App\Models\NotificationMessage',
                            'translationable_id'    => $notification->id,
                            'locale'                => $key,
                            'key'                   => $notification->key],
                        ['value'                 => $request->order_handover_message[$index]]
                    );
                }
            }

            $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_refunded_message')->first();
            if($notification == null){
                $notification = new NotificationMessage();
            }

            $notification->key = 'order_refunded_message';
            $notification->module_type = $request->module_type;
            $notification->message = $request->order_refunded_message[array_search('en', $request->lang)];
            $notification->status = $request['order_refunded_message_status'] == 1 ? 1 : 0;
            $notification->save();
            foreach($request->lang as $index=>$key)
            {
                if($request->order_refunded_message[$index] && $key != 'en')
                {
                    Translation::updateOrInsert(
                        ['translationable_type'  => 'App\Models\NotificationMessage',
                            'translationable_id'    => $notification->id,
                            'locale'                => $key,
                            'key'                   => $notification->key],
                        ['value'                 => $request->order_refunded_message[$index]]
                    );
                }
            }
            
            $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','refund_request_canceled')->first();

            if($notification == null){
                $notification = new NotificationMessage();
            }

            $notification->key = 'refund_request_canceled';
            $notification->module_type = $request->module_type;
            $notification->message = $request->refund_request_canceled[array_search('en', $request->lang)];
            $notification->status = $request['refund_request_canceled_status'] == 1 ? 1 : 0;
            $notification->save();
            foreach($request->lang as $index=>$key)
            {
                if($request->refund_request_canceled[$index] && $key != 'en')
                {
                    Translation::updateOrInsert(
                        ['translationable_type'  => 'App\Models\NotificationMessage',
                            'translationable_id'    => $notification->id,
                            'locale'                => $key,
                            'key'                   => $notification->key],
                        ['value'                 => $request->refund_request_canceled[$index]]
                    );
                }
            }
        }
    
    
        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','out_for_delivery_message')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'out_for_delivery_message';
        $notification->module_type = $request->module_type;
        $notification->message = $request->out_for_delivery_message[array_search('en', $request->lang)];
        $notification->status = $request['out_for_delivery_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->out_for_delivery_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->out_for_delivery_message[$index]]
                );
            }
        }
    
        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_delivered_message')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'order_delivered_message';
        $notification->module_type = $request->module_type;
        $notification->message = $request->delivered_message[array_search('en', $request->lang)];
        $notification->status = $request['delivered_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->delivered_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->delivered_message[$index]]
                );
            }
        }
    
        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','delivery_boy_assign_message')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'delivery_boy_assign_message';
        $notification->module_type = $request->module_type;
        $notification->message = $request->delivery_boy_assign_message[array_search('en', $request->lang)];
        $notification->status = $request['delivery_boy_assign_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->delivery_boy_assign_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->delivery_boy_assign_message[$index]]
                );
            }
        }
    
        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','delivery_boy_delivered_message')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'delivery_boy_delivered_message';
        $notification->module_type = $request->module_type;
        $notification->message = $request->delivery_boy_delivered_message[array_search('en', $request->lang)];
        $notification->status = $request['delivery_boy_delivered_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->delivery_boy_delivered_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->delivery_boy_delivered_message[$index]]
                );
            }
        }
    
        $notification = NotificationMessage::where('module_type',$request->module_type)->where('key','order_cancled_message')->first();
        if($notification == null){
            $notification = new NotificationMessage();
        }

        $notification->key = 'order_cancled_message';
        $notification->module_type = $request->module_type;
        $notification->message = $request->order_cancled_message[array_search('en', $request->lang)];
        $notification->status = $request['order_cancled_message_status'] == 1 ? 1 : 0;
        $notification->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->order_cancled_message[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\NotificationMessage',
                        'translationable_id'    => $notification->id,
                        'locale'                => $key,
                        'key'                   => $notification->key],
                    ['value'                 => $request->order_cancled_message[$index]]
                );
            }
        }


        Toastr::success(translate('messages.message_updated'));
        return back();
    }

    public function location_index()
    {
        return view('admin-views.business-settings.location-index');
    }

    public function location_setup(Request $request)
    {
        $store = Helpers::get_store_id();
        $store->latitude = $request['latitude'];
        $store->longitude = $request['longitude'];
        $store->save();

        Toastr::success(translate('messages.settings_updated'));
        return back();
    }

    public function config_setup()
    {
        return view('admin-views.business-settings.config');
    }

    public function config_update(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key'], [
            'value' => $request['map_api_key']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key_server'], [
            'value' => $request['map_api_key_server']
        ]);

        Toastr::success(translate('messages.config_data_updated'));
        return back();
    }

    public function toggle_settings($key, $value)
    {
        DB::table('business_settings')->updateOrInsert(['key' => $key], [
            'value' => $value
        ]);

        Toastr::success(translate('messages.app_settings_updated'));
        return back();
    }

    public function viewSocialLogin()
    {
        $data = BusinessSetting::where('key', 'social_login')->first();
        if (!$data) {
            Helpers::insert_business_settings_key('social_login', '[{"login_medium":"google","client_id":"","client_secret":"","status":"0"},{"login_medium":"facebook","client_id":"","client_secret":"","status":""}]');
            $data = BusinessSetting::where('key', 'social_login')->first();
        }
        $socialLoginServices = json_decode($data->value, true);
        return view('admin-views.business-settings.social-login.view', compact('socialLoginServices'));
    }

    public function updateSocialLogin($service, Request $request)
    {
        $socialLogin = BusinessSetting::where('key', 'social_login')->first();
        $credential_array = [];
        foreach (json_decode($socialLogin['value'], true) as $key => $data) {
            if ($data['login_medium'] == $service) {
                $cred = [
                    'login_medium' => $service,
                    'client_id' => $request['client_id'],
                    'client_secret' => $request['client_secret'],
                    'status' => $request['status'],
                ];
                array_push($credential_array, $cred);
            } else {
                array_push($credential_array, $data);
            }
        }
        BusinessSetting::where('key', 'social_login')->update([
            'value' => $credential_array
        ]);

        Toastr::success(translate('messages.credential_updated', ['service' => $service]));
        return redirect()->back();
    }

    //recaptcha
    public function recaptcha_index(Request $request)
    {
        return view('admin-views.business-settings.recaptcha-index');
    }

    public function recaptcha_update(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
            'key' => 'recaptcha',
            'value' => json_encode([
                'status' => $request['status'],
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('messages.updated_successfully'));
        return back();
    }
    //Send Mail
    public function send_mail(Request $request)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }
        $response_flag = 0;
        try {
            Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
            $response_flag = 1;
        } catch (\Exception $exception) {
            info($exception);
            $response_flag = 2;
        }

        return response()->json(['success' => $response_flag]);
    }


    public function site_direction(Request $request){
        if($request->status == 1){
            DB::table('business_settings')->updateOrInsert(['key' => 'site_direction'], [
                'value' => 'ltr'
            ]);
        } else
        {
            DB::table('business_settings')->updateOrInsert(['key' => 'site_direction'], [
                'value' => 'rtl'
            ]);
        }
        return ;
    }
}

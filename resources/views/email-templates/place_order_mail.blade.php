<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ translate('messages.order_place') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

        /**
   * Avoid browser level font resizing.
   * 1. Windows Mobile
   * 2. iOS / OSX
   */
        body {
            font-family: 'Roboto', sans-serif;
        }

        body,
        table,
        td,
        a {
            -ms-text-size-adjust: 100%;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
        }

        /**
   * Remove extra space added to tables and cells in Outlook.
   */
        table,
        td {
            mso-table-rspace: 0pt;
            mso-table-lspace: 0pt;
        }

        /**
   * Better fluid images in Internet Explorer.
   */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /**
   * Remove blue links for iOS devices.
   */
        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }

        /**
   * Fix centering issues in Android 4.4.
   */
        /* div[style*="margin: 16px 0;"] {
    margin: 0 !important;
  } */
        /* body {
    width: 100% !important;
    height: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
  } */
        /**
   * Collapse table borders to avoid space between cells.
   */
        table {
            border-collapse: collapse !important;
        }

        a {
            color: #1a82e2;
        }

        img {
            height: auto;
            line-height: 100%;
            text-decoration: none;
            border: 0;
            outline: none;
        }

    </style>
</head>

<body style="background-color: #ececec;margin:0;padding:0">
    <?php
    use App\Models\BusinessSetting;
    $company_phone = BusinessSetting::where('key', 'phone')->first()->value;
    $company_email = BusinessSetting::where('key', 'email_address')->first()->value;
    $company_name = BusinessSetting::where('key', 'business_name')->first()->value;
    $logo = BusinessSetting::where('key', 'logo')->first()->value;
    $company_address = BusinessSetting::where('key', 'address')->first()->value;
    $company_mobile_logo = $logo;
    $company_links = json_decode(BusinessSetting::where('key', 'landing_page_links')->first()->value, true);
    ?>
    <div style="width:650px;margin:auto; background-color:#ececec;height:50px;">
    </div>
    <div style="width:650px;margin:auto; background-color:white;margin-top:100px;
            padding-top:40px;padding-bottom:40px;border-radius: 3px;">
        <table
            style="background-color: rgb(255, 255, 255);width: 90%;margin:auto;height:72px; border-bottom:1px ridge;">
            <tbody>
                <tr>
                    <td>
                        <h2>{{ translate('messages.thanks_for_the_order') }}</h2>
                        <h3 style="color:green;">{{ translate('messages.Your_order_ID') }} : {{ $order_id }}</h3>
                    </td>
                    <td>
                        <div style="text-align: end; margin-inline-end:15px;">
                            <img style="max-width:250px;border:0;"
                                src="{{ asset('/storage/app/public/business/' . $logo) }}" title=""
                                class="sitelogo" width="60%" alt="Image" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        @php($order = \App\Models\Order::find($order_id))
        <table style="background-color: rgb(255, 255, 255);width: 90%;margin:auto; padding-bottom:20px;">
            <tbody>
                <tr style="width: 100%;">
                    @if ($order->order_type != 'parcel')
                        <td style="width:50%;vertical-align: top; margin-top:5px;">
                            <div style="text-align:start;margin-top:10px;">
                                <span style="color: #130505 !important;text-transform: capitalize;font-weight: bold;">
                                    {{ translate('messages.store_info') }} </span><br>
                                @if ($order->store)
                                    <div style="display:flex; align-items:center;margin-top:10px;">
                                        <img style="border:0;border-radius:50%;"
                                            src="{{ asset('/storage/app/public/store/' . $order->store->logo) }}" title="Logo"
                                            class="sitelogo" width="20%" alt="Image" />
                                        <span style="padding-inline-start: 5px;">{{ $order->store->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td style="width:50%;vertical-align: top;">
                            <div style="text-align:end;margin-top:10px;">
                                <span
                                    style="color: #130505 !important;text-transform: capitalize;font-weight: bold;">{{ translate('messages.payment_details') }}
                                </span><br>
                                <div style="margin-top: 10px;">
                                    <span
                                        style="color: #414141 !important ; text-transform: capitalize;">{{ str_replace('_', ' ', $order->payment_method) }}</span><br>
                                    <span style="color: {{ $order->payment_status == 'paid' ? 'green' : 'red' }};">
                                        {{ $order->payment_status }}
                                    </span><br>
                                    <span style="color: #414141 !important ; text-transform: capitalize;">
                                        {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                                    </span>
                                </div>
                            </div>
                        </td>     
                    @else
                    <td style="width:100%;vertical-align: top;">
                        <div style="margin-top:10px;">
                            <span
                                style="color: #130505 !important;text-transform: capitalize;font-weight: bold;">{{ translate('messages.payment_details') }}:
                            </span>
                            <div style="margin-top: 10px; background: aliceblue;">
                                <span
                                    style="color: #414141 !important ; text-transform: capitalize; width: 33.33%; display: inline-block;">{{ str_replace('_', ' ', $order->payment_method) }}
                                </span>
                                <span style="color: {{ $order->payment_status == 'paid' ? 'green' : 'red' }}; width: 33.33%; display: inline-block;">
                                    {{ $order->payment_status }}
                                </span>
                                <span style="color: #414141 !important ; text-transform: capitalize;">
                                    {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                                </span>
                            </div>
                        </div>
                    </td>                                         
                    @endif
                </tr>
            </tbody>
        </table>
        <?php
        $subtotal = 0;
        $total = 0;
        $sub_total = 0;
        $total_tax = 0;
        $total_shipping_cost = $order->delivery_charge;
        $total_discount_on_product = 0;
        $extra_discount = 0;
        $total_addon_price = 0;
        ?>
        <div style="background-color: rgb(248, 248, 248); width: 90%;margin:auto;margin-top:30px;">
            <div style="padding:20px;">
                <table style="width: 100%; ">
                    <tbody style="">
                    @if ($order->order_type=='parcel')
                        <tr style="border-bottom: 1px ridge;text-transform: capitalize;">
                            <th style="padding-bottom: 8px;width:5%;">{{ translate('messages.sl') }}</th>
                            <th style="padding-bottom: 8px;width:50%;">{{ translate('messages.parcel_category') }}</th>
                            <th style="padding-bottom: 8px;width:15%">{{ translate('messages.distance') }}</th>
                            <th style="padding-bottom: 8px;width:30%;">{{ translate('messages.delivery_charge') }}</th>
                        </tr>

                            <tr style="text-align: center; border-bottom: 1px ridge;">
                                <td style="padding:5px;">1</td>
                                <td style="padding:5px;">
                                    <span style="font-weight:bold">
                                        {{ Str::limit($order->parcel_category?$order->parcel_category->name:translate('messages.parcel_category_not_found'), 25, '...') }}
                                    </span>
                                    <br>
                                    <span style="font-size: 12px;">{{ $order->parcel_category?$order->parcel_category->description:translate('messages.parcel_category_not_found') }}</span>
                                </td>
                                <td style="padding:5px;"> {{ $order->distance }} {{translate('messages.km')}} </td>
                                <td style="padding:5px;">{{ \App\CentralLogics\Helpers::format_currency($order->delivery_charge) }}</td>
                            </tr>
                    @else
                        <tr style="border-bottom: 1px ridge;text-transform: capitalize;">
                            <th style="padding-bottom: 8px;width:10%;">{{ translate('messages.sl') }}</th>
                            <th style="padding-bottom: 8px;width:40%;">{{ translate('messages.Ordered_Items') }}</th>
                            <th style="padding-bottom: 8px;width:15%">{{ translate('messages.Unit_price') }}</th>
                            <th style="padding-bottom: 8px;width:15%;">{{ translate('messages.qty') }}</th>
                            <th style="padding-bottom: 8px;width:20%;">{{ translate('messages.total') }}</th>
                        </tr>
                        @foreach ($order->details as $key => $details)
                            <?php
                            $subtotal = $details['price'] * $details->quantity;
                            $item_details = json_decode($details->item_details, true);
                            ?>
                            <tr style="text-align: center; border-bottom: 1px ridge;">
                                <td style="padding:5px;">{{ $key + 1 }}</td>
                                <td style="padding:5px;">
                                    <span style="font-size: 14px;">
                                        {{ Str::limit($item_details['name'], 40, '...') }}
                                    </span>
                                    <br>
                                    @if (count(json_decode($details['variation'], true)) > 0)
                                        <span style="font-size: 12px;">
                                            {{ translate('messages.variation') }} :
                                            {{ json_decode($details['variation'], true)[0]['type'] }}
                                        </span>
                                    @endif
                                    @foreach (json_decode($details['add_ons'], true) as $key2 => $addon)
                                        @if ($key2 == 0)
                                            <br><span style="font-size: 12px;"><u>{{ translate('messages.addons') }}
                                                </u></span>
                                        @endif
                                        <div style="font-size: 12px;">
                                            <span>{{ Str::limit($addon['name'], 20, '...') }} : </span>
                                            <span class="font-weight-bold">
                                                {{ $addon['quantity'] }} x
                                                {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                            </span>
                                        </div>
                                        @php($total_addon_price += $addon['price'] * $addon['quantity'])
                                    @endforeach
                                </td>
                                <td style="padding:5px;">
                                    {{ \App\CentralLogics\Helpers::format_currency($details['price']) }}</td>
                                <td style="padding:5px;">{{ $details->quantity }}</td>
                                <td style="padding:5px;">{{ \App\CentralLogics\Helpers::format_currency($subtotal) }}
                                </td>
                            </tr>
                            <?php
                            $sub_total += $details['price'] * $details['quantity'];
                            $total_tax += $details['tax'];
                            $total_discount_on_product += $details['discount'];
                            $total += $subtotal;
                            ?>
                        @endforeach                    
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
        <table style="background-color: rgb(255, 255, 255);width: 90%;margin:auto;margin-top:30px;">
            <tr>
                <th style="text-align: start; vertical-align: auto;">
                </th>
                <td style="text-align: end">
                    <table style="width: 46%;margin-inline-start:41%; display: inline;text-transform: capitalize; ">
                        <tbody>
                        @if ($order->order_type !='parcel')
                            <tr>
                                <th><b>{{ translate('messages.item') }} {{ translate('messages.price') }} : </b></th>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($sub_total) }}</td>
                            </tr>
                            <tr>
                                <th><b>{{ translate('messages.addon') }} {{ translate('messages.cost') }} : </b></th>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($total_addon_price) }}</td>
                            </tr>
                            <tr>
                                <th><b>{{ translate('messages.subtotal') }} : </b></th>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($sub_total + $total_addon_price) }}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ translate('messages.tax') }} : </td>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($total_tax) }}</td>
                            </tr>
                            <tr>
                                <td>{{ translate('messages.delivery_charge') }} : </td>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($order->delivery_charge) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td>{{ translate('messages.discount') }} : </td>
                                <td>
                                    -
                                    {{ \App\CentralLogics\Helpers::format_currency($order->store_discount_amount) }}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ translate('messages.coupon_discount') }} : </td>
                                <td>
                                    -
                                    {{ \App\CentralLogics\Helpers::format_currency($order->coupon_discount_amount) }}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ translate('messages.delivery_man_tips') }} : </td>
                                <td>
                                    +
                                    {{ \App\CentralLogics\Helpers::format_currency($order->dm_tips) }}
                                </td>
                            </tr>
                        @endif
                            <tr class="bg-primary">
                                <th class="text-left"><b class="text-white">{{ translate('messages.total') }} : </b>
                                </th>
                                <td class="text-white">
                                    {{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div style="padding:5px;width:650px;margin:auto;margin-top:5px; margin-bottom:50px;">
        <table style="margin:auto;width:90%; color:#777777;">
            <tbody style="text-align: center;">
                <tr>
                    @php($social_media = \App\Models\SocialMedia::active()->get())
                    @if (isset($social_media))
                        <th style="width: 100%;">
                            @foreach ($social_media as $social)
                                <div style="display:inline-block;">
                                    <a href="{{ $social->link }}" target=”_blank”>
                                        <img src="{{ asset('public/assets/admin/img/' . $social->name . '.png') }}"
                                            alt="" style="height: 14px; width:14px; padding: 0px 3px 0px 5px;">
                                    </a>
                                </div>
                            @endforeach
                        </th>
                    @endif
                </tr>
                <tr>
                    <th>
                        <div style="font-weight: 400;font-size: 11px;line-height: 22px;color: #242A30;"><span
                                style="margin-inline-end:5px;"> <a href="tel:{{ $company_phone }}"
                                    style="text-decoration: none; color: inherit;">{{ translate('messages.phone') }}:
                                    {{ $company_phone }}</a></span> <span><a href="mailto:{{ $company_email }}"
                                    style="text-decoration: none; color: inherit;">{{ translate('messages.email') }}:
                                    {{ $company_email }}</a></span></div>
                        @if ($company_links['web_app_url_status'])
                            <div style="font-weight: 400;font-size: 11px;line-height: 22px;color: #242A30;">
                                <a href="{{ $company_links['web_app_url'] }}"
                                    style="text-decoration: none; color: inherit;">{{ $company_links['web_app_url'] }}</a>
                            </div>
                        @endif
                        <div style="font-weight: 400;font-size: 11px;line-height: 22px;color: #242A30;">
                            {{ $company_address }}</div>
                        <span
                            style="font-weight: 400;font-size: 10px;line-height: 22px;color: #242A30;">{{ translate('messages.copyright', ['year' => date('Y'), 'title' => $company_name]) }}</span>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>

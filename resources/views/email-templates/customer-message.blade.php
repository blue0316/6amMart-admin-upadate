<?php
use App\Models\BusinessSetting;
$company_phone =BusinessSetting::where('key', 'phone')->first()->value;
$company_email =BusinessSetting::where('key', 'email_address')->first()->value;
$company_name =BusinessSetting::where('key', 'business_name')->first()->value;
$logo =BusinessSetting::where('key', 'logo')->first()->value;
$company_address =BusinessSetting::where('key', 'address')->first()->value;
$company_mobile_logo = $logo;
$company_links = json_decode(BusinessSetting::where('key','landing_page_links')->first()->value, true);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>{{translate('messages.Reply_form_'.$company_name)}}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style type="text/css">

  @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');
  /**
   * Avoid browser level font resizing.
   * 1. Windows Mobile
   * 2. iOS / OSX
   */
   body{
    font-family: 'Roboto', sans-serif;
   }
  body,
  table,
  td,
  a {
    -ms-text-size-adjust: 100%; /* 1 */
    -webkit-text-size-adjust: 100%; /* 2 */
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
  table {
    border-collapse: collapse !important;
  }
  a {
    color: #1A82E2;
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
<body style="background-color: #ECECEC;margin:0;padding:0">
<div style="width:650px;margin:auto; background-color:#ececec;height:50px;">
</div>
<div style="width:650px;margin:auto; background-color:white;margin-top:100px;
            padding-top:40px;padding-bottom:40px;border-radius: 3px;">
    <table style="background-color: rgb(255, 255, 255);width: 90%;margin:auto;height:72px; border-bottom:1px ridge;">
        <tbody>
            <tr>
                <td>
                    <h3 style="color:green;">{{translate('messages.Dear')}}, {{ $name }}</h3>
                </td>
                <td>
                    <div style="text-align: end; margin-inline-end:15px;">
                        <img class="w-100" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                        src="{{ asset('storage/app/public/business/'. $logo) }}" alt="">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="background-color: rgb(248, 248, 248); width: 90%;margin:auto;margin-top:30px;">
        <div style="padding:20px;">
            <p>{!! $body ?? '' !!}</p>
            </p>
        </div>
    </div>
</div>
<div style="padding:5px;width:650px;margin:auto;margin-top:5px; margin-bottom:50px;">
    <table style="margin:auto;width:90%; color:#777777;">
        <tbody style="text-align: center;">
            <tr>
                @php($social_media = \App\Models\SocialMedia::active()->get())
                @if(isset($social_media))
                    <th style="width: 100%;">
                        @foreach ($social_media as $item)
                        <div style="display:inline-block;">
                            <a href="{{$item->link}}" target=”_blank”>
                                <img src="{{ asset('public/assets/landing/img/footer/'. $item->name.'.svg') }}"  style="height: 14px; width:14px; padding: 0px 3px 0px 5px;" alt="">
                            </a>
                        </div>
                        @endforeach
                    </th>
                @endif
            </tr>
            <tr>
                <th >
                    <div style="font-weight: 400;font-size: 11px;line-height: 22px;color: #242A30;"><span style="margin-inline-end:5px;"> <a href="tel:{{$company_phone}}" style="text-decoration: none; color: inherit;">{{translate('messages.phone')}}: {{$company_phone}}</a></span> <span><a href="mailto:{{$company_email}}" style="text-decoration: none; color: inherit;">{{translate('messages.email')}}: {{$company_email}}</a></span></div>
                    @if ($company_links['web_app_url_status'])
                    <div style="font-weight: 400;font-size: 11px;line-height: 22px;color: #242A30;">
                        <a href="{{$company_links['web_app_url']}}" style="text-decoration: none; color: inherit;">{{$company_links['web_app_url']}}</a></div>
                    @endif
                    <div style="font-weight: 400;font-size: 11px;line-height: 22px;color: #242A30;">{{$company_address}}</div>
                    <span style="font-weight: 400;font-size: 10px;line-height: 22px;color: #242A30;">{{translate('messages.All copy right reserved',['year'=>date('Y'),'title'=>$company_name])}}</span>
                </th>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
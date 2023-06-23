<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ translate('messages.welcome') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Helvetica:700,400');
        body{font-family:Helvetica,sans-serif;font-style:normal}a,body,table,td{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}table,td{mso-table-rspace:0pt;mso-table-lspace:0pt}img{-ms-interpolation-mode:bicubic}a[x-apple-data-detectors]{font-family:inherit!important;font-size:inherit!important;font-weight:inherit!important;line-height:inherit!important;color:inherit!important;text-decoration:none!important}table{border-collapse:collapse!important}.congrats-box{margin-top:47px;margin-bottom:38px}
    </style>
</head>
<body style="background-color: #ececec;margin:0;padding:0;text-align:center;">
    <?php
    use App\Models\BusinessSetting;
    $company_phone = BusinessSetting::where('key', 'phone')->first()->value;
    $company_email = BusinessSetting::where('key', 'email_address')->first()->value;
    $company_name = BusinessSetting::where('key', 'business_name')->first()->value;
    $company_address = BusinessSetting::where('key', 'address')->first()->value;
    $logo = BusinessSetting::where('key', 'logo')->first()->value;
    $company_mobile_logo = $logo;
    $company_links = json_decode(BusinessSetting::where('key', 'landing_page_links')->first()->value, true);
    ?>
    <div style="width:595px;margin:auto; background-color:white;margin-top:100px;
            padding-top:40px;padding-bottom:40px;border-radius: 3px; text-align:center; ">

        <img src="{{ asset('/storage/app/public/business/' . $logo) }}" alt="{{ $company_name }}"
            style="height: 80px; width:auto;">

        <div class="congrats-box">
            <span
                style="font-weight: 700;font-size: 26px;color: #000000;text-transform: uppercase; line-height: 135.5%; display:block; margin-bottom:5px;">{{ translate('messages.congratulations') }}</span>
            <span
                style="font-weight: 700;font-size: 14px;line-height: 135.5%;text-align: center;color: #727272; display:block;">{{ translate('messages.account_created') }}</span>
        </div>
        <span
            style="font-weight: bold;font-size: 16px;line-height: 135.5%;text-align: center;color: #182E4B; display:block; margin-bottom: 5px;">{{ translate('messages.dear') }}
            {{ $name }}</span>
        <span
            style="font-weight: 400;font-size: 14px;line-height: 135.5%;color: #182E4B;display:block; margin-bottom:34px;">{{ translate('messages.thanks') }}
            <span style="color: #EF7822;">{{ $company_name }}!</span></span>
        <span
            style="font-weight: 400;font-size: 12px;line-height: 135.5%;color: #5D6774; display:block; margin-bottom:20px;">{{ translate('messages.welcome_text') }}
        </span>
        @if ($type)
        <span style="font-weight: 400;font-size: 14px;line-height: 135.5%;color: #182E4B;display:block; margin-bottom:34px;">{{translate('Default login password: ')}} <span style="color: #EF7822;">password</span></span>
        @endif
        @if ($company_links['web_app_url_status'])
            <a href="{{ $company_links['web_app_url'] }}" target="_blank"
                style="width: 100px;height: 40px;left: 247px;top: 321px;background: #EF7822;border-radius: 5px; padding: 10px;text-decoration: none;color: white;">{{ translate('messages.buy_now') }}</a>
        @endif
        <span
            style="font-weight: 400;font-size: 12px;line-height: 135.5%;color: #5D6774;display:block;margin-top:43px;">{{ translate('messages.feedback') }}
            <a href="mailto:{{ $company_email }}" class="email">{{ $company_email }}</a>
        </span>
    </div>

    <div style="padding:5px;width:650px;margin:auto;margin-top:5px; margin-bottom:50px;">
        <table style="margin:auto;width:90%; color:#777777;">
            <tbody style="text-align: center;">

                <tr>
                    @php($social_media = \App\Models\SocialMedia::active()->get())

                    @if (isset($social_media))
                        <th style="width: 100%;">
                            @foreach ($social_media as $item)
                                <div style="display:inline-block;">
                                    <a href="{{ $item->link }}" target=”_blank”>
                                        <img src="{{ asset('public/assets/admin/img/' . $item->name . '.png') }}" alt=""
                                            style="height: 14px; width:14px; padding: 0px 3px 0px 5px;">
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

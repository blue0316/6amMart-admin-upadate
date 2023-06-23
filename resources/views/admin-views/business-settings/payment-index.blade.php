@extends('layouts.admin.app')

@section('title',translate('messages.Payment Method'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->

        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{asset('/public/assets/admin/img/payment.png')}}" class="w--22" alt="">
                    </span>
                    <span>
                        {{translate('messages.payment')}} {{translate('messages.gateway')}} {{translate('messages.setup')}}
                    </span>
                </h1>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header border-0 pb-0 pt-4">
                        <h5 class="card-title">
                            <span>{{translate('payment_method')}}</span>
                        </h5>
                    </div>
                    <div class="card-body pt-3">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('cash_on_delivery'))
                        <form action="{{route('admin.business-settings.payment-method-update',['cash_on_delivery'])}}"
                              method="post">
                            @csrf

                            <h5 class="text-capitalize mb-3">{{translate('messages.cash_on_delivery')}}</h5>
                            <div class="d-flex flex-wrap p-0">
                                <label class="form-check form--check mr-2 mr-md-4">
                                    <input class="form-check-input" type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <span class="form-check-label">{{translate('messages.active')}}</span>
                                </label>
                                <label class="form-check form--check">
                                    <input class="form-check-input" type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}>
                                    <span class="form-check-label">{{translate('messages.inactive')}}</span>
                                </label>
                            </div>
                            <div class="text-right mt-4 pt-2 mr-2">
                                <button type="submit" class="btn btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header border-0 pb-0 pt-4">
                        <h5 class="card-title">
                            <span>{{translate('payment_method')}}</span>
                        </h5>
                    </div>
                    <div class="card-body pt-3">
                        @php($digital_payment=\App\CentralLogics\Helpers::get_business_settings('digital_payment'))
                        <form action="{{route('admin.business-settings.payment-method-update',['digital_payment'])}}"
                              method="post">
                            @csrf
                            <h5 class="text-capitalize mb-3">{{translate('messages.digital')}} {{translate('messages.payment')}}</h5>
                            <div class="d-flex flex-wrap p-0">
                                <label class="form-check form--check mr-2 mr-md-4">
                                    <input class="form-check-input digital_payment" type="radio" name="status" value="1" {{$digital_payment?($digital_payment['status']==1?'checked':''):''}}>
                                    <span class="form-check-label">{{translate('messages.active')}}</span>
                                </label>
                                <label class="form-check form--check">
                                    <input class="form-check-input digital_payment" type="radio" name="status" value="0" {{$digital_payment?($digital_payment['status']==0?'checked':''):''}}>
                                    <span class="form-check-label">{{translate('messages.inactive')}}</span>
                                </label>
                            </div>
                            <div class="text-right mt-4 pt-2 mr-2">
                                <button type="submit" class="btn btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row digital_payment_methods mt-3 g-3">
            <!-- This Design Will Implement On All Digital Payment Method Its an Static Design Card Start -->
            @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['ssl_commerz_payment']):'javascript:'}}"
                        method="post">
                        @csrf
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.sslcommerz')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/sslcommerz.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" name="store_id" placeholder="Store ID" value="{{env('APP_MODE')!='demo'?($config?$config['store_id']:''):''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" name="store_password" placeholder="Store Password" value="{{env('APP_MODE')!='demo'?($config?$config['store_password']:''):''}}">
                            </div>
                            <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paypal']):'javascript:'}}"
                            method="post">
                            @csrf
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.paypal')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/paypal.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Paypal Client Id" name="paypal_client_id"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['paypal_client_id']:''):''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Paypal Secret" name="paypal_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['paypal_secret']??'':''}}">
                            </div>
                            <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['razor_pay']):'javascript:'}}"
                            method="post">
                            @csrf
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.razorpay')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/razorpay.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Razor Key" name="razor_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['razor_key']:''):''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Razor Secret" name="razor_secret"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['razor_secret']:''):''}}">
                            </div>
                            <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['stripe']):'javascript:'}}"
                              method="post">
                            @csrf
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.stripe')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/stripe.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Publish Key" name="published_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['published_key']:''):''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Api Key" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['api_key']:''):''}}">
                            </div>
                            <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paystack']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.paystack')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <span class="badge badge-soft-danger">{{translate('messages.paystack_callback_warning')}}</span>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/paystack.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Public Key" name="publicKey"
                                           value="{{env('APP_MODE')!='demo'?$config['publicKey']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Secret Key" name="secretKey"
                                           value="{{env('APP_MODE')!='demo'?$config['secretKey']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Payment Url" name="paymentUrl"
                                           value="{{env('APP_MODE')!='demo'?$config['paymentUrl']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Merchant Email" name="merchantEmail"
                                           value="{{env('APP_MODE')!='demo'?$config['merchantEmail']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="button" class="btn h--37px btn-success"onclick="copy_text('{{url('/')}}/paystack-callback')">{{translate('messages.copy_callback')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['senang_pay']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.senang')}} {{translate('messages.pay')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/senang-pay.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Secret Key" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Merchant Key" name="merchant_id"
                                           value="{{env('APP_MODE')!='demo'?$config['merchant_id']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('flutterwave'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['flutterwave']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.flutterwave')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/flutterwave.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Public Key" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Secret Key" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Hash" name="hash"
                                           value="{{env('APP_MODE')!='demo'?$config['hash']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('mercadopago'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['mercadopago']):'javascript:'}}"
                              method="post">
                              @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.mercadopago')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/mercador-pago.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Public Key" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Access Token" name="access_token"
                                           value="{{env('APP_MODE')!='demo'?$config['access_token']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paymob_accept'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paymob_accept']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.paymob_accept')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/paymob.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <label class="{{Session::get('direction') === 'rtl' ? 'pr-3' : 'pl-3'}}">{{translate('messages.callback')}}</label>
                                <span class="btn btn-secondary btn-sm m-2"
                                    onclick="copyToClipboard('#id_paymob_accept')"><i class="tio-copy"></i> {{translate('messages.copy_callback')}}</span>

                                <p class="form-control" id="id_paymob_accept">{{ url('/') }}/paymob-callback</p>
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Api Key" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Iframe Id" name="iframe_id"
                                           value="{{env('APP_MODE')!='demo'?$config['iframe_id']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Integration Id" name="integration_id"
                                           value="{{env('APP_MODE')!='demo'?$config['integration_id']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="HMAC" name="hmac"
                                           value="{{env('APP_MODE')!='demo'?$config['hmac']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('bkash'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['bkash']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.bkash')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/bkash.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Api Key" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Api Secret" name="api_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['api_secret']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Username" name="username"
                                           value="{{env('APP_MODE')!='demo'?$config['username']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Password" name="password"
                                           value="{{env('APP_MODE')!='demo'?$config['password']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paytabs'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paytabs']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.paytabs')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/paytabs.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Profile Id" name="profile_id"
                                           value="{{env('APP_MODE')!='demo'?$config['profile_id']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Server Key" name="server_key"
                                           value="{{env('APP_MODE')!='demo'?$config['server_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Base Url by Region" name="base_url"
                                           value="{{env('APP_MODE')!='demo'?$config['base_url']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paytm'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paytm']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.paytm')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/paytm.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Paytm Merchant Key" name="paytm_merchant_key"
                                           value="{{env('APP_MODE')!='demo'?$config['paytm_merchant_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Paytm Merchant Mid" name="paytm_merchant_mid"
                                           value="{{env('APP_MODE')!='demo'?$config['paytm_merchant_mid']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Merchant Website" name="paytm_merchant_website"
                                           value="{{env('APP_MODE')!='demo'?$config['paytm_merchant_website']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- End Col -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('liqpay'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['liqpay']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                        <h5 class="d-flex flex-wrap justify-content-between">
                            <strong>{{translate('messages.liqpay')}}</strong>
                            <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </h5>
                        <div class="payment--gateway-img">
                            <img src="{{asset('/public/assets/admin/img/payment/liqpay.png')}}" alt="public">
                        </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Public Key" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']:''}}">
                            </div>
                            <div class="form-group mb-4">
                                <input class="form-control" type="text" placeholder="Private Key" name="private_key"
                                           value="{{env('APP_MODE')!='demo'?$config['private_key']:''}}">
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn h--37px btn--primary">{{translate('messages.save')}}</button>
                            </div>
                            @else
                            <button type="submit"
                                    class="btn btn--primary mb-2">{{translate('messages.configure')}}</button>



                        @endif

                    </form>
                    </div>
                </div>
            </div>
            <!-- This Design Will Implement On All Digital Payment Method Its an Static Design Card End -->


            <!-- All Payment Gateway Commented Start Here  -->
            <!-- Required payment gateway images are inside of public/admin/img/payment/ folder  -->

<!-- Commented Payment Method Starts -->
        {{--
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.sslcommerz')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['ssl_commerz_payment']):'javascript:'}}"
                            method="post">
                            @csrf
                                <div class="form-group mb-2">
                                    <label
                                        class="control-label">{{translate('messages.sslcommerz')}} {{translate('messages.payment')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}>
                                    <label
                                       >{{translate('messages.inactive')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.store')}} {{translate('messages.id')}} </label><br>
                                    <input type="text" class="form-control" name="store_id"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['store_id']:''):''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.store')}} {{translate('messages.password')}}</label><br>
                                    <input type="text" class="form-control" name="store_password"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['store_password']:''):''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.razorpay')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['razor_pay']):'javascript:'}}"
                            method="post">
                            @csrf
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.razorpay')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}>
                                    <label
                                       >{{translate('messages.inactive')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label>{{translate('messages.razorkey')}}</label><br>
                                    <input type="text" class="form-control" name="razor_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['razor_key']:''):''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label>{{translate('messages.razorsecret')}}</label><br>
                                    <input type="text" class="form-control" name="razor_secret"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['razor_secret']:''):''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.paypal')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paypal']):'javascript:'}}"
                            method="post">
                            @csrf
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.paypal')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}>
                                    <label>{{translate('messages.inactive')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.paypal')}} {{translate('messages.client')}} {{translate('messages.id')}}</label><br>
                                    <input type="text" class="form-control" name="paypal_client_id"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['paypal_client_id']:''):''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label>{{translate('messages.paypalsecret')}} </label><br>
                                    <input type="text" class="form-control" name="paypal_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['paypal_secret']??'':''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.stripe')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['stripe']):'javascript:'}}"
                              method="post">
                            @csrf
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.stripe')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config?($config['status']==0?'checked':''):''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.published')}} {{translate('messages.key')}}</label><br>
                                    <input type="text" class="form-control" name="published_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['published_key']:''):''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.api')}} {{translate('messages.key')}}</label><br>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?($config?$config['api_key']:''):''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.paystack')}}</h5>
                        <span class="badge badge-soft-danger">{{translate('messages.paystack_callback_warning')}}</span>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paystack']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.paystack')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.publicKey')}}</label><br>
                                    <input type="text" class="form-control" name="publicKey"
                                           value="{{env('APP_MODE')!='demo'?$config['publicKey']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize">{{translate('messages.secret')}} {{translate('messages.key')}} </label><br>
                                    <input type="text" class="form-control" name="secretKey"
                                           value="{{env('APP_MODE')!='demo'?$config['secretKey']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize">{{translate('messages.payment')}} {{translate('messages.url')}}</label><br>
                                    <input type="text" class="form-control" name="paymentUrl"
                                           value="{{env('APP_MODE')!='demo'?$config['paymentUrl']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize">{{translate('messages.merchant')}} {{translate('messages.email')}}</label><br>
                                    <input type="text" class="form-control" name="merchantEmail"
                                           value="{{env('APP_MODE')!='demo'?$config['merchantEmail']:''}}">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                                    <button type="button" class="btn btn-info mb-2 pull-right" onclick="copy_text('{{url('/')}}/paystack-callback')">{{translate('messages.copy_callback')}}</button>
                                </div>


                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>



                            @endif

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.senang')}} {{translate('messages.pay')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['senang_pay']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.senang')}} {{translate('messages.pay')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                       >{{translate('messages.secret')}} {{translate('messages.key')}}</label><br>
                                    <input type="text" class="form-control" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.merchant')}} {{translate('messages.id')}}</label><br>
                                    <input type="text" class="form-control" name="merchant_id"
                                           value="{{env('APP_MODE')!='demo'?$config['merchant_id']:''}}">
                                </div>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <!-- Flutterwave -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.flutterwave')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('flutterwave'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['flutterwave']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.flutterwave')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                       >{{translate('messages.publicKey')}}</label><br>
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                       >{{translate('messages.secret')}} {{translate('messages.key')}}</label><br>
                                    <input type="text" class="form-control" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                       >{{translate('messages.hash')}}</label><br>
                                    <input type="text" class="form-control" name="hash"
                                           value="{{env('APP_MODE')!='demo'?$config['hash']:''}}">
                                </div>

                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <!-- MercadoPago -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.mercadopago')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('mercadopago'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['mercadopago']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.mercadopago')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                       >{{translate('messages.publicKey')}}</label><br>
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                       >{{translate('messages.access_token')}}</label><br>
                                    <input type="text" class="form-control" name="access_token"
                                           value="{{env('APP_MODE')!='demo'?$config['access_token']:''}}">
                                </div>

                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.paymob_accept')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paymob_accept'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paymob_accept']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.paymob_accept')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.callback')}}</label>
                                    <span class="btn btn-secondary btn-sm m-2"
                                          onclick="copyToClipboard('#id_paymob_accept')"><i class="tio-copy"></i> {{translate('messages.copy_callback')}}</span>
                                    <br>
                                    <p class="form-control" id="id_paymob_accept">{{ url('/') }}/paymob-callback</p>
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.api_key')}}</label><br>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.iframe_id')}}</label><br>
                                    <input type="text" class="form-control" name="iframe_id"
                                           value="{{env('APP_MODE')!='demo'?$config['iframe_id']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.integration_id')}}</label><br>
                                    <input type="text" class="form-control" name="integration_id"
                                           value="{{env('APP_MODE')!='demo'?$config['integration_id']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.HMAC')}}</label><br>
                                    <input type="text" class="form-control" name="hmac"
                                           value="{{env('APP_MODE')!='demo'?$config['hmac']:''}}">
                                </div>


                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.bkash')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('bkash'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['bkash']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.bkash')}}</label>
                                </div>

                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('api_key')}}</label><br>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.api_secret')}}</label><br>
                                    <input type="text" class="form-control" name="api_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['api_secret']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.username')}}</label><br>
                                    <input type="text" class="form-control" name="username"
                                           value="{{env('APP_MODE')!='demo'?$config['username']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.password')}}</label><br>
                                    <input type="text" class="form-control" name="password"
                                           value="{{env('APP_MODE')!='demo'?$config['password']:''}}">
                                </div>


                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.paytabs')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paytabs'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paytabs']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.paytabs')}}</label>
                                </div>

                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.profile_id')}}</label><br>
                                    <input type="text" class="form-control" name="profile_id"
                                           value="{{env('APP_MODE')!='demo'?$config['profile_id']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.server')}}</label><br>
                                    <input type="text" class="form-control" name="server_key"
                                           value="{{env('APP_MODE')!='demo'?$config['server_key']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.base_url_by_region')}}</label><br>
                                    <input type="text" class="form-control" name="base_url"
                                           value="{{env('APP_MODE')!='demo'?$config['base_url']:''}}">
                                </div>


                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.paytm')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paytm'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['paytm']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.paytm')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.paytm_merchant_key')}}</label><br>
                                    <input type="text" class="form-control" name="paytm_merchant_key"
                                           value="{{env('APP_MODE')!='demo'?$config['paytm_merchant_key']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label>{{translate('messages.paytm_merchant_mid')}}</label><br>
                                    <input type="text" class="form-control" name="paytm_merchant_mid"
                                           value="{{env('APP_MODE')!='demo'?$config['paytm_merchant_mid']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                       >{{translate('messages.paytm_merchant_website')}}</label><br>
                                    <input type="text" class="form-control" name="paytm_merchant_website"
                                           value="{{env('APP_MODE')!='demo'?$config['paytm_merchant_website']:''}}">
                                </div>

                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">{{translate('messages.liqpay')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('liqpay'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.payment-method-update',['liqpay']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('messages.liqpay')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label>{{translate('messages.active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label>{{translate('messages.inactive')}} </label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                          >{{translate('messages.publicKey')}}</label><br>
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']:''}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="text-capitalize"
                                          >{{translate('messages.privateKey')}}</label><br>
                                    <input type="text" class="form-control" name="private_key"
                                           value="{{env('APP_MODE')!='demo'?$config['private_key']:''}}">
                                </div>

                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{translate('messages.save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('messages.configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
--}}
<!-- Commented Payment Method Ends -->
        </div>
    </div>
@endsection

@push('script_2')
<script>
    @if(!isset($digital_payment) || $digital_payment['status']==0)
        $('.digital_payment_methods').hide();
    @endif
    $(document).ready(function () {
        $('.digital_payment').on('click', function(){
            if($(this).val()=='0')
            {
                $('.digital_payment_methods').addClass('blurry');
            }
            else
            {
                $('.digital_payment_methods').removeClass('blurry');
            }
        })
    });
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();

        toastr.success("{{translate('messages.text_copied')}}");
    }


    function checkedFunc() {
        $('.switch--custom-label .toggle-switch-input').each( function() {
            if(this.checked) {
                $(this).closest('.switch--custom-label').addClass('checked')
            }else {
                $(this).closest('.switch--custom-label').removeClass('checked')
            }
        })
    }
    checkedFunc()
    $('.switch--custom-label .toggle-switch-input').on('change', checkedFunc)


</script>
@endpush

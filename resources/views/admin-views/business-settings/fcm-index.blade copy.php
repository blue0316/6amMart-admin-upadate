@extends('layouts.admin.app')

@section('title',translate('FCM Settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/email.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.firebase')}} {{translate('messages.push')}} {{translate('messages.notification')}} {{translate('messages.setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.update-fcm'):'javascript:'}}" method="post"
                        enctype="multipart/form-data">
                    @csrf
                    @php($key=\App\Models\BusinessSetting::where('key','push_notification_key')->first())
                    <div class="form-group">
                        <label class="input-label"
                                for="exampleFormControlInput1">{{translate('messages.server')}} {{translate('messages.key')}}</label>
                        <textarea name="push_notification_key" class="form-control"
                                    required>{{env('APP_MODE')!='demo'?$key->value??'':''}}</textarea>
                    </div>
                    @php($project_id=\App\Models\BusinessSetting::where('key','fcm_project_id')->first())
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('FCM Project ID')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{$project_id->value??''}}"
                                name="projectId" class="form-control" placeholder="{{ translate('messages.Ex:') }} Project Id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.api_key')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{isset($fcm_credentials['apiKey'])?$fcm_credentials['apiKey']:''}}"
                                name="apiKey" class="form-control" placeholder="{{ translate('messages.Ex:') }} Api key">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.auth_domain')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{isset($fcm_credentials['authDomain'])?$fcm_credentials['authDomain']:''}}"
                                name="authDomain" class="form-control" placeholder="{{ translate('messages.Ex:') }} Auth domain">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.storage_bucket')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{isset($fcm_credentials['storageBucket'])?$fcm_credentials['storageBucket']:''}}"
                                name="storageBucket" class="form-control" placeholder="{{ translate('messages.Ex:') }} Storeage bucket">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.messaging_sender_id')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{isset($fcm_credentials['messagingSenderId'])?$fcm_credentials['messagingSenderId']:''}}"
                                name="messagingSenderId" class="form-control" placeholder="{{ translate('messages.Ex:') }} Messaging sender id">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.app_id')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{isset($fcm_credentials['appId'])?$fcm_credentials['appId']:''}}"
                                name="appId" class="form-control" placeholder="{{ translate('messages.Ex:') }} App Id">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.measurement_id')}}</label>
                        <div class="d-flex">
                            <input type="text" value="{{isset($fcm_credentials['measurementId'])?$fcm_credentials['measurementId']:''}}"
                                name="measurementId" class="form-control" placeholder="{{ translate('messages.Ex:') }} Measurement Id">
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{translate('messages.push')}} {{translate('messages.messages')}}</h2>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-fcm-messages')}}" method="post"
                        enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        @php($opm=\App\Models\BusinessSetting::where('key','order_pending_message')->first())
                        @php($data=$opm?json_decode($opm->value,true):null)
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.pending')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center"
                                        for="pending_status">
                                        <input type="checkbox" name="pending_status" class="toggle-switch-input"
                                            value="1" id="pending_status" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="pending_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($ocm=\App\Models\BusinessSetting::where('key','order_confirmation_msg')->first())
                        @php($data=$ocm?json_decode($ocm->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.confirmation')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                        for="confirm_status">
                                        <input type="checkbox" name="confirm_status" class="toggle-switch-input"
                                            value="1" id="confirm_status" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="confirm_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($oprm=\App\Models\BusinessSetting::where('key','order_processing_message')->first())
                        @php($data=$oprm?json_decode($oprm->value,true):null)
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.processing')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0" for="processing_status">
                                        <input type="checkbox" name="processing_status" class="toggle-switch-input" value="1" id="processing_status" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="processing_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($dbs=\App\Models\BusinessSetting::where('key','order_handover_message')->first())
                        @php($data=$dbs?json_decode($dbs->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.pending')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="order_handover_message_status">
                                        <input type="checkbox" name="order_handover_message_status"
                                                class="toggle-switch-input"
                                                value="1"
                                                id="order_handover_message_status" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="order_handover_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($ofdm=\App\Models\BusinessSetting::where('key','out_for_delivery_message')->first())
                        @php($data=$ofdm?json_decode($ofdm->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.out_for_delivery')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="out_for_delivery">
                                        <input type="checkbox" name="out_for_delivery_status"
                                                class="toggle-switch-input"
                                                value="1" id="out_for_delivery" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="out_for_delivery_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($odm=\App\Models\BusinessSetting::where('key','order_delivered_message')->first())
                        @php($data=$odm?json_decode($odm->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.delivered')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="delivered_status">
                                        <input type="checkbox" name="delivered_status"
                                                class="toggle-switch-input"
                                                value="1" id="delivered_status" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="delivered_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($dba=\App\Models\BusinessSetting::where('key','delivery_boy_assign_message')->first())
                        @php($data=$dba?json_decode($dba->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.deliveryman')}} {{translate('messages.assign')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                        for="delivery_boy_assign">
                                        <input type="checkbox" name="delivery_boy_assign_status"
                                            class="toggle-switch-input"
                                            value="1"
                                            id="delivery_boy_assign" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>
                                <textarea name="delivery_boy_assign_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        {{--@php($dbs=\App\Models\BusinessSetting::where('key','delivery_boy_start_message')->first())
                        @php($data=$dbs?json_decode($dbs->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.deliveryman')}} {{translate('messages.start')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="delivery_boy_start_status">
                                        <input type="checkbox" name="delivery_boy_start_status"
                                                class="toggle-switch-input"
                                                value="1"
                                                id="delivery_boy_start_status" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>

                                <textarea name="delivery_boy_start_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>--}}

                        @php($dbc=\App\Models\BusinessSetting::where('key','delivery_boy_delivered_message')->first())
                        @php($data=$dbc?json_decode($dbc->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.deliveryman')}} {{translate('messages.delivered')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="delivery_boy_delivered">
                                        <input type="checkbox" name="delivery_boy_delivered_status"
                                                class="toggle-switch-input"
                                                value="1"
                                                id="delivery_boy_delivered" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>

                                <textarea name="delivery_boy_delivered_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($dbc=\App\Models\BusinessSetting::where('key','order_cancled_message')->first())
                        @php($data=$dbc?json_decode($dbc->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.canceled')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="order_cancled_message">
                                        <input type="checkbox" name="order_cancled_message_status"
                                                class="toggle-switch-input"
                                                value="1"
                                                id="order_cancled_message" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>

                                <textarea name="order_cancled_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($orm=\App\Models\BusinessSetting::where('key','order_refunded_message')->first())
                        @php($data=$orm?json_decode($orm->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.order')}} {{translate('messages.refunded')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="order_refunded_message">
                                        <input type="checkbox" name="order_refunded_message_status"
                                                class="toggle-switch-input"
                                                value="1"
                                                id="order_refunded_message" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>

                                <textarea name="order_refunded_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($dbc=\App\Models\BusinessSetting::where('key','refund_request_canceled')->first())
                        @php($data=$dbc?json_decode($dbc->value,true):'')
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                    <span class="d-block text--semititle">
                                        {{translate('messages.refund_request_canceled')}} {{translate('messages.message')}}
                                    </span>
                                    <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                            for="refund_request_canceled">
                                        <input type="checkbox" name="refund_request_canceled_status"
                                                class="toggle-switch-input"
                                                value="1"
                                                id="refund_request_canceled" {{$data?($data['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                        <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                    </label>
                                </div>

                                <textarea name="refund_request_canceled" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{translate('messages.push')}} {{translate('messages.messages')}}</h2>
            </div>
            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
            @php($language = $language->value ?? null)
            @php($default_lang = 'bn')
            <div class="card-body">
                <div class="col-12 mb-5">
                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs border-0">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="divider mb-2"></div>
                <form action="{{route('admin.business-settings.update-fcm-messages')}}" method="post"
                        enctype="multipart/form-data">
                    @csrf

                    @if($language)
                    @php($default_lang = json_decode($language)[0])
                    @foreach(json_decode($language) as $lang)

                        <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                            <div class="row">
                                @php($opm=\App\Models\BusinessSetting::with('translations')->where('key','order_pending_message')->first())
                                @php($data=$opm?json_decode($opm->value,true):null)
                                
                                <?php
                                        if(count($opm->translations)){
                                            $translate = [];
                                            foreach($opm->translations as $t)
                                            {   
                                                if($t->locale == $lang && $t->key=='order_pending_message'){
                                                    $translate[$lang]['message'] = $t->value;
                                                }
                                            }
                                           
                                        }
     
                                ?>

                                

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.pending')}} {{translate('messages.message')}} ({{strtoupper($lang)}})
                                            </span>
                                           @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center"
                                                    for="pending_status">
                                                    <input type="checkbox" name="pending_status" class="toggle-switch-input"
                                                        value="1" id="pending_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                               
                                           @endif
                                        </div>
                                        <textarea name="pending_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($ocm=\App\Models\BusinessSetting::with('translations')->where('key','order_confirmation_msg')->first())
                                @php($data=$ocm?json_decode($ocm->value,true):'')
                                <?php
                                if(count($ocm->translations)){
                                        $translate_2 = [];
                                        foreach($ocm->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='order_confirmation_msg'){
                                                $translate_2[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.confirmation')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                    for="confirm_status">
                                                    <input type="checkbox" name="confirm_status" class="toggle-switch-input"
                                                        value="1" id="confirm_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                
                                            @endif
                                        </div>
                                        <textarea name="confirm_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_2[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($oprm=\App\Models\BusinessSetting::with('translations')->where('key','order_processing_message')->first())

                                @php($data=$oprm?json_decode($oprm->value,true):null)
                                
                                <?php
                                if(count($oprm->translations)){
                                        $translate_3 = [];
                                        foreach($oprm->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='order_processing_message'){
                                                $translate_3[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.processing')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0" for="processing_status">
                                                    <input type="checkbox" name="processing_status" class="toggle-switch-input" value="1" id="processing_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                    
                                            @endif
                                        </div>
                                        <textarea name="processing_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_3[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($dbs=\App\Models\BusinessSetting::with('translations')->where('key','order_handover_message')->first())
                                @php($data=$dbs?json_decode($dbs->value,true):'')
                                <?php
                                if(count($dbs->translations)){
                                        $translate_4 = [];
                                        foreach($dbs->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='order_handover_message'){
                                                $translate_4[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.pending')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                        for="order_handover_message_status">
                                                    <input type="checkbox" name="order_handover_message_status"
                                                            class="toggle-switch-input"
                                                            value="1"
                                                            id="order_handover_message_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                
                                            @endif
                                        </div>
                                        <textarea name="order_handover_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_4[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($ofdm=\App\Models\BusinessSetting::with('translations')->where('key','out_for_delivery_message')->first())
                                @php($data=$ofdm?json_decode($ofdm->value,true):'')
                                <?php
                                if(count($ofdm->translations)){
                                        $translate_5 = [];
                                        foreach($ofdm->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='out_for_delivery_message'){
                                                $translate_5[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.out_for_delivery')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                        for="out_for_delivery">
                                                    <input type="checkbox" name="out_for_delivery_status"
                                                            class="toggle-switch-input"
                                                            value="1" id="out_for_delivery" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                            @endif
                                        </div>
                                        <textarea name="out_for_delivery_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_5[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($odm=\App\Models\BusinessSetting::with('translations')->where('key','order_delivered_message')->first())
                                @php($data=$odm?json_decode($odm->value,true):'')
                                <?php
                                if(count($odm->translations)){
                                        $translate_6 = [];
                                        foreach($odm->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='order_delivered_message'){
                                                $translate_6[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.delivered')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                        for="delivered_status">
                                                    <input type="checkbox" name="delivered_status"
                                                            class="toggle-switch-input"
                                                            value="1" id="delivered_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                
                                            @endif
                                        </div>
                                        <textarea name="delivered_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_6[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($dba=\App\Models\BusinessSetting::with('translations')->where('key','delivery_boy_assign_message')->first())
                                @php($data=$dba?json_decode($dba->value,true):'')
                                <?php
                                if(count($dba->translations)){
                                        $translate_7 = [];
                                        foreach($dba->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='delivery_boy_assign_message'){
                                                $translate_7[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.deliveryman')}} {{translate('messages.assign')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                    for="delivery_boy_assign">
                                                    <input type="checkbox" name="delivery_boy_assign_status"
                                                        class="toggle-switch-input"
                                                        value="1"
                                                        id="delivery_boy_assign" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                
                                            @endif
                                        </div>
                                        <textarea name="delivery_boy_assign_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_7[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                {{--@php($dbs=\App\Models\BusinessSetting::where('key','delivery_boy_start_message')->first())
                                @php($data=$dbs?json_decode($dbs->value,true):'')
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.deliveryman')}} {{translate('messages.start')}} {{translate('messages.message')}}
                                            </span>
                                            <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                    for="delivery_boy_start_status">
                                                <input type="checkbox" name="delivery_boy_start_status"
                                                        class="toggle-switch-input"
                                                        value="1"
                                                        id="delivery_boy_start_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                            </label>
                                        </div>

                                        <textarea name="delivery_boy_start_message" class="form-control">{{$data['message']??''}}</textarea>
                                    </div>
                                </div>--}}

                                @php($dbc=\App\Models\BusinessSetting::where('key','delivery_boy_delivered_message')->first())
                                
                                @php($data=$dbc?json_decode($dbc->value,true):'')
                                <?php
                                if(count($dbc->translations)){
                                        $translate_8 = [];
                                        foreach($dbc->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='delivery_boy_delivered_message'){
                                                $translate_8[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.deliveryman')}} {{translate('messages.delivered')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                        for="delivery_boy_delivered">
                                                    <input type="checkbox" name="delivery_boy_delivered_status"
                                                            class="toggle-switch-input"
                                                            value="1"
                                                            id="delivery_boy_delivered" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                
                                            @endif
                                        </div>

                                        <textarea name="delivery_boy_delivered_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_8[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($dbc=\App\Models\BusinessSetting::where('key','order_cancled_message')->first())
                                @php($data=$dbc?json_decode($dbc->value,true):'')
                                <?php
                                if(count($opm->translations)){
                                        $translate_9 = [];
                                        foreach($opm->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='order_cancled_message'){
                                                $translate[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.canceled')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                        for="order_cancled_message">
                                                    <input type="checkbox" name="order_cancled_message_status"
                                                            class="toggle-switch-input"
                                                            value="1"
                                                            id="order_cancled_message" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                                
                                            @endif
                                        </div>

                                        <textarea name="order_cancled_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_9[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                @php($orm=\App\Models\BusinessSetting::with('translations')->where('key','order_refunded_message')->first())
                                @php($data=$orm?json_decode($orm->value,true):'')
                                <?php
                                if(count($opm->translations)){
                                        $translate_10 = [];
                                        foreach($opm->translations as $t)
                                        {   
                                            if($t->locale == $lang && $t->key=='order_refunded_message'){
                                                $translate_10[$lang]['message'] = $t->value;
                                            }
                                        }
                                   
                                     }

                                ?>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap justify-content-between mb-2">
                                            <span class="d-block text--semititle">
                                                {{translate('messages.order')}} {{translate('messages.refunded')}} {{translate('messages.message')}}
                                            </span>
                                            @if ($lang == 'en')
                                                <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                for="order_refunded_message">
                                                    <input type="checkbox" name="order_refunded_message_status"
                                                            class="toggle-switch-input"
                                                            value="1"
                                                            id="order_refunded_message" {{$data?($data['status']==1?'checked':''):''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    <span class="pl-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                                                    <span class="pl-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                                                </label>
                                            @endif
                                        </div>

                                        <textarea name="order_refunded_message[]" class="form-control" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate_10[$lang]['message']??$data['message'] !!}</textarea>
                                    </div>
                                </div>

                                <input type="hidden" name="lang[]" value="{{$lang}}">
                            </div>
                        </div>
                        @endforeach
                    @endif
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('script_2')
<script>

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
<script>
    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');

        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        console.log(lang);
        $("#"+lang+"-form").removeClass('d-none');
        if(lang == '{{$default_lang}}')
        {
            $("#from_part_2").removeClass('d-none');
        }
        else
        {
            $("#from_part_2").addClass('d-none');
        }
    })
</script>

@endpush

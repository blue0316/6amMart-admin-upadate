@extends('layouts.admin.app')

@section('title',translate('messages.app_settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/setting.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.app_settings')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        @php($app_minimum_version_android=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_android'])->first())
        @php($app_minimum_version_android=$app_minimum_version_android?$app_minimum_version_android->value:null)

        @php($app_url_android=\App\Models\BusinessSetting::where(['key'=>'app_url_android'])->first())
        @php($app_url_android=$app_url_android?$app_url_android->value:null)

        @php($app_minimum_version_ios=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_ios'])->first())
        @php($app_minimum_version_ios=$app_minimum_version_ios?$app_minimum_version_ios->value:null)

        @php($app_url_ios=\App\Models\BusinessSetting::where(['key'=>'app_url_ios'])->first())
        @php($app_url_ios=$app_url_ios?$app_url_ios->value:null)
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.app-settings')}}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label  class="form-label">{{translate('messages.app_minimum_version')}} ({{translate('messages.android')}})</label>
                                <input type="number" placeholder="{{translate('messages.app_minimum_version')}}" class="form-control" name="app_minimum_version_android"
                                    value="{{env('APP_MODE')!='demo'?$app_minimum_version_android??'':''}}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{translate('messages.app_url')}} ({{translate('messages.android')}})</label>
                                <input type="text" placeholder="{{translate('messages.app_url')}}" class="form-control" name="app_url_android"
                                    value="{{env('APP_MODE')!='demo'?$app_url_android??'':''}}">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label  class="form-label">{{translate('messages.app_minimum_version')}} ({{translate('messages.ios')}})</label>
                                <input type="number" placeholder="{{translate('messages.app_minimum_version')}}" class="form-control" name="app_minimum_version_ios"
                                    value="{{env('APP_MODE')!='demo'?$app_minimum_version_ios??'':''}}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{translate('messages.app_url')}} ({{translate('messages.ios')}})</label>
                                <input type="text" placeholder="{{translate('messages.app_url')}}" class="form-control" name="app_url_ios"
                                    value="{{env('APP_MODE')!='demo'?$app_url_ios??'':''}}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary mb-2">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('script_2')

@endpush

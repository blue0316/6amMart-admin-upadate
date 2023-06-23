@extends('layouts.admin.app')

@section('title', translate('messages.third_party_apis'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/api.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.third_party_apis')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
        @php($map_api_key=\App\Models\BusinessSetting::where(['key'=>'map_api_key'])->first())
        @php($map_api_key=$map_api_key?$map_api_key->value:null)

        @php($map_api_key_server=\App\Models\BusinessSetting::where(['key'=>'map_api_key_server'])->first())
        @php($map_api_key_server=$map_api_key_server?$map_api_key_server->value:null)
        <div class="card-header d-block p-3">
            <span class="badge badge-soft-primary white--space m-1 text-left">{{translate('messages.map_api_hint')}}</span>
            <span class="badge badge-soft-primary white--space m-1 text-left">{{translate('messages.map_api_hint_2')}}</span>
        </div>
            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.config-update'):'javascript:'}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="input-label">{{translate('messages.map_api_key')}} ({{translate('messages.client')}})</label>
                                <input type="text" placeholder="{{translate('messages.map_api_key')}} ({{translate('messages.client')}})" class="form-control" name="map_api_key"
                                    value="{{env('APP_MODE')!='demo'?$map_api_key??'':''}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="input-label">{{translate('messages.map_api_key')}} ({{translate('messages.server')}})</label>
                                <input type="text" placeholder="{{translate('messages.map_api_key')}} ({{translate('messages.server')}})" class="form-control" name="map_api_key_server"
                                    value="{{env('APP_MODE')!='demo'?$map_api_key_server??'':''}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush

@extends('layouts.admin.app')

@section('title',translate('messages.landing_page_settings'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header pb-0">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/landing.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{ translate('messages.landing_page_settings') }}
            </span>
        </h1>
    </div>
    <div class="mb-5">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <!-- Nav -->
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.top-menu-links')
            <!-- End Nav -->
        </div>
        <!-- End Nav Scroller -->
    </div>
        <!-- End Page Header -->
    <!-- Page Heading -->

    <div class="card my-2">
        <div class="card-body">
            <form action="{{route('admin.business-settings.landing-page-settings', 'links')}}" method="POST">
                @php($landing_page_links = \App\Models\BusinessSetting::where(['key'=>'landing_page_links'])->first())
                @php($landing_page_links = isset($landing_page_links->value)?json_decode($landing_page_links->value, true):null)

                @csrf
                <div class="row">
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="app_url_android_status">
                                <span>{{translate('messages.user')}} {{translate('messages.app_url')}} ({{translate('messages.play_store')}}) </span>
                                <input type="checkbox" class="toggle-switch-input" name="app_url_android_status" id="app_url_android_status" value="1" {{(isset($landing_page_links) && $landing_page_links['app_url_android_status'])?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <input type="text" id="app_url_android"  name="app_url_android" class="form-control" value="{{isset($landing_page_links['app_url_android']) ? $landing_page_links['app_url_android']:''}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="app_url_ios_status">
                                <span>{{translate('messages.user')}} {{translate('messages.app_url')}} ({{translate('messages.app_store')}}) </span>
                                <input type="checkbox" class="toggle-switch-input" name="app_url_ios_status" id="app_url_ios_status" value="1" {{(isset($landing_page_links) && $landing_page_links['app_url_ios_status'])?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <input type="text" id="app_url_ios" name="app_url_ios" class="form-control" value="{{isset($landing_page_links['app_url_ios']) ? $landing_page_links['app_url_ios'] : ''}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="web_app_url_status">
                                <span>{{translate('messages.web_app_url')}} </span>
                                <input type="checkbox" class="toggle-switch-input" name="web_app_url_status" id="web_app_url_status" value="1" {{(isset($landing_page_links) && $landing_page_links['web_app_url_status'])?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <input type="text" id="web_app_url" name="web_app_url" class="form-control" value="{{isset($landing_page_links)?$landing_page_links['web_app_url']:''}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="seller_app_url_status">
                                <span>{{translate('messages.seller_App_url')}} </span>
                                <input type="checkbox" class="toggle-switch-input" name="seller_app_url_status" id="seller_app_url_status" value="1" {{(isset($landing_page_links) && isset($landing_page_links['seller_app_url_status']))?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <input type="text" id="seller_app_url" name="seller_app_url" class="form-control" value="{{isset($landing_page_links['seller_app_url'])?$landing_page_links['seller_app_url']:''}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="deliveryman_app_url_status">
                                <span>{{translate('messages.deliveryman_App_url')}} </span>
                                <input type="checkbox" class="toggle-switch-input" name="deliveryman_app_url_status" id="deliveryman_app_url_status" value="1" {{(isset($landing_page_links) && isset($landing_page_links['deliveryman_app_url_status']))?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                            <input type="text" id="deliveryman_app_url" name="deliveryman_app_url" class="form-control" value="{{isset($landing_page_links['deliveryman_app_url'])?$landing_page_links['deliveryman_app_url']:''}}">
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
</div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // $("#app_url_android_status").on('change', function(){
            //     if($("#app_url_android_status").is(':checked')){
            //         $('#app_url_android').removeAttr('readonly');
            //     } else {
            //         $('#app_url_android').attr('readonly', true);
            //     }
            // });
            // $("#app_url_ios_status").on('change', function(){
            //     if($("#app_url_ios_status").is(':checked')){
            //         $('#app_url_ios').removeAttr('readonly');
            //     } else {
            //         $('#app_url_ios').attr('readonly', true);
            //     }
            // });
            // $("#web_app_url_status").on('change', function(){
            //     if($("#web_app_url_status").is(':checked')){
            //         $('#web_app_url').removeAttr('readonly');
            //     } else {
            //         $('#web_app_url').attr('readonly', true);
            //     }
            // });
        });
    </script>
@endpush

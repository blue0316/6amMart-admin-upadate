@extends('layouts.admin.app')
@section('title', translate('messages.landing_page_settings'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/admin/css/croppie.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
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
            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <!-- Nav -->
                @include('admin-views.business-settings.landing-page-settings.top-menu-links.top-menu-links')
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->
        <div class="card my-2">
            <div class="card-body">
                <form action="{{ route('admin.business-settings.landing-page-settings', 'background-change') }}"
                    method="POST">
                    @php($backgroundChange = \App\Models\BusinessSetting::where(['key' => 'backgroundChange'])->first())
                    @php($backgroundChange = isset($backgroundChange->value) ? json_decode($backgroundChange->value, true) : null)
                    @csrf
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="form-label d-block text-center">{{ translate('Primary Color 1') }}</label>
                            <input name="header-bg" type="color" class="form-control form-control-color" value="{{ isset($backgroundChange['primary_1_hex']) ? $backgroundChange['primary_1_hex'] : '#EF7822' }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label d-block text-center">{{ translate('Primary Color 2') }}</label>
                            <input name="footer-bg" type="color" class="form-control form-control-color" value="{{ isset($backgroundChange['primary_2_hex']) ? $backgroundChange['primary_2_hex'] :'#333E4F'}}" required>
                        </div>

                    </div>
                    <div class="form-group text-right mt-3 mb-0">
                        <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script_2')
@endpush

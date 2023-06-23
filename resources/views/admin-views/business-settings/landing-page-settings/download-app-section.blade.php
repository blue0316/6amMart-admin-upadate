@extends('layouts.admin.app')

@section('title',translate('messages.landing_page_image_settings'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
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

    <div class="card mb-3">
        <div class="card-body landing-page-images">
            @php($download_app_section = \App\Models\BusinessSetting::where(['key'=>'download_app_section'])->first())
            @php($download_app_section = isset($download_app_section->value)?json_decode($download_app_section->value, true):null)
            @php($counter = \App\Models\BusinessSetting::where(['key'=>'counter_section'])->first())
            @php($counter = isset($counter->value)?json_decode($counter->value, true):null)
            <div class="row gy-4">
                <div class="col-sm-12 col-xl-12">
                    <form action="{{route('admin.business-settings.landing-page-settings', 'download-section')}}" id="tnc-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-xl-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('messages.text')}}</label>
                                    <textarea class="form-control" name="description" rows="8">{!! $download_app_section['description'] ?? '' !!}</textarea>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xl-6">
                                <div class="form-group">
                                    <label class="input-label" >{{translate('messages.image')}}<small class="text-danger"> * ( {{translate('messages.size')}}: 514 X 378 px )</small></label>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="customFileEg">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                    </div>

                                    <center id="image-viewer-section" class="pt-4">
                                        <img class="img--200 border" id="viewer" src="{{asset('public/assets/landing')}}/image/{{isset($download_app_section['img'])?$download_app_section['img']:'double_screen_image.png'}}"
                                                onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'"
                                                alt=""/>
                                    </center>
                                </div>
                            </div>
                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" id="reset-button" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mt-2 mb-3">Counter Section</h3>
                            <form action="{{route('admin.business-settings.landing-page-settings', 'counter-section')}}" id="tnc-form" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4 col-xl-4">
                                        <div class="form-group">
                                            <label class="input-label">{{ translate('messages.app_download_count_numbers')}}</label>
                                            <input class="form-control" value="{{ $counter['app_download_count_numbers'] ?? '' }}" name="app_download_count_numbers">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-4">
                                        <div class="form-group">
                                            <label class="input-label">{{ translate('messages.seller_count_numbers')}}</label>
                                            <input class="form-control" name="seller_count_numbers" value="{{ $counter['deliveryman_count_numbers'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-4">
                                        <div class="form-group">
                                            <label class="input-label">{{ translate('messages.deliveryman_count_numbers')}}</label>
                                            <input class="form-control" value="{{ $counter['deliveryman_count_numbers'] ?? '' }}" name="deliveryman_count_numbers">
                                        </div>
                                    </div>
                                </div>
        
                                <div class="btn--container justify-content-end">
                                    <button type="reset" id="reset-button" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script>
        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#'+viewer).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg").change(function () {
            readURL(this, 'viewer');
            $('#image-viewer-section').show(1000);
        });

    </script>
@endpush

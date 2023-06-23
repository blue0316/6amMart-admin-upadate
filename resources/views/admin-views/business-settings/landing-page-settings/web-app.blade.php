@extends('layouts.admin.app')

@section('title',translate('messages.web_app_landing_page_settings'))

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

    <div class="card my-2">
        <div class="card-body landing-page-images">
            @php($web_app_landing_page_settings = \App\Models\BusinessSetting::where(['key'=>'web_app_landing_page_settings'])->first())
            @php($web_app_landing_page_settings = isset($web_app_landing_page_settings->value)?json_decode($web_app_landing_page_settings->value, true):null)

            <div class="row gy-4">
                <div class="col-sm-6 col-xl-4">
                    <form action="{{route('admin.business-settings.landing-page-settings', 'web-app')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="form-group d-block">
                            <span class="input-label text-center" >{{translate('messages.top_content_image')}}<small class="text-danger"> ( {{translate('messages.size')}}: 772 X 899 px )</small></span>
                            <center id="image-viewer-section" class="pt-4">
                                <img class="img--200 border" id="viewer"
                                        src="{{asset('public/assets/landing')}}/image/{{isset($web_app_landing_page_settings['top_content_image'])?$web_app_landing_page_settings['top_content_image']:'double_screen_image.png'}}"
                                        onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'"
                                        alt=""/>
                            </center>
                            <input type="file" name="top_content_image" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                        </label>
                        <div class="form-group mb-0">
                            <div class="landing--page-btns btn--container justify-content-center">
                                <label class="btn btn--reset" for="customFileEg1">{{translate('change image')}}</label>
                                <button type="submit" class="btn btn--primary">{{translate('messages.upload')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <form action="{{route('admin.business-settings.landing-page-settings', 'web-app')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                        <label class="form-group d-block">
                            <span class="input-label text-center" >{{translate('messages.mobile_app_section_image')}}<small class="text-danger"> ( {{translate('messages.size')}}: 1241 X 1755 px )</small></span>
                            <center id="image-viewer-section4" class="pt-4">
                                <img class="img--200 border" id="viewer4"
                                        src="{{asset('public/assets/landing')}}/image/{{isset($web_app_landing_page_settings['mobile_app_section_image'])?$web_app_landing_page_settings['mobile_app_section_image']:'our_app_image.png.png'}}"
                                        onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'"
                                        alt=""/>
                            </center>
                            <input type="file" name="mobile_app_section_image" id="customFileEg4" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                        </label>
                        <div class="form-group mb-0">
                            <div class="landing--page-btns btn--container justify-content-center">
                                <label class="btn btn--reset" for="customFileEg4">{{translate('change image')}}</label>
                                <button type="submit" class="btn btn--primary">{{translate('messages.upload')}}</button>
                            </div>
                        </div>
                    </form>
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

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
            $('#image-viewer-section').show(1000);
        });


        $("#customFileEg2").change(function () {
            readURL(this ,'viewer2');
            $('#image-viewer-section2').show(1000);
        });

        $("#customFileEg3").change(function () {
            readURL(this ,'viewer3');
            $('#image-viewer-section3').show(1000);
        });

        $("#customFileEg4").change(function () {
            readURL(this ,'viewer4');
            $('#image-viewer-section4').show(1000);
        });

    </script>
@endpush

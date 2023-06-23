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
            @php($landing_page_images = \App\Models\BusinessSetting::where(['key'=>'join_as_images'])->first())
            @php($landing_page_images = isset($landing_page_images->value)?json_decode($landing_page_images->value, true):null)

            <div class="row g-4">
                <div class="col-sm-12 col-xl-6">
                    <form action="{{route('admin.business-settings.landing-page-settings', 'joinas')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="form-group d-block">
                            <span class="input-label text-center" >{{translate('messages.seller_banner_background')}}<small class="text-danger"> ( {{translate('messages.size')}}: 1600 X 400 px )</small></span>
                            <center id="image-viewer-section5" class="pt-3 img--200 mx-auto">
                                <img class="border rounded w-100" id="viewer5" src="{{asset('public/assets/landing')}}/image/{{isset($landing_page_images['seller_banner_bg'])?$landing_page_images['seller_banner_bg']:'double_screen_image.png'}}"
                                        onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'"
                                        alt=""/>
                            </center>
                            <input type="file" name="seller_banner_bg" id="customFileEg5" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                        </label>
                        <div class="form-group mb-0">
                            <div class="landing--page-btns btn--container justify-content-center">
                                <label class="btn btn--reset" for="customFileEg5">{{translate('change image')}}</label>
                                <button type="submit" class="btn btn--primary">{{translate('messages.upload')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <form action="{{route('admin.business-settings.landing-page-settings', 'joinas')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="form-group d-block">
                            <span class="input-label text-center" >{{translate('messages.deliveryman_banner_background')}}<small class="text-danger"> ( {{translate('messages.size')}}: 1600 X 400 px )</small></span>
                            <center id="image-viewer-section6" class="pt-3 img--200 mx-auto">
                                <img class="border rounded w-100" id="viewer6" src="{{asset('public/assets/landing')}}/image/{{isset($landing_page_images['deliveryman_banner_bg'])?$landing_page_images['deliveryman_banner_bg']:'double_screen_image.png'}}"
                                        onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'"
                                        alt=""/>
                            </center>
                            <input type="file" name="deliveryman_banner_bg" id="customFileEg6" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                        </label>
                        <div class="form-group mb-0">
                            <div class="landing--page-btns btn--container justify-content-center">
                                <label class="btn btn--reset" for="customFileEg6">{{translate('change image')}}</label>
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

        $("#customFileEg5").change(function () {
            readURL(this ,'viewer5');
            $('#image-viewer-section5').show(1000);
        });

        $("#customFileEg6").change(function () {
            readURL(this ,'viewer6');
            $('#image-viewer-section6').show(1000);
        });

        $("#customFileEg7").change(function () {
            readURL(this ,'viewer7');
            $('#image-viewer-section7').show(1000);
        });

    </script>
@endpush

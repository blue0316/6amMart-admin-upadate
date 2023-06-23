@extends('layouts.admin.app')

@section('title',translate('messages.landing_page_settings'))

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
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <!-- Nav -->
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.top-menu-links')
            <!-- End Nav -->
        </div>
    </div>
    <!-- Page Heading -->

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gy-4">
                @foreach (config('module.module_type') as $key => $item)
                <div class="col-sm-12 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-capitalize card-header border-0 pl-0">{{$item}}</h4>
                            <form action="{{route('admin.business-settings.landing-page-settings', 'module-section')}}" id="tnc-form" method="POST" enctype="multipart/form-data">
                                @php($module_value = isset($module[$item])? $module[$item] :null)
                                @csrf
                                <input type="hidden" name="module" value="{{$item}}">
                                <div class="row">
                                    <div class="col-sm-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="input-label">{{ translate('messages.text')}}</label>
                                            <textarea class="form-control" name="description" rows="8">{!! $module_value['description'] ?? '' !!}</textarea>
                                        </div>
                                    </div>
        
                                    <div class="col-sm-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="input-label" >{{translate('messages.image')}}<small class="text-danger">  ( {{translate('messages.size')}}: 140 X 140 px )</small></label>
                                            <div class="custom-file">
                                                <input type="file" name="image" id="customFileEg-{{$key}}" class="custom-file-input"
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label" for="customFileEg-{{$key}}">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                            </div>
        
                                            <center id="image-viewer-section-{{$key}}" class="pt-4">
                                                <img class="img--120" id="viewer-{{$key}}" src="{{asset('public/assets/landing')}}/image/{{isset($module_value['img']) ? $module_value['img']:'double_screen_image.png'}}"
                                                onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'" alt=""/>
                                            </center>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="btn--container justify-content-end">
                                    <button type="reset" id="reset-button-{{$key}}" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
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

        @foreach (config('module.module_type') as $key => $item)
        $("#customFileEg-{{$key}}").change(function () {
            readURL(this, 'viewer-{{$key}}');
        });

        $('#reset_btn').click(function(){
            $('#viewer-{{$key}}').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
        })
        @endforeach

    </script>
@endpush

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
            <form action="{{route('admin.business-settings.landing-page-settings', 'testimonial')}}" method="POST" enctype="multipart/form-data">
                @php($testimonial = \App\Models\BusinessSetting::where(['key'=>'testimonial'])->first())
                @php($testimonial = isset($testimonial->value)?json_decode($testimonial->value, true):null)

                @csrf

                <div class="form-group">
                    <label class="input-label" for="reviewer_name">{{translate('messages.reviewer')}} <span class="text-danger">*</span></label>
                    <input type="text" placeholder="{{translate('messages.reviewer_name')}}" id="reviewer_name"  name="reviewer_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="input-label" for="reviewer_designation">{{translate('messages.designation')}} <span class="text-danger">*</span></label>
                    <input type="text" placeholder="{{translate('messages.reviewer_designation')}}" id="reviewer_designation"  name="reviewer_designation" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="input-label" for="review">{{translate('messages.review')}}</label>
                    <textarea type="text" id="review" placeholder="{{translate('messages.review')}}"  name="review" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label class="input-label" >{{translate('messages.image')}}<small class="text-danger"> * ( {{translate('messages.size')}}: 140 X 140 px )</small></label>
                    <div class="custom-file">
                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                        <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                    </div>

                    <center id="image-viewer-section" class="initial-hidden pt-2">
                        <img class="img--120" id="viewer"
                                src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt=""/>
                    </center>
                </div>

                <div class="form-group">
                    <label class="input-label" >{{translate('messages.Reviewer Brand')}} {{translate('messages.Image')}} ({{translate('messages.optional')}})<small class="text-danger"> ( {{translate('messages.size')}}: 140 X 140 px )</small></label>
                    <div class="custom-file">
                        <input type="file" name="brand_image" id="customFileEg2" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                        <label class="custom-file-label" for="customFileEg2">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                    </div>

                    <center id="image-viewer-section2" class="initial-hidden pt-2">
                        <img class="img--120" id="viewer2"
                                src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt=""/>
                    </center>
                </div>

                <div class="btn--container justify-content-end">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="review--table table table-borderless table-thead-bordered table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="w--1 border-0">{{translate('sl')}}</th>
                            <th scope="col" class="w--2 border-0">{{translate('messages.reviewer')}}</th>
                            <th scope="col" class="w--1 border-0">{{translate('messages.designation')}}</th>
                            <th scope="col" class="w--1 border-0">{{translate('messages.image two')}}</th>
                            <th scope="col" class="w--4 border-0">{{translate('messages.review')}}</th>
                            <th scope="col" class="w--1 border-0 text-center">{{translate('messages.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($testimonial)
                        @foreach ($testimonial as $key=>$sp)
                            <tr>
                                <td scope="row">{{$key + 1}}</td>
                                <td>
                                    <div class="media align-items-center">
                                        <img class="avatar avatar-lg mr-3" src="{{asset('public/assets/landing/image')}}/{{isset($sp['img']) ? $sp['img'] : ''}}"
                                                onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$sp['name']}}">
                                        <div class="media-body">
                                            <h5 class="text-hover-primary mb-0">{{$sp['name']}}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td>{{$sp['position']}}</td>
                                <td>
                                    <img class="avatar avatar-lg mr-3" src="{{asset('public/assets/landing/image')}}/{{isset($sp['brand_image']) ? $sp['brand_image'] : ''}}"
                                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$sp['name']}}">
                            <div class="media-body">
                                </td>
                                <td class="inline--3">
                                    <p class="text-justify w-100">{{$sp['detail']}}</p>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('sp-{{$key}}','{{translate('messages.Want_to_delete_this_item')}}')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                        <form action="{{route('admin.business-settings.landing-page-settings-delete',['tab'=>'testimonial', 'key'=>$key])}}"
                                                method="post" id="sp-{{$key}}">
                                            @csrf @method('delete')
                                        </form>
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
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
            readURL(this, 'viewer2');
            $('#image-viewer-section2').show(1000);
        });

        $('#reset_btn').click(function(){
            $('#viewer').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
        })
    </script>
@endpush

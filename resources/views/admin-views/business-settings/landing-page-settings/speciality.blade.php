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
        <!-- Nav Scroller -->
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <!-- Nav -->
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.top-menu-links')
            <!-- End Nav -->
        </div>
        <!-- End Nav Scroller -->
    </div>
    <!-- Page Heading -->

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{route('admin.business-settings.landing-page-settings', 'speciality')}}" method="POST" enctype="multipart/form-data">
                @php($speciality = \App\Models\BusinessSetting::where(['key'=>'speciality'])->first())
                @php($speciality = isset($speciality->value)?json_decode($speciality->value, true):null)

                @csrf

                <div class="form-group">
                    <label class="input-label" for="speciality_title">{{translate('messages.speciality_title')}}</label>
                    <input type="text" id="speciality_title"  name="speciality_title" class="form-control" placeholder="Speciality title">
                </div>
                <div class="form-group">
                    <label class="input-label" >{{translate('messages.speciality_img')}}<small class="text-danger">* ( {{translate('messages.size')}}: 60 X 60 px )</small></label>
                    <div class="custom-file">
                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                        <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                    </div>

                    <center id="image-viewer-section" class="pt-2 initial-hidden">
                        <img class="img--120" id="viewer"
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
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0" scope="col">{{translate('sl')}}</th>
                            <th class="border-0" scope="col">{{translate('messages.image')}}</th>
                            <th class="border-0" scope="col">{{translate('messages.speciality_title')}}</th>
                            <th class="border-0 text-center" scope="col">{{translate('messages.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($speciality)
                        @foreach ($speciality as $key=>$sp)
                            <tr>
                                <th scope="row">{{$key + 1}}</th>
                                <td>
                                    <div class="media align-items-center">
                                        <img class="avatar avatar-lg mr-3" src="{{asset('public/assets/landing/image')}}/{{$sp['img']}}"
                                                onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$sp['title']}}">
                                    </div>
                                </td>
                                <td>{{$sp['title']}}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('sp-{{$key}}','{{translate('messages.Want_to_delete_this_item')}}')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.business-settings.landing-page-settings-delete',['tab'=>'speciality', 'key'=>$key])}}"
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
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
            $('#image-viewer-section').show(1000);
        });

        $(document).on('ready', function () {

        });
        $('#reset_btn').click(function(){
            $('#viewer').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
        })
    </script>
@endpush

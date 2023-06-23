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
    @php($promotion_banner = \App\Models\BusinessSetting::where(['key'=>'promotion_banner'])->first())
    @php($promotion_banner = isset($promotion_banner->value)?json_decode($promotion_banner->value, true):[])
    @if (count($promotion_banner) >= 6)
    <div class="card mb-3 p-5">
        <div class="card-body">
            <p class=" card-title text-center"><code>{{ translate('You have already added maximum numbers of banner image') }}</code></p>
        </div>
    </div>
    @else
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{route('admin.business-settings.landing-page-settings', 'promotion-banner')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="input-label" for="title">{{translate('messages.title')}}</label>
                    <input type="text" placeholder="{{translate('messages.title')}}" id="title"  name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="input-label" for="sub_title">{{translate('messages.sub')}} {{translate('messages.title')}}</label>
                    <input type="text" placeholder="{{translate('messages.sub_title')}}" id="sub_title"  name="sub_title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="input-label" >{{translate('messages.banner')}} <small class="text-danger">* ( {{translate('messages.size')}}: 140 X 140 px )</small></label>
                    <div class="custom-file">
                        <input type="file" name="image" id="customFileEg2" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
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
    @endif
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="review--table table table-borderless table-thead-bordered table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="w--1 border-0">{{translate('sl')}}</th>
                            <th scope="col" class="w--1 border-0">{{translate('messages.title')}}</th>
                            <th scope="col" class="w--1 border-0">{{translate('messages.sub_title')}}</th>
                            <th scope="col" class="w--2 border-0 text-center">{{translate('messages.banner_image')}}</th>
                            <th scope="col" class="w--1 border-0 text-center">{{translate('messages.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($promotion_banner)
                        @foreach ($promotion_banner as $key=>$promotion)
                            <tr>
                                <td scope="row">{{$key + 1}}</td>
                                <td>{{$promotion['title']}}</td>
                                <td>{{$promotion['sub_title']}}</td>
                                <td>
                                    <img class="avatar avatar-lg mr-3 w-100" src="{{asset('public/assets/landing/image')}}/{{$promotion['img']}}"
                                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$promotion['title']}}">
                            <div class="media-body">
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('promotion-{{$key}}','{{translate('messages.Want_to_delete_this_item')}}')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                        <form action="{{route('admin.business-settings.landing-page-settings-delete',['tab'=>'promotion_banner', 'key'=>$key])}}"
                                                method="post" id="promotion-{{$key}}">
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

        $("#customFileEg2").change(function () {
            readURL(this, 'viewer2');
            $('#image-viewer-section2').show(1000);
        });

        $('#reset_btn').click(function(){
            $('#viewer').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
        })
    </script>
@endpush

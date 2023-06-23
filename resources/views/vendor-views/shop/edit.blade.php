
@extends('layouts.vendor.app')
@section('title',translate('messages.edit_store'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
     <!-- Custom styles for this page -->
     <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
     <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <!-- Content Row -->
    <div class="content container-fluid">
        <div class="page-header">
            <h2 class="page-header-title text-capitalize">
                <img class="w--26" src="{{asset('/public/assets/admin/img/store.png')}}" alt="public">
                <span>
                    {{translate('messages.edit')}} {{translate('messages.store')}} {{translate('messages.info')}}
                </span>
            </h1>
        </div>
        <form action="{{route('vendor.shop.update')}}" method="post"
                enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('messages.store')}} {{translate('messages.name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{$shop->name}}" class="form-control" id="name"
                                                required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">{{translate('messages.contact')}} {{translate('messages.number')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="contact" value="{{$shop->phone}}" class="form-control" id="name"
                                                required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">{{translate('messages.address')}}<span class="text-danger">*</span></label>
                                        <textarea type="text" rows="4" name="address" value="" class="form-control" id="address"
                                                required>{{$shop->address}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title font-regular">
                                {{translate('messages.upload')}} {{translate('messages.logo')}}
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column pt-0">
                            <center class="my-auto py-4 py-xl-5">
                                <img class="store-banner" id="viewer"
                                onerror="this.src='{{asset('public/assets/admin/img/image-place-holder.png')}}'"
                                src="{{asset('storage/app/public/store/'.$shop->logo)}}" alt="Product thumbnail"/>
                            </center>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileUpload">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title font-regular">
                                {{translate('messages.upload')}} {{translate('messages.cover')}} {{translate('messages.photo')}} <span class="text-danger">({{translate('messages.ratio')}} 2:1)</span>
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column pt-0">
                            <center class="my-auto py-4 py-xl-5">
                                <img class="store-banner" id="coverImageViewer"
                                onerror="this.src='{{asset('public/assets/admin/img/restaurant_cover.jpg')}}'"
                                src="{{asset('storage/app/public/store/cover/'.$shop->cover_photo)}}" alt="Product thumbnail"/>
                            </center>
                            <div class="custom-file">
                                <input type="file" name="photo" id="coverImageUpload" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="coverImageUpload">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 justify-content-end btn--container">
                <a class="btn btn--danger text-capitalize" href="{{route('vendor.shop.view')}}">{{translate('messages.cancel')}}</a>
                <button type="submit" class="btn btn--primary text-capitalize" id="btn_update">{{translate('messages.update')}}</button>
            </div>
        </form>
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

        $("#coverImageUpload").change(function () {
            readURL(this, 'coverImageViewer');
        });

        $("#customFileUpload").change(function () {
            readURL(this, 'viewer');
        });
   </script>
@endpush

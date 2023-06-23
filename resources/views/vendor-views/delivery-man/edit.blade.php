@extends('layouts.vendor.app')

@section('title',translate('Update delivery-man'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('/public/assets/admin/css/intlTelInput.css')}}" />
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.update')}} {{translate('messages.deliveryman')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="javascript:" method="post"
                enctype="multipart/form-data" id="deliaveryman_form" class="js-validate">
            @csrf
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="tio-user"></i> {{translate('messages.general_information')}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.first')}} {{translate('messages.name')}}</label>
                                    <input type="text" value="{{$delivery_man['f_name']}}" name="f_name"
                                        class="form-control" placeholder="{{translate('messages.first_name')}}"
                                        required>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.last')}} {{translate('messages.name')}}</label>
                                    <input type="text" value="{{$delivery_man['l_name']}}" name="l_name"
                                        class="form-control" placeholder="{{translate('messages.last_name')}}"
                                        required>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.email')}}</label>
                                        <input type="email" value="{{$delivery_man['email']}}" name="email" class="form-control"
                                            placeholder="{{ translate('messages.Ex:') }} ex@example.com"
                                            required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.identity')}} {{translate('messages.type')}}</label>
                                    <select name="identity_type" class="form-control">
                                        <option
                                            value="passport" {{$delivery_man['identity_type']=='passport'?'selected':''}}>
                                            {{translate('messages.passport')}}
                                        </option>
                                        <option
                                            value="driving_license" {{$delivery_man['identity_type']=='driving_license'?'selected':''}}>
                                            {{translate('messages.driving')}} {{translate('messages.license')}}
                                        </option>
                                        <option value="nid" {{$delivery_man['identity_type']=='nid'?'selected':''}}>{{translate('messages.nid')}}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.identity')}} {{translate('messages.number')}}</label>
                                    <input type="text" name="identity_number" value="{{$delivery_man['identity_number']}}"
                                        class="form-control"
                                        placeholder="{{ translate('messages.Ex:') }} DH-23434-LS"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="form-label m-0">{{translate('messages.identity')}} {{translate('messages.image')}}
                            <small class="text-danger">* {{translate('messages.( Ratio 190x120 )')}}</small></h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="form-group">
                                <div class="btn--container" id="coba">
                                    @foreach(json_decode($delivery_man['identity_image'],true) as $img)
                                        <div>
                                            <img class="img--120" src="{{asset('storage/app/public/delivery-man').'/'.$img}}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="form-label m-0">{{translate('messages.deliveryman')}} {{translate('messages.image')}}
                            <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1 )</small></h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="my-auto">
                                <center class="pt-3">
                                    <img class="img--120" id="viewer"
                                        src="{{asset('storage/app/public/delivery-man').'/'.$delivery_man['image']}}" alt="delivery-man image"/>
                                </center>
                            </div>
                            <div class="custom-file mt-3">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="tio-user"></i> {{translate('messages.account_information')}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.phone')}}</label>
                                        <input type="number" id="phone" name="phone" value="{{$delivery_man['phone']}}" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 017********"
                                                required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="js-form-message form-group mb-0">
                                        <label class="input-label" for="signupSrPassword">{{translate('messages.password')}}</label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control" name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}"
                                            data-msg="Your password is invalid. Please try again."
                                            data-hs-toggle-password-options='{
                                            "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                            "defaultClass": "tio-hidden-outlined",
                                            "showClass": "tio-visible-outlined",
                                            "classChangeTarget": ".js-toggle-passowrd-show-icon-1"
                                            }'>
                                            <div class="js-toggle-password-target-1 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-passowrd-show-icon-1 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="js-form-message form-group mb-0">
                                        <label class="input-label" for="signupSrConfirmPassword">{{translate('messages.confirm_password')}}</label>
                                        <div class="input-group input-group-merge">
                                        <input type="password" class="js-toggle-password form-control" name="confirmPassword" id="signupSrConfirmPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}"
                                                data-msg="Password does not match the confirm password."
                                                data-hs-toggle-password-options='{
                                                "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": ".js-toggle-passowrd-show-icon-2"
                                                }'>
                                            <div class="js-toggle-password-target-2 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                <i class="js-toggle-passowrd-show-icon-2 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script_2')

<script src="{{asset('/public/assets/admin/js/intlTelInput.js')}}"></script>
<script src="{{asset('/public/assets/admin/js/intlTelInput-jquery.min.js')}}"></script>
<link rel="shortcut icon" href="{{asset('/public/assets/admin/img/flags.png')}}" type="image/x-icon">
<link rel="shortcut icon" href="{{asset('/public/assets/admin/img/flags@2x.png')}}" type="image/x-icon">

<script>
    $(document).on('ready', function () {
      // INITIALIZATION OF SHOW PASSWORD
      // =======================================================
      $('.js-toggle-password').each(function () {
        new HSTogglePassword(this).init()
      });


      // INITIALIZATION OF FORM VALIDATION
      // =======================================================
      $('.js-validate').each(function() {
        $.HSCore.components.HSValidation.init($(this), {
          rules: {
            confirmPassword: {
              equalTo: '#signupSrPassword'
            }
          }
        });
      });
    });
</script>

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
        });

        @php($country=\App\Models\BusinessSetting::where('key','country')->first())
        var phone = $("#phone").intlTelInput({
            utilsScript: "{{asset('/public/assets/admin/js/utils.js')}}",
            nationalMode: true,
            autoHideDialCode: true,
            autoPlaceholder: "ON",
            dropdownContainer: document.body,
            formatOnDisplay: true,
            hiddenInput: "phone",
            initialCountry: "{{$country?$country->value:auto}}",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
    </script>

    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '75px',
                groupClassName: '',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('Please only input png or jpg type file', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('File size too big', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
    <script>
        $('#deliaveryman_form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.delivery-man.update',[$delivery_man['id']])}}',
                // data: $('#food_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else if(data.message){
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('vendor.delivery-man.list')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
    <script>
        $('#reset_btn').click(function(){
            location.reload(true);
        //     $('#viewer').attr('src','{{asset('storage/app/public/delivery-man').'/'.$delivery_man['image']}}');
        //     $("#coba").empty().spartanMultiImagePicker({
        //     fieldName: 'identity_image[]',
        //     maxCount: 5,
        //     rowHeight: '120px',
        //     groupClassName: 'col-6 spartan_item_wrapper size--md',
        //     maxFileSize: '',
        //     placeholderImage: {
        //         image: '{{asset('public/assets/admin/img/400x400/img2.jpg')}}',
        //         width: '100%'
        //     },
        //     dropFileLabel: "Drop Here",
        //     onAddRow: function (index, file) {

        //     },
        //     onRenderedPreview: function (index) {

        //     },
        //     onRemoveRow: function (index) {

        //     },
        //     onExtensionErr: function (index, file) {
        //         toastr.error('{{translate('messages.please_only_input_png_or_jpg_type_file')}}', {
        //             CloseButton: true,
        //             ProgressBar: true
        //         });
        //     },
        //     onSizeErr: function (index, file) {
        //         toastr.error('{{translate('messages.file_size_too_big')}}', {
        //             CloseButton: true,
        //             ProgressBar: true
        //         });
        //     }
        // });
        })
    </script>
@endpush

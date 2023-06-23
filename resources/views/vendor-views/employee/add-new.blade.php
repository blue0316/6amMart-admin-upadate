@extends('layouts.vendor.app')
@section('title', translate('messages.Employee Add'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/role.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.add_new_employee') }}
                </span>
            </h1>
        </div>

        <!-- Content Row -->

        <form action="{{ route('vendor.employee.add-new') }}" method="post" enctype="multipart/form-data" class="js-validate">
            @csrf
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon"><i class="tio-user"></i></span>
                        <span>{{ translate('messages.general_information') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label qcont text-capitalize"
                                    for="fname">{{ translate('messages.first') }}
                                    {{ translate('messages.name') }}</label>
                                <input type="text" name="f_name" class="form-control" id="fname"
                                    placeholder="{{ translate('messages.Ex:') }} Sakeef Ameer" value="{{ old('f_name') }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="input-label qcont text-capitalize"
                                    for="lname">{{ translate('messages.last') }}
                                    {{ translate('messages.name') }}</label>
                                <input type="text" name="l_name" class="form-control" id="lname"
                                    value="{{ old('l_name') }}" placeholder="{{ translate('messages.Ex:') }} Prodhan"
                                    value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label class="input-label qcont text-capitalize"
                                    for="phone">{{ translate('messages.phone') }}</label>
                                <input type="number" name="phone" value="{{ old('phone') }}" class="form-control"
                                    id="phone" placeholder="{{ translate('messages.Ex:') }} +88017********" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="input-label qcont text-capitalize"
                                    for="role_id">{{ translate('messages.Role') }}</label>
                                <select class="form-control custom-select2" name="role_id" required>
                                    <option value="" selected disabled>{{ translate('messages.select') }}
                                        {{ translate('messages.Role') }}</option>
                                    @foreach ($rls as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="form-label text-center mb-3">
                                        {{ translate('messages.employee_image') }}
                                        <span class="text-danger">{{ translate('messages.Ratio (1:1)') }}</span>
                                    </h5>
                                    <center class="my-auto">
                                        <img class="store-banner" id="viewer"
                                            src="{{ asset('public\assets\admin\img\160x160\img1.jpg') }}"
                                            alt="Employee thumbnail" />
                                    </center>
                                    <div class="form-group mt-3 mb-0">
                                        <label class="form-label">{{ translate('messages.Employee image size max 2 MB') }}
                                            <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" name="image" id="customFileUpload"
                                                class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                value="{{ old('image') }}" required>
                                            <label class="custom-file-label"
                                                for="customFileUpload">{{ translate('messages.choose') }}
                                                {{ translate('messages.file') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon"><i class="tio-user"></i></span>
                                        <span>{{ translate('messages.account_information') }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="input-label qcont text-capitalize"
                                                for="email">{{ translate('messages.email') }}</label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                class="form-control" id="email"
                                                placeholder="{{ translate('messages.Ex:') }} ex@gmail.com" required>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="js-form-message form-group mb-0">
                                                <label class="input-label" for="signupSrPassword">{{translate('messages.password')}}</label>

                                                <div class="input-group input-group-merge">
                                                    <input type="password" class="js-toggle-password form-control" name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'8+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'8+'])}}" required
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
                                        <div class="col-md-4">
                                            <div class="js-form-message form-group mb-0">
                                                <label class="input-label" for="signupSrConfirmPassword">{{translate('messages.confirm_password')}}</label>
                                                <div class="input-group input-group-merge">
                                                <input type="password" class="js-toggle-password form-control" name="confirmPassword" id="signupSrConfirmPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'8+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'8+'])}}" required
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
                                        <!-- Copy of Password -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" id="reset_btn"
                                    class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit"
                                    class="btn btn--primary">{{ translate('messages.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


@endsection

@push('script_2')
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

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });
    </script>
    <script>
        $('#reset_btn').click(function() {
            $('#viewer').attr('src', '{{ asset('public\assets\admin\img\400x400\img2.jpg') }}');
        })
    </script>
@endpush

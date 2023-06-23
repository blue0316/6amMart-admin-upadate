@extends('layouts.landing.app')
@section('title', translate('messages.deliveryman_registration'))
@push('css_or_js')
    <link rel="stylesheet" href="{{asset('/public/assets/admin/css/intlTelInput.css')}}"/>
@endpush

@section('content')
    <section class="about-section py-5 position-relative">
        <div class="container">
            <!-- Page Header -->
            <div class="section-header">
                <h2 class="title mb-2">{{translate("messages.Deliveryman")}} <span class="text--base">{{translate("messages.Application")}}</span></h2>
            </div>
            <!-- End Page Header -->
                <form action="{{ route('deliveryman.store') }}" method="post" enctype="multipart/form-data" id="form-id">
                    @csrf
                    <div class="card __card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <svg width="20" x="0" y="0" viewBox="0 0 460.8 460.8" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g><g><g>
                                        <path d="M230.432,239.282c65.829,0,119.641-53.812,119.641-119.641C350.073,53.812,296.261,0,230.432,0
                                            S110.792,53.812,110.792,119.641S164.604,239.282,230.432,239.282z" fill="#020202" data-original="#000000" class=""></path>
                                        <path d="M435.755,334.89c-3.135-7.837-7.314-15.151-12.016-21.943c-24.033-35.527-61.126-59.037-102.922-64.784
                                            c-5.224-0.522-10.971,0.522-15.151,3.657c-21.943,16.196-48.065,24.555-75.233,24.555s-53.29-8.359-75.233-24.555
                                            c-4.18-3.135-9.927-4.702-15.151-3.657c-41.796,5.747-79.412,29.257-102.922,64.784c-4.702,6.792-8.882,14.629-12.016,21.943
                                            c-1.567,3.135-1.045,6.792,0.522,9.927c4.18,7.314,9.404,14.629,14.106,20.898c7.314,9.927,15.151,18.808,24.033,27.167
                                            c7.314,7.314,15.673,14.106,24.033,20.898c41.273,30.825,90.906,47.02,142.106,47.02s100.833-16.196,142.106-47.02
                                            c8.359-6.269,16.718-13.584,24.033-20.898c8.359-8.359,16.718-17.241,24.033-27.167c5.224-6.792,9.927-13.584,14.106-20.898
                                            C436.8,341.682,437.322,338.024,435.755,334.89z" fill="#020202" data-original="#000000" class=""></path>
                                    </g>
                                </g>
                            </g>
                            </svg>{{ translate('messages.deliveryman') }} {{ translate('messages.info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.first') }}
                                            {{ translate('messages.name') }}</label>
                                        <input type="text" name="f_name" class="form-control __form-control"
                                            placeholder="{{ translate('messages.first') }} {{ translate('messages.name') }}" required
                                            value="{{ old('f_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.last') }}
                                            {{ translate('messages.name') }}</label>
                                        <input type="text" name="l_name" class="form-control __form-control"
                                            placeholder="{{ translate('messages.last') }} {{ translate('messages.name') }}"
                                            value="{{ old('l_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.email') }}</label>
                                        <input type="email" name="email" class="form-control __form-control"
                                            placeholder="{{ translate('messages.Ex:') }} ex@example.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.deliveryman') }}
                                            {{ translate('messages.type') }}</label>
                                        <select name="earning" class="form-control __form-control">
                                            <option value="1">{{ translate('messages.freelancer') }}</option>
                                            <option value="0">{{ translate('messages.salary_based') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.zone') }}</label>
                                        <select name="zone_id" class="form-control __form-control" required
                                            data-placeholder="{{ translate('messages.select') }} {{ translate('messages.zone') }}">
                                            <option value="" readonly="true" hidden="true">{{ translate('messages.select') }}
                                                {{ translate('messages.zone') }}</option>
                                            @foreach (\App\Models\Zone::active()->get() as $zone)
                                                @if (isset(auth('admin')->user()->zone_id))
                                                    @if (auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{ $zone->id }}" selected>{{ $zone->name }}
                                                        </option>
                                                    @endif
                                                @else
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.identity') }}
                                            {{ translate('messages.type') }}</label>
                                        <select name="identity_type" class="form-control __form-control">
                                            <option value="passport">{{ translate('messages.passport') }}</option>
                                            <option value="driving_license">{{ translate('messages.driving') }}
                                                {{ translate('messages.license') }}</option>
                                            <option value="nid">{{ translate('messages.nid') }}</option>
                                            <option value="restaurant_id">{{ translate('messages.store') }}
                                                {{ translate('messages.id') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.identity') }}
                                            {{ translate('messages.number') }}</label>
                                        <input type="text" name="identity_number" class="form-control __form-control"
                                            value="{{ old('identity_number') }}" placeholder="{{ translate('messages.Ex:') }} DH-23434-LS" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.identity') }}
                                            {{ translate('messages.image') }}</label>
                                        <div>
                                            <div class="row" id="coba"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card __card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <svg width="20" x="0" y="0" viewBox="0 0 460.8 460.8" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g><g><g>
                                        <path d="M230.432,239.282c65.829,0,119.641-53.812,119.641-119.641C350.073,53.812,296.261,0,230.432,0
                                            S110.792,53.812,110.792,119.641S164.604,239.282,230.432,239.282z" fill="#020202" data-original="#000000" class=""></path>
                                        <path d="M435.755,334.89c-3.135-7.837-7.314-15.151-12.016-21.943c-24.033-35.527-61.126-59.037-102.922-64.784
                                            c-5.224-0.522-10.971,0.522-15.151,3.657c-21.943,16.196-48.065,24.555-75.233,24.555s-53.29-8.359-75.233-24.555
                                            c-4.18-3.135-9.927-4.702-15.151-3.657c-41.796,5.747-79.412,29.257-102.922,64.784c-4.702,6.792-8.882,14.629-12.016,21.943
                                            c-1.567,3.135-1.045,6.792,0.522,9.927c4.18,7.314,9.404,14.629,14.106,20.898c7.314,9.927,15.151,18.808,24.033,27.167
                                            c7.314,7.314,15.673,14.106,24.033,20.898c41.273,30.825,90.906,47.02,142.106,47.02s100.833-16.196,142.106-47.02
                                            c8.359-6.269,16.718-13.584,24.033-20.898c8.359-8.359,16.718-17.241,24.033-27.167c5.224-6.792,9.927-13.584,14.106-20.898
                                            C436.8,341.682,437.322,338.024,435.755,334.89z" fill="#020202" data-original="#000000" class=""></path>
                                    </g>
                                </g>
                            </g>
                            </svg>{{ translate('messages.login') }} {{ translate('messages.info') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label" for="phone">{{ translate('messages.phone') }}</label>
                                        <div class="input-group">
                                            <input type="tel" name="tel" id="phone" placeholder="{{ translate('messages.Ex:') }} 017********"
                                                class="form-control __form-control" value="{{ old('tel') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.password') }}</label>
                                        <input type="text" name="password" class="form-control __form-control" placeholder="{{ translate('messages.Ex:') }} password"
                                            value="{{ old('password') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex">
                                <div class="col-lg-6">
                                    <div class="form-group pt-3 mb-5">
                                        <img class="__register-img mb-3" id="viewer"
                                            src="{{ asset('public/assets/admin/img/400x400/img2.jpg') }}"
                                            alt="delivery-man image" />
                                        <label  class="input-label">{{ translate('messages.deliveryman') }} {{ translate('messages.image') }}<small
                                            class="text-danger">* ( {{ translate('messages.ratio') }} 1:1 )</small></label>
                                        <div class="custom-file">
                                            <input type="file" name="image" id="customFileEg1" class="form-control __form-control"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-12">
                                    {{-- recaptcha --}}
                                    @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                                        <div id="recaptcha_element" style="width: 100%;" data-type="image"></div>
                                        <br/>
                                    @else
                                        <div class="row p-2">
                                            <div class="col-6 pr-0">
                                                <input type="text" class="form-control" name="custome_recaptcha"
                                                        id="custome_recaptcha" required placeholder="{{\__('Enter recaptcha value')}}" autocomplete="off" value="{{env('APP_DEBUG')?session('six_captcha'):''}}">
                                            </div>
                                            <div class="col-6" style="background-color: #FFFFFF; border-radius: 5px;">
                                                <img src="<?php echo $custome_recaptcha->inline(); ?>" style="width: 100%; border-radius: 4px;"/>
                                            </div>
                                        </div>
                                    @endif                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="cmn--btn border-0 outline-0">{{ translate('messages.submit') }}</button>
                    </div>
                </form>
        </div>

    </section>

@endsection

@push('script_2')
    {{-- <script src="{{ asset('public/assets/admin') }}/js/toastr.js"></script>
    {!! Toastr::message() !!} --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"
        integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"
        integrity="sha512-hkmipUFWbNGcKnR0nayU95TV/6YhJ7J9YUAkx4WLoIgrVr7w1NYz28YkdNFMtPyPeX1FrQzbfs3gl+y94uZpSw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.min.js"
             integrity="sha512-lv6g7RcY/5b9GMtFgw1qpTrznYu1U4Fm2z5PfDTG1puaaA+6F+aunX+GlMotukUFkxhDrvli/AgjAu128n2sXw=="
             crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <link rel="shortcut icon" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/img/flags.png"
        type="image/x-icon">
    <link rel="shortcut icon" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/img/flags@2x.png"
        type="image/x-icon">
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

        $("#customFileEg1").change(function() {
            readURL(this);
        });
        @php($country = \App\Models\BusinessSetting::where('key', 'country')->first())
        var phone = $("#phone").intlTelInput({
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
            autoHideDialCode: true,
            autoPlaceholder: "ON",
            dropdownContainer: document.body,
            formatOnDisplay: true,
            hiddenInput: "phone",
            initialCountry: "{{ $country ? $country->value : auto }}",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        // $("#phone").on('change', function(){
        //     $(this).val(phone.getNumber());
        // })
    </script>
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '120px',
                groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('public/assets/admin/img/400x400/img2.jpg') }}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error('{{ translate('messages.please_only_input_png_or_jpg_type_file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ translate('messages.file_size_too_big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
        
    </script>


    {{-- recaptcha scripts start --}}
    @if(isset($recaptcha) && $recaptcha['status'] == 1)
        <script type="text/javascript">
            var onloadCallback = function () {
                grecaptcha.render('recaptcha_element', {
                    'sitekey': '{{ \App\CentralLogics\Helpers::get_business_settings('recaptcha')['site_key'] }}'
                });
            };
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script>
            $("#form-id").on('submit',function(e) {
                var response = grecaptcha.getResponse();

                if (response.length === 0) {
                    e.preventDefault();
                    toastr.error("{{__('messages.Please check the recaptcha')}}");
                }
            });
        </script>
    @endif
    {{-- recaptcha scripts end --}}
@endpush

@extends('layouts.landing.app')
@section('title', translate('messages.store_registration'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin') }}/css/toastr.css">
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/select2.min.css') }}"/>
    <style>
        #map {
            height: 350px;
        }

        @media only screen and (max-width: 768px) {

            /* For mobile phones: */
            #map {
                height: 200px;
            }
        }

        .form-container {
            box-shadow: 4px 4px 10px rgba(65, 83, 179, 0.15);
            border-radius: 8px;
            border: 2px solid #b3bac3;
            padding: 0.625rem;
        }

        .row-margin-top {
            margin-top: 20px;
        }

        .cover-photo {
            margin-inline-start: 150px;
        }

        .restaurant-logo {
            margin-inline-start: 100px;
            margin-inline-end: 150px;
        }

    </style>
    
@endpush
@section('content')
    <section class="m-0 py-5">
        <div class="container">
            <!-- Page Header -->
            <div class="section-header">
                <h2 class="title mb-2">{{ translate('messages.store') }} <span class="text--base">{{translate('application')}}</span></h2>
            </div>
            <!-- End Page Header -->
            <form class="js-validate" action="{{ route('restaurant.store') }}" method="post" enctype="multipart/form-data" id="form-id">
                @csrf
                <div class="card __card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <svg width="20" x="0" y="0" viewBox="0 0 68 68" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g><path d="m62.99 57.53h-1.17v-29.22c-1.09-.47-2.02-1.25-2.67-2.23-1.08 1.63-2.93 2.71-5.03 2.71s-3.95-1.08-5.03-2.71c-1.08 1.63-2.93 2.71-5.03 2.71s-3.95-1.08-5.03-2.71c-1.08 1.63-2.93 2.71-5.03 2.71s-3.95-1.08-5.03-2.71c-1.08 1.63-2.92 2.71-5.02 2.71-2.11 0-3.97-1.09-5.05-2.74-1.09 1.61-2.92 2.67-5.01 2.67-2.1 0-3.95-1.08-5.03-2.71-.65.98-1.58 1.77-2.68 2.23v29.29h-1.17c-1.21 0-2.19.98-2.19 2.19v4.16h62.36v-4.16c0-1.21-.98-2.19-2.19-2.19zm-33.55 0h-16.45v-20.29c0-1.36 1.1-2.47 2.47-2.47h11.51c1.36 0 2.47 1.11 2.47 2.47zm24.43-9.54c0 .88-.71 1.59-1.59 1.59h-13.41c-.88 0-1.6-.71-1.6-1.59v-12.13c0-.88.72-1.59 1.6-1.59h13.41c.88 0 1.59.71 1.59 1.59z" fill="#000000" data-original="#000000"></path><path d="m59.86 19.99h7.77l-3.07-6.5c-.33-.7-1.03-1.15-1.81-1.15h-5.46z" fill="#000000" data-original="#000000"></path><path d="m10.72 12.27h-5.46c-.77 0-1.48.45-1.81 1.15l-3.07 6.5h7.76z" fill="#000000" data-original="#000000"></path><path d="m60.15 21.99v.77c0 2.22 1.8 4.03 4.03 4.03 2.22 0 4.02-1.81 4.02-4.03v-.77z" fill="#000000" data-original="#000000"></path><path d="m54.12 26.79c2.22 0 4.03-1.81 4.03-4.03v-.77h-8.06v.77c0 2.22 1.81 4.03 4.03 4.03z" fill="#000000" data-original="#000000"></path><path d="m46.71 14.26-.39-1.92h-6.87l.52 7.65h7.9z" fill="#000000" data-original="#000000"></path><path d="m9.86 22.69c0 2.22 1.81 4.03 4.03 4.03s4.03-1.81 4.03-4.03v-.7h-8.06z" fill="#000000" data-original="#000000"></path><path d="m55.18 12.34h-6.82l1.16 5.73.39 1.92h7.84z" fill="#000000" data-original="#000000"></path><path d="m19.92 22.76c0 2.22 1.8 4.03 4.03 4.03 2.22 0 4.02-1.81 4.02-4.03v-.77h-8.05z" fill="#000000" data-original="#000000"></path><path d="m7.86 22.69v-.77h-8.06v.77c0 2.22 1.81 4.03 4.03 4.03s4.03-1.81 4.03-4.03z" fill="#000000" data-original="#000000"></path><path d="m19.64 12.34h-6.81l-2.55 7.58h7.83z" fill="#000000" data-original="#000000"></path><path d="m30.56 12.34-.52 7.65h7.92l-.51-7.65z" fill="#000000" data-original="#000000"></path><path d="m44.06 26.79c2.22 0 4.03-1.81 4.03-4.03v-.77h-8.06v.77c0 2.22 1.81 4.03 4.03 4.03z" fill="#000000" data-original="#000000"></path><path d="m28.55 12.34h-6.86l-1.55 7.65h7.9z" fill="#000000" data-original="#000000"></path><path d="m29.97 22.76c0 2.22 1.81 4.03 4.03 4.03s4.03-1.81 4.03-4.03v-.77h-8.06z" fill="#000000" data-original="#000000"></path><path d="m13.49 10.34h48.33v-2.03c0-2.31-1.87-4.19-4.18-4.19h-47.27c-2.31 0-4.19 1.88-4.19 4.19v1.96h7.33z" fill="#000000" data-original="#000000"></path></g></g></svg> {{ translate('messages.store') }} {{ translate('messages.info') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row row-margin-top">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="name">{{ translate('messages.store') }}
                                        {{ translate('messages.name') }}</label>
                                    <input type="text" name="name" class="form-control __form-control"
                                        placeholder="{{ translate('messages.first') }} {{ translate('messages.name') }}"
                                        value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="tax">{{ translate('messages.vat/tax') }} (%)</label>
                                    <input type="number" name="tax" class="form-control __form-control"
                                        placeholder="{{ translate('messages.vat/tax') }}" min="0" step=".01" required
                                        value="{{ old('tax') }}">
                                </div>
                            </div>

                        </div>
                        <div class="row row-margin-top">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="address">{{ translate('messages.store') }}
                                        {{ translate('messages.address') }}</label>
                                    <textarea type="text" name="address" class="form-control __form-control"
                                        placeholder="{{ translate('messages.store') }} {{ translate('messages.address') }}"
                                        required>{{ old('address') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="maximum_delivery_time">{{translate('messages.approx_delivery_time')}}</label>
                                    <div class="input-group">
                                        <input type="number" name="minimum_delivery_time" class="form-control __form-control" placeholder="Min: 10" value="{{old('minimum_delivery_time')}}">
                                        <input type="number" name="maximum_delivery_time" class="form-control __form-control" placeholder="Max: 20" value="{{old('maximum_delivery_time')}}">
                                        <select name="delivery_time_type" class="form-control __form-control text-capitalize" id="" required>
                                            <option value="min">{{translate('messages.minutes')}}</option>
                                            <option value="hours">{{translate('messages.hours')}}</option>
                                            <option value="days">{{translate('messages.days')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row row-margin-top">
                            <div class="col-md-6 col-lg-6 col-sm-12 float-end">
                                <div class="form-group">

                                    <img class="__register-img" id="coverImageViewer"
                                        src="{{ asset('public/assets/admin/img/900x400/img1.jpg') }}"
                                        alt="Product thumbnail" />

                                    <label for="name" class="input-label pt-2">{{ translate('messages.upload') }} {{ translate('messages.cover') }}
                                        {{ translate('messages.photo') }} <span
                                            class="text-danger">({{ translate('messages.ratio') }}
                                            2:1)</span></label>
                                    <div class="custom-file">
                                        <input type="file" name="cover_photo" id="coverImageUpload" class="form-control __form-control"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">

                                    <img class="__register-img" id="logoImageViewer"
                                        src="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        alt="Product thumbnail" />


                                    <label class="input-label pt-2">{{ translate('messages.store') }}
                                        {{ translate('messages.logo') }}<small style="color: red"> (
                                            {{ translate('messages.ratio') }}
                                            1:1
                                            )</small></label>
                                    <div class="custom-file">
                                        <input type="file" name="logo" id="customFileEg1" class="form-control __form-control"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row row-margin-top">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="choice_zones">{{ translate('messages.zone') }} <span
                                            class="input-label-secondary" title="{{ translate('messages.select_zone_for_map') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.select_zone_for_map') }}"></span></label>
                                    <select name="zone_id" id="choice_zones" required class="form-control __form-control js-select2-custom js-example-basic-single"
                                        data-placeholder="{{ translate('messages.select') }} {{ translate('messages.zone') }}">
                                        <option value="" selected disabled>{{ translate('messages.select') }}
                                            {{ translate('messages.zone') }}</option>
                                        @foreach (\App\Models\Zone::active()->get() as $zone)
                                            @if (isset(auth('admin')->user()->zone_id))
                                                @if (auth('admin')->user()->zone_id == $zone->id)
                                                    <option value="{{ $zone->id }}" selected>{{ $zone->name }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label">{{translate('messages.module')}}</label>
                                    <select name="module_id" required id="module_id"
                                            class="js-data-example-ajax form-control __form-control"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}">
                                            {{-- <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                        @foreach(\App\Models\Module::notParcel()->get() as $module)
                                            <option value="{{$module->id}}">{{$module->module_name}}</option>
                                        @endforeach --}}
                                    </select>
                                    <small class="text-danger">{{translate('messages.module_change_warning')}}</small>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-12 col-sm-12 mb-3">
                                <input id="pac-input" class="controls rounded" style="height: 3em;width:fit-content;" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                <div id="map"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="latitude">{{ translate('messages.latitude') }} <span
                                            class="input-label-secondary"
                                            title="{{ translate('messages.store_lat_lng_warning') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.store_lat_lng_warning') }}"></span></label>
                                    <input type="text" id="latitude" name="latitude" class="form-control __form-control"
                                        placeholder="{{ translate('messages.Ex:') }} -94.22213" value="{{ old('latitude') }}" required readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label" for="longitude">{{ translate('messages.longitude') }} <span
                                            class="input-label-secondary"
                                            title="{{ translate('messages.store_lat_lng_warning') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.store_lat_lng_warning') }}"></span></label>
                                    <input type="text" name="longitude" class="form-control __form-control" placeholder="{{ translate('messages.Ex:') }} 103.344322"
                                        id="longitude" value="{{ old('longitude') }}" required readonly>
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
                        </svg>
                        {{ translate('messages.owner') }} {{ translate('messages.info') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row row-margin-top">
                            <div class="col-md-4 col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="f_name">{{ translate('messages.first') }}
                                        {{ translate('messages.name') }}</label>
                                    <input type="text" name="f_name" class="form-control __form-control"
                                        placeholder="{{ translate('messages.first') }} {{ translate('messages.name') }}"
                                        value="{{ old('f_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="l_name">{{ translate('messages.last') }}
                                        {{ translate('messages.name') }}</label>
                                    <input type="text" name="l_name" class="form-control __form-control"
                                        placeholder="{{ translate('messages.last') }} {{ translate('messages.name') }}"
                                        value="{{ old('l_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <label class="input-label" for="phone">{{ translate('messages.phone') }}</label>
                                    <input type="text" name="phone" class="form-control __form-control" placeholder="{{ translate('messages.Ex:') }} 017********"
                                        value="{{ old('phone') }}" required>
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
                        </svg>
                        {{ translate('messages.login') }} {{ translate('messages.info') }}
                    </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mt-3">
                            <div class="col-md-4 col-sm-12 col-lg-4">
                                <div class="form-group">
                                    <label class="input-label" for="email">{{ translate('messages.email') }}</label>
                                    <input type="email" name="email" class="form-control __form-control" placeholder="{{ translate('messages.Ex:') }} ex@example.com"
                                        value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-lg-4">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleInputPassword">{{ translate('messages.password') }}</label>
                                    <input type="password" name="password"
                                        placeholder="{{ translate('messages.password_length_placeholder', ['length' => '6+']) }}"
                                        class="form-control __form-control form-control __form-control-user" minlength="6" id="exampleInputPassword" required
                                        value="{{ old('password') }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-lg-4">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="signupSrConfirmPassword">{{ translate('messages.confirm_password') }}</label>
                                    <input type="password" name="confirm-password" class="form-control __form-control form-control __form-control-user"
                                        minlength="6" id="exampleRepeatPassword"
                                        placeholder="{{ translate('messages.password_length_placeholder', ['length' => '6+']) }}"
                                        required value="{{ old('confirm-password') }}">
                                    <div class="pass invalid-feedback">{{ translate('messages.password_not_matched') }}</div>
                                </div>

                            </div>
                            <div class="row mt-5">
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
                </div>
                <div class="text-end">
                    <button type="submit" class="cmn--btn rounded-md border-0 outline-0">{{ translate('messages.submit') }}</button>
                </div>
            </form>
        </div>
    </section>

    @endsection
    @push('script_2')

        <script>
            $('#exampleInputPassword ,#exampleRepeatPassword').on('keyup', function() {
                var pass = $("#exampleInputPassword").val();
                var passRepeat = $("#exampleRepeatPassword").val();
                if (pass == passRepeat) {
                    $('.pass').hide();
                } else {
                    $('.pass').show();
                }
            });


            function readURL(input, viewer) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#' + viewer).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#customFileEg1").change(function() {
                readURL(this, 'logoImageViewer');
            });

            $("#coverImageUpload").change(function() {
                readURL(this, 'coverImageViewer');
            });
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
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
        <script
                src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=drawing,places&v=3.45.8">
        </script>
        {{-- <script>
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

        </script> --}}
        <script>
            @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
            @php($default_location = $default_location->value ? json_decode($default_location->value, true) : 0)
            let myLatlng = {
                lat: {{ $default_location ? $default_location['lat'] : '23.757989' }},
                lng: {{ $default_location ? $default_location['lng'] : '90.360587' }}
            };
            let map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: myLatlng,
            });
            var zonePolygon = null;
            let infoWindow = new google.maps.InfoWindow({
                content: "Click the map to get Lat/Lng!",
                position: myLatlng,
            });
            var bounds = new google.maps.LatLngBounds();

            function initMap() {
                // Create the initial InfoWindow.
                infoWindow.open(map);
                //get current location block
                infoWindow = new google.maps.InfoWindow();
                // Try HTML5 geolocation.
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            myLatlng = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };
                            infoWindow.setPosition(myLatlng);
                            infoWindow.setContent("Location found.");
                            infoWindow.open(map);
                            map.setCenter(myLatlng);
                        },
                        () => {
                            handleLocationError(true, infoWindow, map.getCenter());
                        }
                    );
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infoWindow, map.getCenter());
                }
                //-----end block------
                // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            let markers = [];
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };
                // Create a marker for each place.
                markers.push(
                    new google.maps.Marker({
                    map,
                    icon,
                    title: place.name,
                    position: place.geometry.location,
                    })
                );

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                });
                map.fitBounds(bounds);
            });
            }
            initMap();

            function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                infoWindow.setPosition(pos);
                infoWindow.setContent(
                    browserHasGeolocation ?
                    "Error: The Geolocation service failed." :
                    "Error: Your browser doesn't support geolocation."
                );
                infoWindow.open(map);
            }
            $('#choice_zones').on('change', function() {
                var id = $(this).val();
                $.get({
                    url: '{{ url('/') }}/admin/zone/get-coordinates/' + id,
                    dataType: 'json',
                    success: function(data) {
                        if (zonePolygon) {
                            zonePolygon.setMap(null);
                        }
                        zonePolygon = new google.maps.Polygon({
                            paths: data.coordinates,
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: 'white',
                            fillOpacity: 0,
                        });
                        zonePolygon.setMap(map);
                        zonePolygon.getPaths().forEach(function(path) {
                            path.forEach(function(latlng) {
                                bounds.extend(latlng);
                                map.fitBounds(bounds);
                            });
                        });
                        map.setCenter(data.center);
                        google.maps.event.addListener(zonePolygon, 'click', function(mapsMouseEvent) {
                            infoWindow.close();
                            // Create a new InfoWindow.
                            infoWindow = new google.maps.InfoWindow({
                                position: mapsMouseEvent.latLng,
                                content: JSON.stringify(mapsMouseEvent.latLng.toJSON(),
                                    null, 2),
                            });
                            var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null,
                                2);
                            var coordinates = JSON.parse(coordinates);

                            document.getElementById('latitude').value = coordinates['lat'];
                            document.getElementById('longitude').value = coordinates['lng'];
                            infoWindow.open(map);
                        });
                    },
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

        <script>
            var zone_id = 0;
            $('#choice_zones').on('change', function() {
                if($(this).val())
            {
                zone_id = $(this).val();
            }
            });

            $(document).ready(function() {
                $('#module_id').select2({
                    ajax: {
                        url: '{{url('/')}}/store/get-all-modules/',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                page: params.page,
                                zone_id: zone_id
                            };
                        },
                        processResults: function (data) {
                            return {
                            results: data
                            };
                        },
                        __port: function (params, success, failure) {
                            var $request = $.ajax(params);
    
                            $request.then(success);
                            $request.fail(failure);
    
                            return $request;
                        }
                    }
                });
            });

            $(document).ready(function() {
                $('.js-example-basic-single').select2();
            });
    
        </script>
        
    <script src="{{ asset('public/assets/landing/js/select2.min.js') }}"></script>
    @endpush

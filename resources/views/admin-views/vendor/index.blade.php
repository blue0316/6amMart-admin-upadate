@extends('layouts.admin.app')

@section('title',translate('messages.add_new_store'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/store.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.add')}} {{translate('messages.new')}} {{translate('messages.store')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="{{route('admin.store.store')}}" method="post" enctype="multipart/form-data" class="js-validate" id="vendor_form">
            @csrf

            <div class="row g-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title m-0 d-flex align-items-center">
                                <img class="mr-2 align-self-start w--20" src="{{asset('public/assets/admin/img/resturant.png')}}" alt="instructions">
                                <span>{{translate('store_information')}}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-0">
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="name">{{translate('messages.store')}} {{translate('messages.name')}}</label>
                                        <input type="text" name="name" class="form-control" placeholder="{{translate('messages.store')}} {{translate('messages.name')}}" value="{{old('name')}}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="address">{{translate('messages.store')}} {{translate('messages.address')}}</label>
                                        <textarea type="text" name="address" class="form-control h--45px" placeholder="{{translate('messages.store')}} {{translate('messages.address')}}" required >{{old('address')}}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 my-0">
                                <div class="col-lg-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="tax">{{translate('messages.vat/tax')}} (%)</label>
                                        <input type="number" name="tax" class="form-control" placeholder="{{translate('messages.vat/tax')}}" min="0" step=".01" required value="{{old('tax')}}">
                                    </div>
                                </div>
                                <div class="col-lg-8 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="maximum_delivery_time">{{translate('messages.approx_delivery_time')}}</label>
                                        <div class="input-group">
                                            <input type="number" name="minimum_delivery_time" class="form-control" placeholder="Min: 10" value="{{old('minimum_delivery_time')}}">
                                            <input type="number" name="maximum_delivery_time" class="form-control" placeholder="Max: 20" value="{{old('maximum_delivery_time')}}">
                                            <select name="delivery_time_type" class="form-control text-capitalize" id="" required>
                                                <option value="min">{{translate('messages.minutes')}}</option>
                                                <option value="hours">{{translate('messages.hours')}}</option>
                                                <option value="days">{{translate('messages.days')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 my-0">
                                <div class="col-sm-6 col-lg-4">
                                    <div class="d-flex flex-column">
                                        <label class="input-label mb-0">{{translate('messages.store')}} {{translate('messages.logo')}}<small class="text-danger"> ( {{translate('messages.ratio')}} 1:1 )</small></label>
                                        <center class="py-4 my-auto">
                                            <img class="img--120" id="viewer"
                                            src="{{asset('public/assets/admin/img/100x100/1.png')}}" alt="{{translate('store_logo')}}"/>
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="logo" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                            <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="d-flex flex-column">
                                        <label class="input-label mb-0">{{translate('messages.upload')}} {{translate('messages.cover')}} {{translate('messages.photo')}} <span class="text-danger">({{translate('messages.ratio')}} 2:1)</span></label>
                                        <center class="py-4 my-auto">
                                            <img styl class="img--vertical-120" id="coverImageViewer"
                                            src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="Product thumbnail"/>
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="cover_photo" id="coverImageUpload" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="coverImageUpload">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 my-0">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="input-label" for="choice_zones">{{translate('messages.zone')}}<span
                                                class="input-label-secondary" title="{{translate('messages.select_zone_for_map')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.select_zone_for_map')}}"></span></label>
                                        <select name="zone_id" id="choice_zones" required
                                                class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.zone')}}">
                                                <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.zone')}}</option>
                                            @foreach(\App\Models\Zone::active()->get() as $zone)
                                                @if(isset(auth('admin')->user()->zone_id))
                                                    @if(auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                                    @endif
                                                @else
                                                <option value="{{$zone->id}}">{{$zone->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="latitude">{{translate('messages.latitude')}}<span
                                                class="input-label-secondary" title="{{translate('messages.store_lat_lng_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.store_lat_lng_warning')}}"></span></label>
                                        <input type="text" id="latitude"
                                                name="latitude" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} -94.22213" value="{{old('latitude')}}" required readonly>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label class="input-label" for="longitude">{{translate('messages.longitude')}}<span
                                                class="input-label-secondary" title="{{translate('messages.store_lat_lng_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.store_lat_lng_warning')}}"></span></label>
                                        <input type="text"
                                                name="longitude" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 103.344322" id="longitude" value="{{old('longitude')}}" required readonly>
                                    </div>
                                    {{-- <div class="form-group mb-0">
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" id="module_id" required
                                                class="js-data-example-ajax form-control"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}">
                                        </select>
                                        <small class="text-danger">{{translate('messages.module_change_warning')}}</small>
                                    </div> --}}
                                </div>
                                <div class="col-lg-8">
                                    <input id="pac-input" class="controls rounded"
                                        data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.search_your_location_here') }}" type="text" placeholder="{{ translate('messages.search_here') }}" />
                                    <div id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title m-0 d-flex align-items-center">
                                <span class="card-header-icon mr-2"><i class="tio-user"></i></span>
                                <span>{{translate('messages.owner')}} {{translate('messages.information')}}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="f_name">{{translate('messages.first')}} {{translate('messages.name')}}</label>
                                        <input type="text" name="f_name" class="form-control" placeholder="{{translate('messages.first')}} {{translate('messages.name')}}"
                                                value="{{old('f_name')}}"  required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="l_name">{{translate('messages.last')}} {{translate('messages.name')}}</label>
                                        <input type="text" name="l_name" class="form-control" placeholder="{{translate('messages.last')}} {{translate('messages.name')}}"
                                        value="{{old('l_name')}}"  required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="phone">{{translate('messages.phone')}}</label>
                                        <input type="number" name="phone" class="form-control" placeholder="{{ translate('messages.Ex:') }} 017********"
                                        value="{{old('phone')}}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title m-0 d-flex align-items-center">
                                <span class="card-header-icon mr-2"><i class="tio-user"></i></span>
                                <span>{{translate('messages.account')}} {{translate('messages.information')}}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="email">{{translate('messages.email')}}</label>
                                        <input type="email" name="email" class="form-control" placeholder="{{ translate('messages.Ex:') }} ex@example.com"
                                        value="{{old('email')}}"  required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="js-form-message form-group mb-0">
                                        <label class="input-label" for="signupSrPassword">{{translate('messages.password')}}</label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control" name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" required
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
                                        <input type="password" class="js-toggle-password form-control" name="confirmPassword" id="signupSrConfirmPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" required
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
                <div class="col-lg-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script>
      $(document).on('ready', function () {
        @if (isset(auth('admin')->user()->zone_id))
            $('#choice_zones').trigger('change');
        @endif
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
        });

        $("#coverImageUpload").change(function () {
            readURL(this, 'coverImageViewer');
        });
    </script>

    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '120px',
                groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
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
                    toastr.error('{{translate('messages.please_only_input_png_or_jpg_type_file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{translate('messages.file_size_too_big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&libraries=places&callback=initMap&v=3.45.8"></script>
    <script>
        @php($default_location=\App\Models\BusinessSetting::where('key','default_location')->first())
        @php($default_location=$default_location->value?json_decode($default_location->value, true):0)
        let myLatlng = { lat: {{$default_location?$default_location['lat']:'23.757989'}}, lng: {{$default_location?$default_location['lng']:'90.360587'}} };
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
                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();
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
                browserHasGeolocation
                ? "Error: The Geolocation service failed."
                : "Error: Your browser doesn't support geolocation."
            );
            infoWindow.open(map);
        }
        $('#choice_zones').on('change', function(){
            var id = $(this).val();
            $.get({
                url: '{{url('/')}}/admin/zone/get-coordinates/'+id,
                dataType: 'json',
                success: function (data) {
                    if(zonePolygon)
                    {
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
                    google.maps.event.addListener(zonePolygon, 'click', function (mapsMouseEvent) {
                        infoWindow.close();
                        // Create a new InfoWindow.
                        infoWindow = new google.maps.InfoWindow({
                        position: mapsMouseEvent.latLng,
                        content: JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2),
                        });
                        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                        var coordinates = JSON.parse(coordinates);

                        document.getElementById('latitude').value = coordinates['lat'];
                        document.getElementById('longitude').value = coordinates['lng'];
                        infoWindow.open(map);
                    });
                },
            });
        });
        $("#vendor_form").on('keydown', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        })
    </script>
        <script>
            $('#reset_btn').click(function(){
                $('#viewer').attr('src', "{{ asset('public/assets/admin/img/400x400/img2.jpg') }}");
                $('#customFileEg1').val(null);
                $('#coverImageViewer').attr('src', "{{ asset('public/assets/admin/img/900x400/img1.jpg') }}");
                $('#coverImageUpload').val(null);
                $('#choice_zones').val(null).trigger('change');
                $('#module_id').val(null).trigger('change');
                zonePolygon.setMap(null);
                $('#coordinates').val(null);
                $('#latitude').val(null);
                $('#longitude').val(null);
            })
        </script>

        <script>
            var zone_id = 0;
            $('#choice_zones').on('change', function() {
                if($(this).val())
            {
                zone_id = $(this).val();
            }
            });

            

            $('#module_id').select2({
                    ajax: {
                         url: '{{url('/')}}/store/get-all-modules',
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
        </script>
@endpush

@extends('layouts.admin.app')

@section('title','Update restaurant info')


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.update')}} {{translate('messages.store')}}</span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="{{route('admin.store.update',[$store['id']])}}" method="post" class="js-validate"
                enctype="multipart/form-data" id="vendor_form">
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
                                <div class="col-lg-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="name">{{translate('messages.store')}} {{translate('messages.name')}}</label>
                                        <input type="text" name="name" class="form-control" placeholder="{{translate('messages.store')}} {{translate('messages.name')}}"
                                                required value="{{$store->name}}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="address">{{translate('messages.vat/tax')}} (%)</label>
                                        <input type="number" name="tax" class="form-control" placeholder="{{translate('messages.vat/tax')}}" min="0" step=".01" required value="{{$store->tax}}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="address">{{translate('messages.store')}} {{translate('messages.address')}}</label>
                                        <textarea  type="text" name="address" class="form-control h--45px" placeholder="{{translate('messages.store')}} {{translate('messages.address')}}"
                                                required>{{$store->address}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 my-0">
                                <div class="col-lg-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" required class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}" disabled>
                                                <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}" {{$store->module_id==$module->id?'selected':''}}>{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-sm-6">
                                    @php

                                        $delivery_time_start = preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $store->delivery_time??'')?explode('-',$store->delivery_time)[0]:10;
                                        $delivery_time_end = preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $store->delivery_time??'')?explode(' ',explode('-',$store->delivery_time)[1])[0]:30;
                                        $delivery_time_type = preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $store->delivery_time??'')?explode(' ',explode('-',$store->delivery_time)[1])[1]:'min';
                                    @endphp
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="maximum_delivery_time">{{translate('messages.approx_delivery_time')}}</label>
                                        <div class="input-group">
                                            <input type="number" name="minimum_delivery_time" class="form-control" placeholder="Min: 10" value="{{$delivery_time_start}}" title="{{translate('messages.minimum_delivery_time')}}">
                                            <input type="number" name="maximum_delivery_time" class="form-control" placeholder="Max: 20" value="{{$delivery_time_end}}" title="{{translate('messages.maximum_delivery_time')}}">
                                            <select name="delivery_time_type" class="form-control text-capitalize" id="" required>
                                                <option value="min" {{$delivery_time_type=='min'?'selected':''}}>{{translate('messages.minutes')}}</option>
                                                <option value="hours" {{$delivery_time_type=='hours'?'selected':''}}>{{translate('messages.hours')}}</option>
                                                <option value="days" {{$delivery_time_type=='days'?'selected':''}}>{{translate('messages.days')}}</option>
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
                                                src="{{asset('storage/app/public/store').'/'.$store->logo}}" alt="{{$store->name}}" onerror='this.src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}"'/>
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="logo" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="d-flex flex-column">
                                        <label class="input-label mb-0">{{translate('messages.upload')}} {{translate('messages.cover')}} {{translate('messages.photo')}} <span class="text-danger">({{translate('messages.ratio')}} 2:1)</span></label>
                                        <center class="py-4 my-auto">
                                            <img styl class="img--vertical-120" id="coverImageViewer"
                                            onerror="this.src='{{asset('public/assets/admin/img/900x400/img1.jpg')}}'"
                                            src="{{asset('storage/app/public/store/cover/'.$store->cover_photo)}}" alt="Product thumbnail"/>
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
                                        <select name="zone_id" id="choice_zones" onchange="get_zone_data(this.value)" data-placeholder="{{translate('messages.select')}} {{translate('messages.zone')}}"
                                                class="form-control js-select2-custom">
                                            @foreach(\App\Models\Zone::active()->get() as $zone)
                                                @if(isset(auth('admin')->user()->zone_id))
                                                    @if(auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{$zone->id}}" {{$store->zone_id == $zone->id? 'selected': ''}}>{{$zone->name}}</option>
                                                    @endif
                                                @else
                                                    <option value="{{$zone->id}}" {{$store->zone_id == $zone->id? 'selected': ''}}>{{$zone->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.latitude')}}<span
                                                class="input-label-secondary" title="{{translate('messages.store_lat_lng_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.store_lat_lng_warning')}}"></span></label>
                                        <input type="text"
                                                name="latitude" class="form-control" id="latitude"
                                                placeholder="{{ translate('messages.Ex:') }} -94.22213" value="{{$store->latitude}}" readonly>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.longitude')}}<span
                                                class="input-label-secondary" title="{{translate('messages.store_lat_lng_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.store_lat_lng_warning')}}"></span></label>
                                        <input type="text"
                                                name="longitude" class="form-control" id="longitude"
                                                placeholder="{{ translate('messages.Ex:') }} 103.344322" value="{{$store->longitude}}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <input id="pac-input" class="controls rounded" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.search_your_location_here') }}" type="text"
                                        placeholder="{{ translate('messages.search_here') }}" />
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
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.first')}} {{translate('messages.name')}}</label>
                                        <input type="text" name="f_name" class="form-control" placeholder="{{translate('messages.first')}} {{translate('messages.name')}}" value="{{$store->vendor->f_name}}"
                                                required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.last')}} {{translate('messages.name')}}</label>
                                        <input type="text" name="l_name" class="form-control" placeholder="{{translate('messages.last')}} {{translate('messages.name')}}"
                                        value="{{$store->vendor->l_name}}"  required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.phone')}}</label>
                                        <input type="number" name="phone" class="form-control" placeholder="{{ translate('messages.Ex:') }} 017********"
                                        value="{{$store->phone}}"   required>
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
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.email')}}</label>
                                        <input type="email" name="email" class="form-control" placeholder="{{ translate('messages.Ex:') }} ex@example.com" value="{{$store->email}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="js-form-message form-group mb-0">
                                        <label class="input-label" for="signupSrPassword">Password</label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control" name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" aria-label="6+ characters required"
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
                                <div class="col-md-4 col-sm-6">
                                    <div class="js-form-message form-group mb-0">
                                        <label class="input-label" for="signupSrConfirmPassword">Confirm password</label>

                                        <div class="input-group input-group-merge">
                                        <input type="password" class="js-toggle-password form-control" name="confirmPassword" id="signupSrConfirmPassword" placeholder="{{translate('messages.password_length_placeholder', ['length'=>'6+'])}}" aria-label="6+ characters required"                                      data-msg="Password does not match the confirm password."
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
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </div>
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

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
        });

        $("#coverImageUpload").change(function () {
            readURL(this, 'coverImageViewer');
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&libraries=places&callback=initMap&v=3.45.8"></script>
    <script>
        let myLatlng = { lat: {{$store->latitude}}, lng: {{$store->longitude}} };
        const map = new google.maps.Map(document.getElementById("map"), {
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
            new google.maps.Marker({
                position: { lat: {{$store->latitude}}, lng: {{$store->longitude}} },
                map,
                title: "{{$store->name}}",
            });
            infoWindow.open(map);
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
        function get_zone_data(id)
        {
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
        }
        $(document).on('ready', function (){
            var id = $('#choice_zones').val();
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
    </script>
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

        get_zone_data({{$store->zone_id}});
      });
        $("#vendor_form").on('keydown', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        })
    </script>
    <script>
        $('#reset_btn').click(function(){
            $('#viewer').attr('src', "{{asset('storage/app/public/store').'/'.$store->logo}}");
            $('#customFileEg1').val(null);
            $('#coverImageViewer').attr('src', "{{asset('storage/app/public/store/cover/'.$store->cover_photo)}}");
            $('#coverImageUpload').val(null);
            $('#choice_zones').val("{{$store->zone_id}}").trigger('change');
            $('#latitude').val("{{$store->latitude}}");
            $('#longitude').val("{{$store->longitude}}");
        })
    </script>
@endpush

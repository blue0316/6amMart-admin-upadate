@extends('layouts.admin.app')

@section('title',translate('Update Zone'))

@push('css_or_js')

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
                    {{translate('messages.zone')}} {{translate('messages.update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="{{route('admin.business-settings.zone.update', $zone->id)}}" method="post" id="zone_form" class="shadow--card">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <div class="zone-setup-instructions">
                        <div class="zone-setup-top">
                            <h6 class="subtitle">{{ translate('messages.Instructions') }}</h6>
                            <p>
                                {{ translate('messages.Create zone by click on map and connect the dots together') }}
                            </p>
                        </div>
                        <div class="zone-setup-item">
                            <div class="zone-setup-icon">
                                <i class="tio-hand-draw"></i>
                            </div>
                            <div class="info">
                                {{ translate('messages.Use this to drag map to find proper area') }}
                            </div>
                        </div>
                        <div class="zone-setup-item">
                            <div class="zone-setup-icon">
                                <i class="tio-free-transform"></i>
                            </div>
                            <div class="info">
                                {{ translate('messages.Click this icon to start pin points in the map and connect them to draw a zone . Minimum 3  points required') }}
                            </div>
                        </div>
                        <div class="instructions-image mt-4">
                            <img src="{{asset('public/assets/admin/img/instructions.gif')}}" alt="instructions">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-7 zone-setup">
                    <div class="pl-xl-5 pl-xxl-0">
                        <div class="form-group">
                            <label class="input-label"
                                    for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="{{translate('messages.new_zone')}}" value="{{$zone->name}}" required>
                        </div>
                        {{-- <div class="d-flex flex-wrap select--all-checkes">
                            <h5 class="input-label m-0 text-capitalize">{{translate('messages.Payment Method')}} </h5>
                        </div>
                        <div class="check--item-wrapper mb-1">
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="cash_on_delivery" value="cash_on_delivery" class="form-check-input"
                                           id="cash_on_delivery" {{$zone->cash_on_delivery == 1 ?'checked':''}}>
                                    <label class="form-check-label qcont text-dark" for="cash_on_delivery">{{translate('messages.Cash On Delivery')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="digital_payment" value="digital_payment" class="form-check-input"
                                           id="digital_payment" {{$zone->digital_payment == 1 ?'checked':''}}>
                                    <label class="form-check-label qcont text-dark" for="digital_payment">{{translate('messages.digital payment')}}</label>
                                </div>
                            </div>
                        </div> --}}
                        <div class="form-group d-none">
                            <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Coordinates') }}<span class="input-label-secondary" title="{{translate('messages.draw_your_zone_on_the_map')}}">{{translate('messages.draw_your_zone_on_the_map')}}</span></label>
                                    <textarea type="text" name="coordinates"  id="coordinates" class="form-control">@foreach($zone->coordinates[0] as $key=>$coords)<?php if(count($zone->coordinates[0]) != $key+1) {if($key != 0) echo(','); ?>({{$coords->getLat()}}, {{$coords->getLng()}})<?php } ?>@endforeach
                            </textarea>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label class="input-label">
                                        {{ translate('messages.Minimum delivery charge') }} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                    </label>
                                    <input type="number" id="minimum_delivery_charge" name="minimum_delivery_charge" class="form-control h--45px" placeholder="{{ translate('Ex:') }} 10" value="{{$zone->minimum_shipping_charge}}" required="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label class="input-label">
                                        {{ translate('messages.Delivery charge per KM') }} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                    </label>
                                    <input type="number" id="delivery_charge_per_km" name="per_km_delivery_charge" class="form-control h--45px" placeholder="{{ translate('messages.Ex:') }} 10" value="{{$zone->per_km_shipping_charge}}"  required="">
                                </div>
                            </div>
                        </div> --}}
                        <div class="map-warper rounded mt-0">
                            <input id="pac-input" class="controls rounded initial--33" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                            <div id="map-canvas" class="initial--34"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn--container mt-3 justify-content-end">
                <button id="reset_btn" type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
<script src="https://maps.googleapis.com/maps/api/js?v=3.45.8&key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&libraries=drawing,places"></script>
<script>
    auto_grow();
    function auto_grow() {
        let element = document.getElementById("coordinates");
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }

</script>
<script>
    var map; // Global declaration of the map
    var lat_longs = new Array();
    var drawingManager;
    var lastpolygon = null;
    var bounds = new google.maps.LatLngBounds();
    var polygons = [];


    function resetMap(controlDiv) {
        // Set CSS for the control border.
        const controlUI = document.createElement("div");
        controlUI.style.backgroundColor = "#fff";
        controlUI.style.border = "2px solid #fff";
        controlUI.style.borderRadius = "3px";
        controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
        controlUI.style.cursor = "pointer";
        controlUI.style.marginTop = "8px";
        controlUI.style.marginBottom = "22px";
        controlUI.style.textAlign = "center";
        controlUI.title = "Reset map";
        controlDiv.appendChild(controlUI);
        // Set CSS for the control interior.
        const controlText = document.createElement("div");
        controlText.style.color = "rgb(25,25,25)";
        controlText.style.fontFamily = "Roboto,Arial,sans-serif";
        controlText.style.fontSize = "10px";
        controlText.style.lineHeight = "16px";
        controlText.style.paddingLeft = "2px";
        controlText.style.paddingRight = "2px";
        controlText.innerHTML = "X";
        controlUI.appendChild(controlText);
        // Setup the click event listeners: simply set the map to Chicago.
        controlUI.addEventListener("click", () => {
            lastpolygon.setMap(null);
            $('#coordinates').val('');

        });
    }

    function initialize() {
        var myLatlng = new google.maps.LatLng({{trim(explode(' ',$zone->center)[1], 'POINT()')}}, {{trim(explode(' ',$zone->center)[0], 'POINT()')}});
        var myOptions = {
            zoom: 13,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

        const polygonCoords = [

            @foreach($zone->coordinates[0] as $coords)
            { lat: {{$coords->getLat()}}, lng: {{$coords->getLng()}} },
            @endforeach
        ];

        var zonePolygon = new google.maps.Polygon({
            paths: polygonCoords,
            strokeColor: "#050df2",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillOpacity: 0,
        });

        zonePolygon.setMap(map);

        zonePolygon.getPaths().forEach(function(path) {
            path.forEach(function(latlng) {
                bounds.extend(latlng);
                map.fitBounds(bounds);
            });
        });


        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [google.maps.drawing.OverlayType.POLYGON]
            },
            polygonOptions: {
            editable: true
            }
        });
        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
            var newShape = event.overlay;
            newShape.type = event.type;
        });

        google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
            if(lastpolygon)
                {
                    lastpolygon.setMap(null);
                }
                $('#coordinates').val(event.overlay.getPath().getArray());
                lastpolygon = event.overlay;
                auto_grow();
        });
        const resetDiv = document.createElement("div");
        resetMap(resetDiv, lastpolygon);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(resetDiv);

        // Create the search box and link it to the UI element.
        const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            let markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
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
    google.maps.event.addDomListener(window, 'load', initialize);

    function set_all_zones()
    {
        $.get({
            url: '{{route('admin.zone.zoneCoordinates')}}/{{$zone->id}}',
            dataType: 'json',
            success: function (data) {

                console.log(data);
                for(var i=0; i<data.length;i++)
                {
                    polygons.push(new google.maps.Polygon({
                        paths: data[i],
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.1,
                    }));
                    polygons[i].setMap(map);
                }

            },
        });
    }
    $(document).on('ready', function(){
        set_all_zones();
        $("#zone_form").on('keydown', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        })
    });

    $('#reset_btn').click(function(){
        // $('#zone_name').val('');
        // $('#coordinates').val('');
        // $('#min_delivery_charge').val('');
        // $('#delivery_charge_per_km').val('');
        location.reload(true);
    })

</script>
@endpush

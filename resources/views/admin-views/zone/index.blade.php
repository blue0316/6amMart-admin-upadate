@extends('layouts.admin.app')

@section('title',translate('Add new zone'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/zone.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.Add new zone')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <form action="{{route('admin.zone.store')}}" method="post" id="zone_form" class="shadow--card">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-md-5">
                            <div class="zone-setup-instructions">
                                <div class="zone-setup-top">
                                    <h6 class="subtitle">{{ translate('Instructions') }}</h6>
                                    <p>
                                        {{ translate('Create zone by click on map and connect the dots together') }}
                                    </p>
                                </div>
                                <div class="zone-setup-item">
                                    <div class="zone-setup-icon">
                                        <i class="tio-hand-draw"></i>
                                    </div>
                                    <div class="info">
                                        {{ translate('Use this to drag map to find proper area') }}
                                    </div>
                                </div>
                                <div class="zone-setup-item">
                                    <div class="zone-setup-icon">
                                        <i class="tio-free-transform"></i>
                                    </div>
                                    <div class="info">
                                        {{ translate('Click this icon to start pin points in the map and connect them to draw a zone . Minimum 3  points required') }}
                                    </div>
                                </div>
                                <div class="instructions-image mt-4">
                                    <img src="{{asset('public/assets/admin/img/instructions.gif')}}" alt="instructions">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-7 zone-setup">
                            <div class="pl-xl-5 pl-xxl-0">
                                <div class="form-group mb-3">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="{{translate('messages.new_zone')}}" value="{{old('name')}}" required>
                                </div>
                                {{-- <div class="d-flex flex-wrap select--all-checkes">
                                    <h5 class="input-label m-0 text-capitalize">{{translate('messages.Payment Method')}} </h5>
                                </div>
                                <div class="check--item-wrapper mb-1">
                                    <div class="check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="cash_on_delivery" value="cash_on_delivery" class="form-check-input"
                                                   id="cash_on_delivery">
                                            <label class="form-check-label qcont text-dark" for="cash_on_delivery">{{translate('messages.Cash On Delivery')}}</label>
                                        </div>
                                    </div>
                                    <div class="check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="digital_payment" value="digital_payment" class="form-check-input"
                                                   id="digital_payment">
                                            <label class="form-check-label qcont text-dark" for="digital_payment">{{translate('messages.digital payment')}}</label>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="form-group mb-3 d-none">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('Coordinates') }}<span class="input-label-secondary" title="{{translate('messages.draw_your_zone_on_the_map')}}">{{translate('messages.draw_your_zone_on_the_map')}}</span></label>
                                        <textarea type="text" rows="8" name="coordinates"  id="coordinates" class="form-control" readonly></textarea>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="input-label">
                                                {{ translate('Minimum delivery charge') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                            </label>
                                            <input type="number" id="minimum_delivery_charge" name="minimum_delivery_charge" class="form-control h--45px" placeholder="{{ translate('Ex:') }} 10" required="">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="input-label">
                                                {{ translate('Delivery charge per KM') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                            </label>
                                            <input type="number" id="delivery_charge_per_km" name="per_km_delivery_charge" class="form-control h--45px" placeholder="{{ translate('messages.Ex:') }} 10" required="">
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="map-warper rounded mt-0">
                                    <input id="pac-input" class="controls rounded" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                    <div id="map-canvas" class="rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container mt-3 justify-content-end">
                        <button id="reset_btn" type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.zone')}} {{translate('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$zones->total()}}</span>
                            </h5>
                            <form action="javascript:" id="search-form"  class="search-form">
                                            <!-- Search -->
                                @csrf
                                <div class="input-group input--group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{translate('messages.ex_:_search_name')}}" aria-label="{{translate('messages.search')}}" required>
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>

                                </div>
                                <!-- End Search -->
                            </form>
                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    {{-- <span class="dropdown-header">{{ translate('messages.options') }}</span>
                                    <a id="export-copy" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/illustrations/copy.svg"
                                            alt="Image Description">
                                        {{ translate('messages.copy') }}
                                    </a>
                                    <a id="export-print" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/illustrations/print.svg"
                                            alt="Image Description">
                                        {{ translate('messages.print') }}
                                    </a>
                                    <div class="dropdown-divider"></div> --}}
                                    <span class="dropdown-header">{{ translate('messages.download') }}
                                        {{ translate('messages.options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{route('admin.zone.export', ['type'=>'excel'])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item" href="{{route('admin.zone.export', ['type'=>'csv'])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>
                                    {{-- <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/pdf.svg"
                                            alt="Image Description">
                                        {{ translate('messages.pdf') }}
                                    </a> --}}
                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('messages.SL') }}</th>
                                <th class="border-0">{{translate('messages.id')}}</th>
                                <th class="border-0" >{{translate('messages.name')}}</th>
                                <th class="border-0" >{{translate('messages.stores')}}</th>
                                <th class="border-0" >{{translate('messages.deliverymen')}}</th>
                                <th class="border-0" >{{translate('messages.status')}}</th>
                                <th class="border-0" >{{translate('messages.digital_payment')}}</th>
                                <th class="border-0" >{{translate('messages.cash_on_delivery')}}</th>
                                <th class="border-0 text-center" >{{translate('messages.action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($zones as $key=>$zone)
                                <tr>
                                    <td>{{$key+$zones->firstItem()}}</td>
                                    <td>{{$zone->id}}</td>
                                    <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{$zone['name']}}
                                    </span>
                                    </td>
                                    <td>{{$zone->stores_count}}</td>
                                    <td>{{$zone->deliverymen_count}}</td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$zone->id}}">
                                            <input type="checkbox" onclick="status_form_alert('status-{{$zone['id']}}','{{ translate('Want to change status for this zone ?') }}', event)" class="toggle-switch-input" id="stocksCheckbox{{$zone->id}}" {{$zone->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <form action="{{route('admin.zone.status',[$zone['id'],$zone->status?0:1])}}" method="get" id="status-{{$zone['id']}}">
                                        </form>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="digital_paymentCheckbox{{$zone->id}}">
                                            <input type="checkbox" onclick="status_form_alert('digital_payment-{{$zone['id']}}','{{ translate('Want to change digital payment for this zone ?') }}', event)" class="toggle-switch-input" id="digital_paymentCheckbox{{$zone->id}}" {{$zone->digital_payment?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <form action="{{route('admin.zone.digital-payment',[$zone['id'],$zone->digital_payment?0:1])}}" method="get" id="digital_payment-{{$zone['id']}}">
                                        </form>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="cashOnDeliveryCheckbox{{$zone->id}}">
                                            <input type="checkbox" onclick="status_form_alert('cash_on_delivery-{{$zone['id']}}','{{ translate('Want to change cash on delivery for this zone ?') }}', event)" class="toggle-switch-input" id="cashOnDeliveryCheckbox{{$zone->id}}" {{$zone->cash_on_delivery?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <form action="{{route('admin.zone.cash-on-delivery',[$zone['id'],$zone->cash_on_delivery?0:1])}}" method="get" id="cash_on_delivery-{{$zone['id']}}">
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary"
                                                href="{{route('admin.business-settings.zone.edit',[$zone['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.zone')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--warning btn-outline-warning" title="Module Setup"
                                                href="{{route('admin.business-settings.zone.module-setup',[$zone['id']])}}"><i class="tio-settings"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('zone-{{$zone['id']}}','{{ translate('Want to delete this zone ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.zone')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.zone.delete',[$zone['id']])}}" method="post" id="zone-{{$zone['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($zones) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $zones->withQueryString()->links() !!}
                        </div>
                        @if(count($zones) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function status_form_alert(id, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.Yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#'+id).submit()
                }
            })
        }
    auto_grow();
    function auto_grow() {
        let element = document.getElementById("coordinates");
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }

    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $("#zone_form").on('keydown', function(e){
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            })
        });
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&callback=initialize&libraries=drawing,places&v=3.49"></script>

    <script>
        var map; // Global declaration of the map
        var drawingManager;
        var lastpolygon = null;
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
            @php($default_location=\App\Models\BusinessSetting::where('key','default_location')->first())
            @php($default_location=$default_location->value?json_decode($default_location->value, true):0)
            var myLatlng = { lat: {{$default_location?$default_location['lat']:'23.757989'}}, lng: {{$default_location?$default_location['lng']:'90.360587'}} };


            var myOptions = {
                zoom: 13,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
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


            //get current location block
            // infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    map.setCenter(pos);
                });
            }

            drawingManager.addListener("overlaycomplete", function(event) {
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

        // initialize();


        function set_all_zones()
        {
            $.get({
                url: '{{route('admin.zone.zoneCoordinates')}}',
                dataType: 'json',
                success: function (data) {
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
        });

    </script>
    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.zone.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.total);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
        <script>
            $('#reset_btn').click(function(){
                $('#name').val(null);
                $('#minimum_delivery_charge').val(null);
                $('#delivery_charge_per_km').val(null);

                lastpolygon.setMap(null);
                $('#coordinates').val(null);
            })
        </script>
@endpush

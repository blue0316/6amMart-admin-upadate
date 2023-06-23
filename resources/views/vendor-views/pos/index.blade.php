@extends('layouts.vendor.app')

@section('title', translate('messages.POS Orders'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css" media="print">
        @page {
            size: auto;
            /* auto is the initial value */
            margin: 0;
            /* this affects the margin in the printer settings */
        }
    </style>
@endpush

@section('content')
    @php($store_data = \App\CentralLogics\Helpers::get_store_data())
    <section class="section-content padding-y-sm bg-default mt-1">
        <div class="content container-fluid">
            <div class="d-flex flex-wrap">
                <div class="order--pos-left">
                    <div class="card h-100">
                        <div class="card-header bg-light border-0">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-incognito"></i>
                                </span>
                                <span>
                                    {{ translate('products') }}
                                </span>
                            </h5>
                        </div>
                        <div class="card-header d-flex flex-wrap justify-content-between ">
                            <div class="w-100">
                                <div class="row g-2 justify-content-around">
                                    <div class="col-sm-6">
                                        <form id="search-form" class="search-form m-0">
                                            <!-- Search -->
                                            <div class="input-group input--group">
                                                <input id="datatableSearch" type="search"
                                                    value="{{ $keyword ? $keyword : '' }}" name="search"
                                                    class="form-control h--45px"
                                                    placeholder="{{ translate('messages.ex_:_search_here') }}"
                                                    aria-label="{{ translate('messages.search_here') }}">
                                                <button class="btn btn--secondary h--45px" type="button"><i
                                                        class="tio-search"></i></button>
                                            </div>
                                            <!-- End Search -->
                                        </form>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <select name="category" id="category" class="form-control js-select2-custom"
                                                title="{{ translate('messages.select') }} {{ translate('messages.category') }}"
                                                onchange="set_category_filter('{{url()->full()}}',this.value)">
                                                <option value="">{{ translate('messages.all_categories') }}</option>
                                                @foreach ($categories as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $category == $item->id ? 'selected' : '' }}>
                                                        {{ Str::limit($item->name, 20, '...') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column" id="items">
                            <div class="row g-3 mb-auto">
                                @foreach ($products as $product)
                                    <div class="order--item-box item-box">
                                        @include('vendor-views.pos._single_product', [
                                            'product' => $product,
                                            'store' => $store_data,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                            @if (count($products) >= 13)
                                <hr>
                            @endif
                            <div class="page-area mt-2">
                                {!! $products->withQueryString()->links() !!}
                            </div>
                            @if (count($products) === 0)
                                <div class="search--no-found">
                                    <img src="{{ asset('public/assets/admin/img/search-icon.png') }}" alt="img">
                                    <p>
                                        {{ translate('messages.no_products_on_store_pos_search') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="order--pos-right">
                    <div class="card">
                        <div class="card-header bg-light border-0 m-1">
                            <h5 class="card-title">
                                <span>
                                    {{ translate('messages.Billing Section') }}
                                </span>
                            </h5>
                        </div>
                        <div class="w-100">
                            <div class="d-flex flex-wrap flex-row p-2 add--customer-btn">
                                <select id='customer' name="customer_id"
                                    data-placeholder="{{ translate('messages.walk_in_customer') }}"
                                    class="js-data-example-ajax form-control"></select>
                                <button class="btn btn--primary" data-toggle="modal"
                                    data-target="#add-customer">{{ translate('messages.add_new_customer') }}</button>
                            </div>
                            @if ($store_data->self_delivery_system == 1)
                                <div class="pos--delivery-options">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title">
                                            <span class="card-title-icon">
                                                <i class="tio-user"></i>
                                            </span>
                                            <span>{{ translate('messages.Delivery Information') }}</span>
                                        </h5>
                                        <span class="delivery--edit-icon text-primary" id="delivery_address"
                                            data-toggle="modal" data-target="#paymentModal"><i class="tio-edit"></i></span>
                                    </div>
                                    <div class="pos--delivery-options-info d-flex flex-wrap" id="del-add">
                                        @include('vendor-views.pos._address')
                                    </div>
                                </div>
                            @endif
                        </div>


                        <div class='w-100' id="cart">
                            @include('vendor-views.pos._cart')
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- container //  -->
    </section>

    <div class="modal fade" id="add-customer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.add_new_customer') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('vendor.pos.customer-store')}}" method="post" id="product_form">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('first_name') }} <span
                                            class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="f_name" class="form-control"
                                        placeholder="{{ translate('first_name') }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('last_name') }} <span
                                            class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="l_name" class="form-control"
                                        placeholder="{{ translate('last_name') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('email') }}<span
                                            class="input-label-secondary text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="{{ translate('Ex_:_ex@example.com') }}" required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="input-label">{{ translate('phone') }}
                                        ({{ translate('with_country_code') }})<span
                                            class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                        placeholder="{{ translate('phone') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                            <button type="submit" id="submit_new_customer"
                                class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
    <div class="modal fade" id="quick-view" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="quick-view-modal">

            </div>
        </div>
    </div>

    @php($order = \App\Models\Order::find(session('last_order')))
    @if ($order)
        @php(session(['last_order' => false]))
        <div class="modal fade" id="print-invoice" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('messages.print') }} {{ translate('messages.invoice') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row ff-emoji">
                        <div class="col-md-12">
                            <center>
                                <input type="button" class="btn btn--primary non-printable text-white"
                                    onclick="printDiv('printableArea')" value="Proceed, If thermal printer is ready." />
                                <a href="{{ url()->previous() }}" class="btn btn-danger non-printable">Back</a>
                            </center>
                            <hr class="non-printable">
                        </div>
                        <div class="row m-auto" id="print-modal-content">
                            @include('vendor-views.pos.order.invoice')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif


@endsection

@push('script_2')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places&callback=initMap&v=3.49">
    </script>
    <script>
        function initMap() {
            let map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: {
                    lat: {{ $store_data ? $store_data['latitude'] : '23.757989' }},
                    lng: {{ $store_data ? $store_data['longitude'] : '90.360587' }}
                }
            });

            let zonePolygon = null;

            //get current location block
            let infoWindow = new google.maps.InfoWindow();
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
            const bounds = new google.maps.LatLngBounds();
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
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    console.log(place.geometry.location);
                    if (!google.maps.geometry.poly.containsLocation(
                            place.geometry.location,
                            zonePolygon
                        )) {
                        toastr.error('{{ translate('messages.out_of_coverage') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        return false;
                    }

                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();

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
            @if ($store_data)
                $.get({
                    url: '{{ url('/') }}/admin/zone/get-coordinates/{{ $store_data->zone_id }}',
                    dataType: 'json',
                    success: function(data) {
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
                                content: JSON.stringify(mapsMouseEvent.latLng.toJSON(), null,
                                    2),
                            });
                            var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                            var coordinates = JSON.parse(coordinates);

                            document.getElementById('latitude').value = coordinates['lat'];
                            document.getElementById('longitude').value = coordinates['lng'];
                            infoWindow.open(map);

                            var geocoder = geocoder = new google.maps.Geocoder();
                            var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);

                            geocoder.geocode({
                                'latLng': latlng
                            }, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    if (results[1]) {
                                        var address = results[1].formatted_address;
                                        // initialize services
                                        const geocoder = new google.maps.Geocoder();
                                        const service = new google.maps.DistanceMatrixService();
                                        // build request
                                        const origin1 = {
                                            lat: {{ $store_data['latitude'] }},
                                            lng: {{ $store_data['longitude'] }}
                                        };
                                        const origin2 = "{{ $store_data->address }}";
                                        const destinationA = address;
                                        const destinationB = {
                                            lat: coordinates['lat'],
                                            lng: coordinates['lng']
                                        };
                                        const request = {
                                            origins: [origin1, origin2],
                                            destinations: [destinationA, destinationB],
                                            travelMode: google.maps.TravelMode.DRIVING,
                                            unitSystem: google.maps.UnitSystem.METRIC,
                                            avoidHighways: false,
                                            avoidTolls: false,
                                        };

                                        // get distance matrix response
                                        service.getDistanceMatrix(request).then((response) => {
                                            // put response
                                            var distancMeter = response.rows[0]
                                                .elements[0].distance['value'];
                                            console.log(distancMeter);
                                            var distanceMile = distancMeter / 1000;
                                            var distancMileResult = Math.round((
                                                    distanceMile + Number.EPSILON) *
                                                100) / 100;
                                            console.log(distancMileResult);
                                            <?php
                                            $per_km_shipping_charge = (float) \App\Models\BusinessSetting::where(['key' => 'per_km_shipping_charge'])->first()->value;
                                            $minimum_shipping_charge = (float) \App\Models\BusinessSetting::where(['key' => 'minimum_shipping_charge'])->first()->value;
                                            // $original_delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
                                            ?>

                                            var original_delivery_charge = (
                                                    distancMileResult *
                                                    {{ $per_km_shipping_charge }} >
                                                    {{ $minimum_shipping_charge }}) ?
                                                distancMileResult *
                                                {{ $per_km_shipping_charge }} :
                                                {{ $minimum_shipping_charge }};
                                            var delivery_charge = Math.round((
                                                original_delivery_charge +
                                                Number.EPSILON) * 100) / 100;
                                            document.getElementById('delivery_fee')
                                                .value = delivery_charge;
                                            $('#delivery_fee').siblings('strong').html(
                                                delivery_charge +
                                                '{{ \App\CentralLogics\Helpers::currency_symbol() }}'
                                                );

                                            console.log(Math.round((
                                                original_delivery_charge +
                                                Number.EPSILON) * 100) / 100);
                                        });

                                    }
                                }
                            });
                        });
                    },
                });
            @endif

        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation ?
                "Error: {{ translate('The Geolocation service failed') }}." :
                "Error: {{ translate("Your browser doesn't support geolocation") }}."
            );
            infoWindow.open(map);
        }

        $("#order_place").on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        })

        $("#insertPayableAmount").on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        })
    </script>

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ translate($error) }}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        </script>
    @endif


    <script>
        $('#payment_card').on('change', function() {
            $("#paid_section").hide();
        });
        $('#payment_cash').on('change', function() {
            $("#paid_section").show();
        });

        $(document).on('ready', function() {
            @if ($order)
                $('#print-invoice').modal('show');
            @endif
        });

        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }

        function set_category_filter(url, id) {
            var nurl = new URL(url);
            nurl.searchParams.set('category_id', id);
            location.href = nurl;
        }


        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            var keyword = $('#datatableSearch').val();
            var nurl = new URL('{!! url()->full() !!}');
            nurl.searchParams.set('keyword', keyword);
            location.href = nurl;
        });

        function addon_quantity_input_toggle(e) {
            var cb = $(e.target);
            if (cb.is(":checked")) {
                cb.siblings('.addon-quantity-input').css({
                    'visibility': 'visible'
                });
            } else {
                cb.siblings('.addon-quantity-input').css({
                    'visibility': 'hidden'
                });
            }
        }

        function quickView(product_id) {
            $.get({
                url: '{{ route('vendor.pos.quick-view') }}',
                dataType: 'json',
                data: {
                    product_id: product_id
                },
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    console.log("success...")
                    $('#quick-view').modal('show');
                    $('#quick-view-modal').empty().html(data.view);
                },
                complete: function() {
                    $('#loading').hide();
                },
            });
        }

        function quickViewCartItem(product_id, item_key) {
            $.get({
                url: '{{ route('vendor.pos.quick-view-cart-item') }}',
                dataType: 'json',
                data: {
                    product_id: product_id,
                    item_key: item_key
                },
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    console.log("success...")
                    $('#quick-view').modal('show');
                    $('#quick-view-modal').empty().html(data.view);
                },
                complete: function() {
                    $('#loading').hide();
                },
            });
        }

        function checkAddToCartValidity() {
            var names = {};
            $('#add-to-cart-form input:radio').each(function() { // find unique names
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() { // then count them
                count++;
            });
            console.log(count);
            if ($('input:radio:checked').length == count) {
                return true;
            }
            return true;
        }

        function cartQuantityInitialize() {
            $('.btn-number').click(function(e) {
                e.preventDefault();

                var fieldName = $(this).attr('data-field');
                var type = $(this).attr('data-type');
                var input = $("input[name='" + fieldName + "']");
                var currentVal = parseInt(input.val());

                if (!isNaN(currentVal)) {
                    if (type == 'minus') {

                        if (currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('min')) {
                            $(this).attr('disabled', true);
                        }

                    } else if (type == 'plus') {

                        if (currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('max')) {
                            $(this).attr('disabled', true);
                        }

                    }
                } else {
                    input.val(0);
                }
            });

            $('.input-number').focusin(function() {
                $(this).data('oldValue', $(this).val());
            });

            $('.input-number').change(function() {

                minValue = parseInt($(this).attr('min'));
                maxValue = parseInt($(this).attr('max'));
                valueCurrent = parseInt($(this).val());

                var name = $(this).attr('name');
                if (valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cart',
                        text: '{{ translate('Sorry, the minimum value was reached') }}'
                    });
                    $(this).val($(this).data('oldValue'));
                }
                if (valueCurrent <= maxValue) {
                    $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cart',
                        text: '{{ translate('Sorry, stock limit exceeded.') }}'
                    });
                    $(this).val($(this).data('oldValue'));
                }
            });
            $(".input-number").keydown(function(e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                    // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        function getVariantPrice() {
            if ($('#add-to-cart-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: '{{ route('vendor.pos.variant_price') }}',
                    data: $('#add-to-cart-form').serializeArray(),
                    success: function(data) {
                        $('#add-to-cart-form #chosen_price_div').removeClass('d-none');
                        $('#add-to-cart-form #chosen_price_div #chosen_price').html(data.price);
                    }
                });
            }
        }

        function addToCart(form_id = 'add-to-cart-form') {
            if (checkAddToCartValidity()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{ route('vendor.pos.add-to-cart') }}',
                    data: $('#' + form_id).serializeArray(),
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(data) {
                        console.log(data.data);
                        if (data.data == 1) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Cart',
                                text: "{{ translate('messages.product_already_added_in_cart') }}"
                            });
                            return false;
                        } else if (data.data == 2) {
                            updateCart();
                            Swal.fire({
                                icon: 'info',
                                title: 'Cart',
                                text: "{{ translate('messages.product_has_been_updated_in_cart') }}"
                            });

                            return false;
                        } else if (data.data == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cart',
                                text: '{{ translate('messages.Sorry, product out of stock') }}'
                            });
                            return false;
                        } else if (data.data == 'variation_error') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cart',
                                text: data.message
                            });
                            return false;
                        }
                        $('.call-when-done').click();

                        toastr.success('{{ translate('messages.product_has_been_added_in_cart') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });

                        updateCart();
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
            } else {
                Swal.fire({
                    type: 'info',
                    title: 'Cart',
                    text: '{{ translate('Please choose all the options') }}'
                });
            }
        }

        function removeFromCart(key) {
            $.post('{{ route('vendor.pos.remove-from-cart') }}', {
                _token: '{{ csrf_token() }}',
                key: key
            }, function(data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    updateCart();
                    toastr.info('{{ translate('messages.item_has_been_removed_from_cart') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }

            });
        }

        function emptyCart() {
            $.post('{{ route('vendor.pos.emptyCart') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#del-add').empty();
                updateCart();
                toastr.info('{{ translate('messages.item_has_been_removed_from_cart') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            });
        }

        function updateCart() {
            $.post('<?php echo e(route('vendor.pos.cart_items')); ?>', {
                _token: '<?php echo e(csrf_token()); ?>'
            }, function(data) {
                $('#cart').empty().html(data);
            });
        }

        function deliveryAdressStore(form_id = 'delivery_address_store') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('vendor.pos.add-delivery-info') }}',
                data: $('#' + form_id).serializeArray(),
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        $('#del-add').empty().html(data.view);
                    }
                    updateCart();
                    $('.call-when-done').click();
                },
                complete: function() {
                    $('#loading').hide();
                    $('#paymentModal').modal('hide');
                }
            });
        }

        function payableAmount(form_id = 'payable_store_amount') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('vendor.pos.paid') }}',
                data: $('#' + form_id).serializeArray(),
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    // if (data.data) {
                    //     $('#del-add').empty().html(data.view);
                    // }
                    updateCart();
                    $('.call-when-done').click();
                },
                complete: function() {
                    $('#loading').hide();
                    $('#insertPayableAmount').modal('hide');
                }
            });
        }


        $(function() {
            $(document).on('click', 'input[type=number]', function() {
                this.select();
            });
        });


        function updateQuantity(e) {
            var element = $(e.target);
            var minValue = parseInt(element.attr('min'));
            // maxValue = parseInt(element.attr('max'));
            var valueCurrent = parseInt(element.val());

            var key = element.data('key');
            if (valueCurrent >= minValue) {
                $.post('{{ route('vendor.pos.updateQuantity') }}', {
                    _token: '{{ csrf_token() }}',
                    key: key,
                    quantity: valueCurrent
                }, function(data) {
                    updateCart();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Cart',
                    text: '{{ translate('Sorry, the minimum value was reached') }}'
                });
                element.val(element.data('oldValue'));
            }
            // if (valueCurrent <= maxValue) {
            //     $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
            // } else {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Cart',
            //         text: 'Sorry, stock limit exceeded.'
            //     });
            //     $(this).val($(this).data('oldValue'));
            // }


            // Allow: backspace, delete, tab, escape, enter and .
            if (e.type == 'keydown') {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                    // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            }

        };

        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function() {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{ route('vendor.pos.customers') }}',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });


        $('#delivery_address').on('click', function() {
            console.log('delivery_address clicked');
            initMap();
        });
    </script>
    <!-- IE Support -->
@endpush

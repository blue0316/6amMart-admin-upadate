@extends('layouts.admin.app')

@section('title', translate('Store Report'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header report-page-header">
            <div class="d-flex">
                <img src="{{ asset('public/assets/admin/img/store-report.svg') }}" class="page-header-icon" alt="">
                <div class="w-0 flex-grow-1 pl-3">
                    <h1 class="page-header-title m-0">
                        {{ translate('Store Wise Report') }}
                    </h1>
                    <span>
                        Monitor store’s <strong class="font-bold text--title">business</strong> analytics & Reports
                    </span>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Page Header Menu -->
        <ul class="nav nav-tabs page-header-tabs mb-2">
            <li class="nav-item">
                <a href="{{ route('admin.transactions.report.store-summary-report') }}"
                    class="nav-link">{{ translate('Summary Report') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.transactions.report.store-sales-report') }}"
                    class="nav-link">{{ translate('Sales Report') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.transactions.report.store-order-report') }}"
                    class="nav-link active">{{ translate('Order Report') }}</a>
            </li>
            {{-- <li class="nav-item">
            <a href="" class="nav-link">{{translate('Transactions Report')}}</a>
        </li> --}}
        </ul>

        <div class="card filter--card">
            <div class="card-body p-xl-5">
                <h5 class="form-label m-0 mb-3">
                    {{ translate('Filter Data') }}
                </h5>
                <form action="{{ route('admin.transactions.report.set-date') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <select name="zone_id" class="form-control js-select2-custom"
                                onchange="set_zone_filter('{{ url()->full() }}',this.value)" id="zone">
                                <option value="all">{{ translate('messages.All Zones') }}</option>
                                @foreach (\App\Models\Zone::orderBy('name')->get() as $z)
                                    <option value="{{ $z['id'] }}"
                                        {{ isset($zone) && $zone->id == $z['id'] ? 'selected' : '' }}>
                                        {{ $z['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <select name="store_id" onchange="set_store_filter('{{ url()->full() }}',this.value)"
                                data-placeholder="{{ translate('messages.select') }} {{ translate('messages.store') }}"
                                class="js-data-example-ajax form-control">
                                @if (isset($store))
                                    <option value="{{ $store->id }}" selected>{{ $store->name }}</option>
                                @else
                                    <option value="all" selected>{{ translate('messages.all') }}
                                        {{ translate('messages.stores') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <select class="form-control" name="filter"
                                onchange="set_time_filter('{{ url()->full() }}',this.value)">
                                <option value="all_time" {{ isset($filter) && $filter == 'all_time' ? 'selected' : '' }}>
                                    {{ translate('messages.All Time') }}</option>
                                <option value="this_year" {{ isset($filter) && $filter == 'this_year' ? 'selected' : '' }}>
                                    {{ translate('messages.This Year') }}</option>
                                <option value="previous_year"
                                    {{ isset($filter) && $filter == 'previous_year' ? 'selected' : '' }}>{{ translate('messages.Previous Year') }}
                                </option>
                                <option value="this_month"
                                    {{ isset($filter) && $filter == 'this_month' ? 'selected' : '' }}>{{ translate('messages.This Month') }}</option>
                                <option value="this_week" {{ isset($filter) && $filter == 'this_week' ? 'selected' : '' }}>
                                    {{ translate('messages.This Week') }}</option>
                                <option value="custom" {{ isset($filter) && $filter == 'custom' ? 'selected' : '' }}>
                                    {{ translate('Custom') }}</option>
                            </select>
                        </div>
                        @if (isset($filter) && $filter == 'custom')
                        <div class="col-md-4 col-sm-6">
                            <input type="date" name="from" id="from_date"
                                {{ session()->has('from_date') ? 'value=' . session('from_date') : '' }}
                                class="form-control" required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <input type="date" name="to" id="to_date"
                                {{ session()->has('to_date') ? 'value=' . session('to_date') : '' }} class="form-control"
                                required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <button type="submit" class="btn btn--primary btn-block">{{ translate('show_data') }}</button>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>


        <div class="store-report-content mt-11px">
            <div class="left-content">
                <div class="left-content-card">
                    <img src="{{ asset('/public/assets/admin/img/report/cart.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">{{ $orders->total() }}</h4>
                        <h6 class="subtext">{{ translate('messages.Total Order') }}</h6>
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ asset('/public/assets/admin/img/report/total-order.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">{{ \App\CentralLogics\Helpers::number_format_short($total_order_amount) }}
                        </h4>
                        <h6 class="subtext">{{ translate('messages.total_order_amount') }}</h6>
                    </div>
                    <div class="coupon__discount w-100 text-right d-flex justify-content-between">
                        <div>
                            <strong class="text-danger">{{ \App\CentralLogics\Helpers::number_format_short($total_canceled) }}</strong>
                            <div>{{ translate('messages.canceled') }}</div>
                        </div>
                        <div>
                            <strong>{{ \App\CentralLogics\Helpers::number_format_short($total_ongoing) }}</strong>
                            <div>
                                {{ translate('Incomplete') }}
                            </div>
                        </div>
                        <div>
                            <strong class="text-success">{{ \App\CentralLogics\Helpers::number_format_short($total_delivered) }}</strong>
                            <div>
                                {{ translate('Completed') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ asset('/public/assets/admin/img/report/total-discount.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">
                            {{ \App\CentralLogics\Helpers::number_format_short($total_coupon_discount + $total_product_discount) }}
                        </h4>
                        <h6 class="subtext">{{ translate('Total Discount Given') }}</h6>
                    </div>
                    <div class="coupon__discount w-100 text-right d-flex justify-content-between">
                        <div>
                            <strong>{{ \App\CentralLogics\Helpers::number_format_short($total_coupon_discount) }}</strong>
                            <div>{{ translate('messages.coupon_discount') }}</div>
                        </div>
                        <div>
                            <strong>{{ \App\CentralLogics\Helpers::number_format_short($total_product_discount) }}</strong>
                            <div>
                                {{ translate('Product Discount') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="center-chart-area">
                <div class="center-chart-header">
                    <h4 class="title">{{ translate('Total Orders') }}</h4>
                    <h5 class="subtitle">{{ translate('Average Order Value :') }}
                        {{ $orders->count() > 0 ? \App\CentralLogics\Helpers::number_format_short($total_order_amount / $orders->total()) : 0 }}
                        <span class="input-label-secondary text--title" data-toggle="tooltip"
                    data-placement="right"
                    data-original-title="{{ translate('Average Value of all type of orders.') }}">
                    <i class="tio-info-outined"></i>
                </span>
                    </h5>
                </div>
                <canvas id="updatingData" class="store-center-chart"
                    data-hs-chartjs-options='{
                "type": "bar",
                "data": {
                  "labels": [{{ implode(',', $label) }}],
                  "datasets": [{
                    "data": [{{ implode(',', $data) }}],
                    "backgroundColor": "#82CFCF",
                    "hoverBackgroundColor": "#82CFCF",
                    "borderColor": "#82CFCF"
                  }]
                },
                "options": {
                  "scales": {
                    "yAxes": [{
                      "gridLines": {
                        "color": "#e7eaf3",
                        "drawBorder": false,
                        "zeroLineColor": "#e7eaf3"
                      },
                      "ticks": {
                        "beginAtZero": true,
                        "stepSize": {{ceil((array_sum($data)/10000))*2000}},
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5,
                        "postfix": " {{ \App\CentralLogics\Helpers::currency_symbol() }}"
                      }
                    }],
                    "xAxes": [{
                      "gridLines": {
                        "display": false,
                        "drawBorder": false
                      },
                      "ticks": {
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5
                      },
                      "categoryPercentage": 0.3,
                      "maxBarThickness": "10"
                    }]
                  },
                  "cornerRadius": 5,
                  "tooltips": {
                    "prefix": " ",
                    "hasIndicator": true,
                    "mode": "index",
                    "intersect": false
                  },
                  "hover": {
                    "mode": "nearest",
                    "intersect": true
                  }
                }
              }'>
                </canvas>
            </div>
            <div class="right-content">
                <!-- Dognut Pie -->
                <div class="card h-100 bg-white payment-statistics-shadow">
                    <div class="card-header border-0 ">
                        <h5 class="card-title">
                            <span>{{ translate('order statistics') }}</span>
                        </h5>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="position-relative pie-chart">
                            <div id="dognut-pie"></div>
                            <!-- Total Orders -->
                            <div class="total--orders">
                                <h3>{{ $orders->total() }}
                                </h3>
                                <span>{{ translate('messages.orders') }}</span>
                            </div>
                            <!-- Total Orders -->
                        </div>
                        <div class="apex-legends">
                            <div class="before-bg-107980">
                                <span>Total canceled
                                    ({{ $total_canceled_count }})</span>
                            </div>
                            <div class="before-bg-56B98F">
                                <span>Total ongoing (
                                    {{ $total_ongoing_count }})</span>
                            </div>
                            <div class="before-bg-E5F5F1">
                                <span>Total delivered
                                    ({{ $total_delivered_count }})</span>
                            </div>
                        </div>
                        <div class="earning-statistics-content mt-3">
                            <a href="{{ route('admin.order.list', ['all']) }}" class="trx-btn">{{ translate('View All Orders') }}</a>
                        </div>
                    </div>
                </div>
                <!-- Dognut Pie -->
            </div>
        </div>

        <div class="mt-11px card">
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{ translate('Total Sales') }}</h5>
                    <form action="javascript:" id="search-form" class="search-form">
                        <!-- Search -->
                        @csrf
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{ translate('Search by ID..') }}"
                                aria-label="{{ translate('messages.search') }}" required>
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>

                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                            href="javascript:;"
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
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('admin.transactions.report.store-order-report-export', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('admin.transactions.report.store-order-report-export', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless middle-align __txt-14px">
                        <thead class="thead-light white--space-false">
                            <tr>
                                <th class="border-top border-bottom text-capitalize">{{ translate('SL') }}</th>
                                <th class="border-top border-bottom text-capitalize">{{ translate('Order ID') }}</th>
                                <th class="border-top border-bottom text-capitalize">{{ translate('Order Date') }}</th>
                                <th class="border-top border-bottom text-capitalize">{{ translate('Customer Info') }}</th>
                                <th class="border-top border-bottom text-capitalize">{{ translate('Total Amount') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">
                                    {{ translate('Discount') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">{{ translate('Tax') }}
                                </th>
                                <th class="border-top border-bottom text-capitalize text-center">
                                    {{ translate('Delivery Charge') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">{{ translate('Action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">
                            @foreach ($orders as $key => $order)
                                <tr class="status-{{ $order['order_status'] }} class-all">
                                    <td class="">
                                        {{ $key + $orders->firstItem() }}
                                    </td>
                                    <td class="table-column-pl-0">
                                        <a
                                            href="{{ route('admin.order.details', ['id' => $order['id'],'module_id'=>$order['module_id']]) }}">{{ $order['id'] }}</a>
                                    </td>
                                    <td>
                                        <div>
                                            <div>
                                                {{ date('d M Y', strtotime($order['created_at'])) }}
                                            </div>
                                            <div class="d-block text-uppercase">
                                                {{ date(config('timeformat'), strtotime($order['created_at'])) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($order->customer)
                                            <a class="text-body text-capitalize"
                                                href="{{ route('admin.transactions.customer.view', [$order['user_id']]) }}">
                                                <strong>{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</strong>
                                                <div>{{ $order->customer['phone'] }}</div>
                                            </a>
                                        @else
                                            <label class="badge badge-danger">{{ translate('messages.invalid') }}
                                                {{ translate('messages.customer') }}
                                                {{ translate('messages.data') }}</label>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-right mw--85px">
                                            <div>
                                                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']) }}
                                            </div>
                                            @if ($order->payment_status == 'paid')
                                                <strong class="text-success">
                                                    {{ translate('messages.paid') }}
                                                </strong>
                                            @else
                                                <strong class="text-danger">
                                                    {{ translate('messages.unpaid') }}
                                                </strong>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount'] + $order['store_discount_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['total_tax_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['original_delivery_charge']) }}
                                    </td>

                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn"
                                                href="{{ route('admin.order.details', ['id' => $order['id'],'module_id'=>$order['module_id']]) }}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                            <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{ route('admin.transactions.order.generate-invoice', ['id' => $order['id']]) }}">
                                                <i class="tio-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->


                @if (count($orders) !== 0)
                    <hr>
                    <div class="page-area">
                        {!! $orders->withQueryString()->links() !!}
                    </div>
                @endif
                @if (count($orders) === 0)
                    <div class="empty--data">
                        <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                        <h5>
                            {{ translate('no_data_found') }}
                        </h5>
                    </div>
                @endif
            </div>
        </div>


    </div>

@endsection


@push('script')
    <script src="{{ asset('public/assets/admin') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script
        src="{{ asset('public/assets/admin') }}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js">
    </script>


    <!-- Apex Charts -->
    <script src="{{ asset('/public/assets/admin/js/apex-charts/apexcharts.js') }}"></script>
    <!-- Apex Charts -->
@endpush


@push('script_2')
    <!-- Dognut Pie Chart -->
    <script>
        var options = {
            series: [{{ $total_canceled_count}}, {{ $total_ongoing_count}}, {{ $total_delivered_count }}],
            chart: {
                width: 320,
                type: 'donut',
            },
            labels: ['Total canceled ({{ $total_canceled_count}})',
                'Total ongoing ({{ $total_ongoing_count}})',
                'Total delivered  ({{ $total_delivered_count }})'
            ],
            dataLabels: {
                enabled: false,
                style: {
                    colors: ['#ffffff', '#ffffff', '#107980']
                }
            },
            responsive: [{
                breakpoint: 1650,
                options: {
                    chart: {
                        width: 260
                    },
                }
            }],
            colors: ['#107980', '#56B98F', '#111'],
            fill: {
                colors: ['#107980', '#56B98F', '#E5F5F1']
            },
            legend: {
                show: false
            },
        };

        var chart = new ApexCharts(document.querySelector("#dognut-pie"), options);
        chart.render();
    </script>
    <!-- Dognut Pie Chart -->



    <script>
        // Bar Charts
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function() {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        // all:true,
                        @if (isset($zone))
                            zone_ids: [{{ $zone->id }}],
                        @endif
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

        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.transactions.report.store-order-report-search') }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#set-rows').html(data.view);
                    // $('#countItems').html(data.count);
                    $('.page-area').hide();
                },
                complete: function() {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush

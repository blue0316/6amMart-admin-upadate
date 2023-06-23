@extends('layouts.admin.app')

@section('title', translate('Store Report'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">
        @php
            $from = session('from_date');
            $to = session('to_date');
        @endphp

        <!-- Page Header -->
        <div class="page-header report-page-header">
            <div class="d-flex">
                <img src="{{ asset('public/assets/admin/img/store-report.svg') }}" class="page-header-icon" alt="">
                <div class="w-0 flex-grow-1 pl-3">
                    <h1 class="page-header-title m-0">
                        {{ translate('Store Report') }}
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
                    class="nav-link active">{{ translate('Sales Report') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.transactions.report.store-order-report') }}"
                    class="nav-link">{{ translate('Order Report') }}</a>
            </li>
            {{-- <li class="nav-item">
                <a href="" class="nav-link">{{ translate('Transactions Report') }}</a>
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
                            <select class="form-control" name="filter" onchange="set_time_filter('{{ url()->full() }}',this.value)">
                                <option value="all_time" {{ isset($filter) && $filter == "all_time" ? 'selected' : '' }}>{{ translate('messages.All Time') }}</option>
                                <option value="this_year" {{ isset($filter) && $filter == "this_year" ? 'selected' : '' }}>{{ translate('messages.This Year') }}</option>
                                <option value="previous_year" {{ isset($filter) && $filter == "previous_year" ? 'selected' : '' }}>{{ translate('messages.Previous Year') }}</option>
                                <option value="this_month" {{ isset($filter) && $filter == "this_month" ? 'selected' : '' }}>{{ translate('messages.This Month') }}</option>
                                <option value="this_week" {{ isset($filter) && $filter == "this_week" ? 'selected' : '' }}>{{ translate('messages.This Week') }}</option>
                                <option value="custom" {{ isset($filter) && $filter == 'custom' ? 'selected' : '' }}>
                                    {{ translate('messages.Custom') }}</option>
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
                    <img src="{{ asset('/public/assets/admin/img/report/gross.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">
                            {{ \App\CentralLogics\Helpers::number_format_short($orders->sum('order_amount')) }}</h4>
                        <h6 class="subtext">{{ translate('Gross Sale') }}</h6>
                        {{-- <div class="info-txt">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.5 3V3.75H9.96975L6.75 6.96975L5.14012 5.35988C5.0698 5.28957 4.97444 5.25008 4.875 5.25008C4.77556 5.25008 4.6802 5.28957 4.60988 5.35988L0.75 9.21975L1.28025 9.75L4.875 6.15525L6.48487 7.76513C6.5552 7.83543 6.65056 7.87492 6.75 7.87492C6.84944 7.87492 6.9448 7.83543 7.01513 7.76513L10.5 4.28025V6.75H11.25V3H7.5Z"
                                    fill="#00AA6D" />
                            </svg> 10% more from last month
                        </div> --}}
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ asset('/public/assets/admin/img/report/tax.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">
                            {{ \App\CentralLogics\Helpers::number_format_short($orders->sum('total_tax_amount')) }}</h4>
                        <h6 class="subtext">{{ translate('Total Tax') }}</h6>
                        {{-- <div class="info-txt text-danger">Store Wise Tax</div> --}}
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ asset('/public/assets/admin/img/report/commission.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">
                            {{ \App\CentralLogics\Helpers::number_format_short($orders->sum('transaction_sum_admin_commission')+$orders->sum('transaction_sum_delivery_fee_comission')-$orders->sum('transaction_sum_admin_expense')) }}
                        </h4>
                        <h6 class="subtext">{{ translate('Total Commission') }}</h6>
                        {{-- <div class="info-txt d-flex flex-wrap align-items-center flex-wrap">
                            <span class="badge badge-primary px-2">
                                $3,453 Due
                            </span>
                            <span class="text--primary">
                                $30,000 Collected
                            </span>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="center-chart-area">
                <div class="center-chart-header">
                    <h4 class="title">{{ translate('Total Orders') }}</h4>
                    <h5 class="subtitle">{{ translate('Average Order Value :') }}
                        {{ $orders->count() > 0 ? \App\CentralLogics\Helpers::number_format_short($orders->sum('order_amount') / $orders->count()) : 0 }}
                        <span class="input-label-secondary text--title" data-toggle="tooltip"
                    data-placement="right"
                    data-original-title="{{ translate('Average Value of completed orders.') }}">
                    <i class="tio-info-outined"></i>
                </span>
                    </h5>
                </div>
                <canvas id="updatingData" class="store-center-chart"
                    data-hs-chartjs-options='{
                    "type": "bar",
                    "data": {
                      "labels": [{{ implode(",",$label) }}],
                      "datasets": [{
                        "data": [{{ implode(",",$data) }}],
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
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="earning-statistics-content">
                            <h6 class="subtitle">{{ translate('Total Store Earnings') }}</h6>
                            <h3 class="title">
                                {{ \App\CentralLogics\Helpers::number_format_short($orders->sum('transaction_sum_store_amount')) }}
                            </h3>
                            {{-- <div class="subtxt">Incomplete Settlement amount <strong
                                    class="text--title font-medium">$12,834</strong> </div>
                            <a href="" class="trx-btn">View All Transaction</a> --}}
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
                                placeholder="{{ translate('Search by product..') }}"
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
                                href="{{ route('admin.transactions.report.store-sales-report-export', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('admin.transactions.report.store-sales-report-export', ['type' => 'csv', request()->getQueryString()]) }}">
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
                    <table class="table table-borderless">
                        <thead class="thead-light white--space-false">
                            <tr>
                                <th class="border-top border-bottom text-capitalize">{{ translate('SL') }}</th>
                                <th class="border-top border-bottom text-capitalize">{{ translate('Product') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">{{ translate('QTY Sold') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">
                                    {{ translate('Gross Sale') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">
                                    {{ translate('Discount Given') }}</th>
                                <th class="border-top border-bottom text-capitalize text-center">{{ translate('Action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">

                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + $items->firstItem() }}</td>
                                    <td>
                                        <a class="media align-items-center"
                                            href="{{ route('admin.item.view', [$item['id'], 'module_id'=>$item['module_id']]) }}">
                                            <div class="media-body">
                                                <h5 class="text-hover-primary mb-0">{{ $item['name'] }}</h5>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{ $item->orders->sum('quantity') }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->orders->sum('price') }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->orders->sum('discount_on_item') }}
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a href="{{ route('admin.item.view', [$item['id'], 'module_id'=>$item['module_id']]) }}"
                                                class="action-btn btn--primary btn-outline-primary">
                                                <i class="tio-invisible"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (count($items) !== 0)
                        <hr>
                        <div class="page-area">
                            {!! $items->withQueryString()->links() !!}
                        </div>
                    @endif
                    @if (count($items) === 0)
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
    @endpush


    @push('script_2')
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


            $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.report.store-sales-report-search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    // $('#countItems').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
        </script>
    @endpush

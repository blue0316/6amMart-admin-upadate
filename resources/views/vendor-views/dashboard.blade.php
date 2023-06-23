@extends('layouts.vendor.app')

@section('title',translate('messages.dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        @if(auth('vendor')->check())
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm">
                    <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{asset('public/assets/admin/img/category.png')}}" class="w--20" alt="">
                    </span>
                        <span>{{translate('messages.dashboard')}}</span>
                    </h1>
                </div>
                <div class="col-sm text-sm-right">
                    <span class="d-inline-flex align-items-center">
                        <span class="mr-2">{{translate('messages.followup')}}</span>
                        <i class="tio-restaurant fz-30px"></i>
                    </span>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="card mb-3">
            <div class="card-body">
                <div class="row gx-2 gx-lg-3 mb-2">
                    <div class="col-md-9">
                        <h4><i class="tio-chart-bar-4 fz-30px"></i>{{translate('messages.dashboard_order_statistics')}}</h4>
                    </div>
                    <div class="col-md-3">
                        <select class="custom-select" name="statistics_type" onchange="order_stats_update(this.value)">
                            <option
                                value="overall" {{$params['statistics_type'] == 'overall'?'selected':''}}>
                                {{translate('messages.Overall Statistics')}}
                            </option>
                            <option
                                value="today" {{$params['statistics_type'] == 'today'?'selected':''}}>
                                {{translate("messages.Today's Statistics")}}
                            </option>
                            <option
                                value="this_month" {{$params['statistics_type'] == 'this_month'?'selected':''}}>
                                {{translate("messages.This Month's Statistics")}}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="py-2"></div>
                <div class="row g-2" id="order_stats">
                    @include('vendor-views.partials._dashboard-order-stats',['data'=>$data])
                </div>
            </div>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-lg-12 mb-3 mb-lg-12">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Body -->
                    <div class="card-body">
                        <div class="row mb-2 align-items-center">
                            <div class="col-sm mb-2 mb-sm-0">
                                <div class="d-flex flex-wrap justify-content-center align-items-center">
                                    @php($amount=array_sum($earning))
                                    <span class="h5 m-0 mr-3 fz--11 d-flex align-items-center mb-2 mb-md-0">
                                        <span class="legend-indicator chart-bg-2"></span>
                                        {{translate('messages.total_earning')}} : <span>{{\App\CentralLogics\Helpers::format_currency(array_sum($earning))}}</span>
                                    </span>
                                    <span class="h5 m-0 fz--11 d-flex align-items-center mb-2 mb-md-0">
                                        <span class="legend-indicator chart-bg-3"></span>
                                        {{translate('messages.commission_given')}} : <span>{{\App\CentralLogics\Helpers::format_currency(array_sum($commission))}}</span>
                                    </span>
                                </div>

                            </div>

                            <div class="col-sm-auto align-self-sm-end">
                                <!-- Legend Indicators -->
                                <h5 class="text-center">
                                    {{translate('messages.yearly_statistics')}}
                                    <i class="tio-chart-bar-4 fz--40px"></i>
                                </h5>
                                <!-- End Legend Indicators -->
                            </div>
                        </div>
                        <!-- End Row -->

                        <!-- Bar Chart -->
                        <div class="chartjs-custom">
                            <canvas id="updatingData" class="h-20rem"
                                    data-hs-chartjs-options='{
                            "type": "bar",
                            "data": {
                              "labels": ["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                              "datasets": [{
                                "data": [{{$earning[1]}},{{$earning[2]}},{{$earning[3]}},{{$earning[4]}},{{$earning[5]}},{{$earning[6]}},{{$earning[7]}},{{$earning[8]}},{{$earning[9]}},{{$earning[10]}},{{$earning[11]}},{{$earning[12]}}],
                                "backgroundColor": "#00AA96",
                                "hoverBackgroundColor": "#00AA96",
                                "borderColor": "#00AA96"
                              },
                              {
                                "data": [{{$commission[1]}},{{$commission[2]}},{{$commission[3]}},{{$commission[4]}},{{$commission[5]}},{{$commission[6]}},{{$commission[7]}},{{$commission[8]}},{{$commission[9]}},{{$commission[10]}},{{$commission[11]}},{{$commission[12]}}],
                                "backgroundColor": "#b9e0e0",
                                "borderColor": "#b9e0e0"
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
                                    "stepSize": {{$amount>1?20000:1}},
                                    "fontSize": 12,
                                    "fontColor": "#97a4af",
                                    "fontFamily": "Open Sans, sans-serif",
                                    "padding": 10,
                                    "postfix": " {{\App\CentralLogics\Helpers::currency_symbol()}}"
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
                                  "categoryPercentage": 0.5,
                                  "maxBarThickness": "10"
                                }]
                              },
                              "cornerRadius": 2,
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
                          }'></canvas>
                        </div>
                        <!-- End Bar Chart -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-6 mt-3">
                <!-- Card -->
                <div class="card h-100" id="top-selling-items-view">
                    @include('vendor-views.partials._top-selling-items',['top_sell'=>$data['top_sell']])
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-6 mt-3">
                <!-- Card -->
                <div class="card h-100" id="top-rated-items-view">
                    @include('vendor-views.partials._most-rated-items',['most_rated_items'=>$data['most_rated_items']])
                </div>
                <!-- End Card -->
            </div>


        </div>
        <!-- End Row -->
        @else
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('messages.welcome')}}, {{auth('vendor_employee')->user()->f_name}}.</h1>
                    <p class="page-header-text">{{translate('messages.employee_welcome_message')}}</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        @endif
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script
        src="{{asset('public/assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush


@push('script_2')
    <script>
        // INITIALIZATION OF CHARTJS
        // =======================================================
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function () {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
    </script>

    <script>
        function order_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.dashboard.order-stats')}}',
                data: {
                    statistics_type: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    insert_param('statistics_type',type);
                    $('#order_stats').html(data.view)
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }
    </script>

    <script>
        function insert_param(key, value) {
            key = encodeURIComponent(key);
            value = encodeURIComponent(value);
            // kvp looks like ['key1=value1', 'key2=value2', ...]
            var kvp = document.location.search.substr(1).split('&');
            let i = 0;

            for (; i < kvp.length; i++) {
                if (kvp[i].startsWith(key + '=')) {
                    let pair = kvp[i].split('=');
                    pair[1] = value;
                    kvp[i] = pair.join('=');
                    break;
                }
            }
            if (i >= kvp.length) {
                kvp[kvp.length] = [key, value].join('=');
            }
            // can return this or...
            let params = kvp.join('&');
            // change url page with new params
            window.history.pushState('page2', 'Title', '{{url()->current()}}?' + params);
        }
    </script>
@endpush

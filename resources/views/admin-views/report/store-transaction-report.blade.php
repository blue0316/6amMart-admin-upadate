@extends('layouts.admin.app')

@section('title',translate('Store Report'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header report-page-header">
        <div class="d-flex">
            <img src="{{asset('public/assets/admin/img/store-report.svg')}}" class="page-header-icon" alt="">
            <div class="w-0 flex-grow-1 pl-3">
                <h1 class="page-header-title m-0">
                    {{translate('Store Wise Report')}}
                </h1>
                <span>
                    Monitor store’s  <strong class="font-bold text--title">business</strong> analytics & Reports
                </span>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Page Header Menu -->
    <ul class="nav nav-tabs page-header-tabs mb-2">
        <li class="nav-item">
            <a href="" class="nav-link">{{translate('Summary Report')}}</a>
        </li>
        <li class="nav-item">
            <a href="" class="nav-link">{{translate('Sales Report')}}</a>
        </li>
        <li class="nav-item">
            <a href="" class="nav-link">{{translate('Order Report')}}</a>
        </li>
        <li class="nav-item">
            <a href="" class="nav-link active">{{translate('Transactions Report')}}</a>
        </li>
    </ul>

    <div class="card filter--card">
        <div class="card-body p-xl-5">
            <h5 class="form-label m-0 mb-3">
                {{translate('Filter Data')}}
            </h5>
            <form method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <select class="form-control js-select2-custom">
                            <option value="">All Zone</option>
                            <option value="">Farmgate</option>
                            <option value="">Mirpur</option>
                            <option value="">Uttara</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <select class="form-control js-select2-custom">
                            <option value="">All Zone</option>
                            <option value="">Farmgate</option>
                            <option value="">Mirpur</option>
                            <option value="">Uttara</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <select class="js-data-example-ajax form-control">
                            <option value="">All Zone</option>
                            <option value="">Farmgate</option>
                            <option value="">Mirpur</option>
                            <option value="">Uttara</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <input type="date" name="from" id="from_date" class="form-control" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <input type="date" name="to" id="to_date" class="form-control" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <button type="submit" class="btn btn--primary btn-block">{{translate('show_data')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="store-report-content mt-11px">
        <div class="left-content">
            <div class="left-content-card">
                <img src="{{asset('/public/assets/admin/img/report/trx-1.svg')}}" alt="">
                <div class="info">
                    <h4 class="subtitle __txt-22">594</h4>
                    <h6 class="subtext font-regular">Total Transactions</h6>
                    <div class="info-txt text-danger">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 9V8.25H9.96975L6.75 5.03025L5.14012 6.64012C5.0698 6.71043 4.97444 6.74992 4.875 6.74992C4.77556 6.74992 4.6802 6.71043 4.60988 6.64012L0.75 2.78025L1.28025 2.25L4.875 5.84475L6.48487 4.23487C6.5552 4.16457 6.65056 4.12508 6.75 4.12508C6.84944 4.12508 6.9448 4.16457 7.01513 4.23487L10.5 7.71975V5.25H11.25V9H7.5Z" fill="#FF6D6D"/>
                        </svg> 10% less from last month</div>
                </div>
            </div>
            <div class="left-content-card">
                <img src="{{asset('/public/assets/admin/img/report/trx-2.svg')}}" alt="">
                <div class="info">
                    <h4 class="subtitle __txt-22">$3,330</h4>
                    <h6 class="subtext font-regular">Total Transaction Amount</h6>
                </div>
            </div>
            <div class="left-content-card">
                <img src="{{asset('/public/assets/admin/img/report/trx-3.svg')}}" alt="">
                <div class="info">
                    <h4 class="subtitle __txt-22">$3,330</h4>
                    <h6 class="subtext font-regular">Commission Earned</h6>
                </div>
                <div class="coupon__discount w-100 text-right d-flex justify-content-between">
                    <div class="text-sm">
                        <strong>$30,000</strong>
                        <div>Coupon Discount</div>
                    </div>
                    <div class="text-sm">
                        <strong>$3,453</strong>
                        <div>
                            Product Discount
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="center-chart-area">
            <div class="center-chart-header">
                <h4 class="title">{{translate('Total Orders')}}</h4>
                <h5 class="subtitle">{{translate('Average Order Value :')}} $456.98</h5>
            </div>
          <canvas id="updatingData" class="store-center-chart"
              data-hs-chartjs-options='{
                "type": "bar",
                "data": {
                  "labels": ["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                  "datasets": [{
                    "data": [12, 17, 40, 50, 83, 34, 12, 17, 40, 50, 83, 34],
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
                        "stepSize": 20,
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5,
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
                        <span>{{translate('payment statistics')}}</span>
                    </h5>
                </div>
                <div class="card-body px-0 pt-0">
                    <div class="position-relative pie-chart">
                        <div id="dognut-pie"></div>
                        <!-- Total Orders -->
                        <div class="total--orders">
                            <h3>$ 1.6M+</h3>
                            <span>Orders</span>
                        </div>
                        <!-- Total Orders -->
                    </div>
                    <div class="apex-legends">
                        <div class="before-bg-107980">
                            <span>Cash Payments ($56M)</span>
                        </div>
                        <div class="before-bg-56B98F">
                            <span>Digital Payments ($ 4,783)</span>
                        </div>
                        <div class="before-bg-E5F5F1">
                            <span>Wallet ($75,439)</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dognut Pie -->
        </div>
    </div>

    <div class="mt-11px card">
        <div class="card-header border-0 py-2">
            <div class="search--button-wrapper">
                <h5 class="card-title">{{translate('Total Sales')}}</h5>
                <form action="javascript:" id="search-form" class="search-form">
                                <!-- Search -->
                    @csrf
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{translate('Search by ID, customer or payment status')}}" aria-label="{{translate('messages.search')}}" required>
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
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.store.export', ['type'=>'excel',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.store.export', ['type'=>'csv',request()->getQueryString()])}}">
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
                            <th class="border-top border-bottom text-capitalize pl-4">{{translate('SL')}}</th>
                            <th class="border-top border-bottom text-capitalize">{{translate('XID')}}</th>
                            <th class="border-top border-bottom text-capitalize">{{translate('Created At')}}</th>
                            <th class="border-top border-bottom text-capitalize">{{translate('Transaction Amount')}}</th>
                            <th class="border-top border-bottom text-capitalize pl-0">{{translate('Reference')}}</th>
                            <th class="border-top border-bottom text-capitalize pl-0">{{translate('Payment Method')}}</th>
                            <th class="border-top border-bottom text-capitalize text-center">{{translate('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="pl-4">1</td>
                            <td>
                                <a href="#0">100021</a>
                            </td>
                            <td>
                                <div>20-03-2022, </div>
                                <div>4:30 PM</div>
                            </td>
                            <td>
                                <div class="text-right w--120px">
                                    <div>
                                        $ 403.20
                                    </div>
                                    <strong class="badge font-medium __badge-sm badge-success text-white">complete
                                    </strong>
                                </div>
                            </td>
                            <td class="pl-0">
                                078 8502 2342 
                            </td>
                            <td class="pl-0">
                                Visa Card
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn" href="#0">
                                        <i class="tio-download-to"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">1</td>
                            <td>
                                <a href="#0">100021</a>
                            </td>
                            <td>
                                <div>20-03-2022, </div>
                                <div>4:30 PM</div>
                            </td>
                            <td>
                                <div class="text-right w--120px">
                                    <div>
                                        $ 403.20
                                    </div>
                                    <strong class="badge font-medium __badge-sm badge-danger text-white">denied
                                    </strong>
                                </div>
                            </td>
                            <td class="pl-0">
                                078 8502 2342 
                            </td>
                            <td class="pl-0">
                                Visa Card
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn" href="#0">
                                        <i class="tio-download-to"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">1</td>
                            <td>
                                <a href="#0">100021</a>
                            </td>
                            <td>
                                <div>20-03-2022, </div>
                                <div>4:30 PM</div>
                            </td>
                            <td>
                                <div class="text-right w--120px">
                                    <div>
                                        $ 403.20
                                    </div>
                                    <strong class="badge font-medium __badge-sm badge-success text-white">complete
                                    </strong>
                                </div>
                            </td>
                            <td class="pl-0">
                                078 8502 2342 
                            </td>
                            <td class="pl-0">
                                Visa Card
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn" href="#0">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn" href="#0">
                                        <i class="tio-download-to"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>

@endsection


@push('script')
    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{asset('public/assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>

    
    <!-- Apex Charts -->
    <script src="{{asset('/public/assets/admin/js/apex-charts/apexcharts.js')}}"></script>
    <!-- Apex Charts -->
@endpush


@push('script_2')

<!-- Dognut Pie Chart -->
<script>
     var options = {
          series: [300, 200, 150],
            chart: {
            width: 320,
            type: 'donut',
            },
        labels: ['Ongoing (34)', 'Delivered (876)', 'Pending (23)'],
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

    $('.js-chart').each(function () {
        $.HSCore.components.HSChartJS.init($(this));
    });

    var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
</script>


@endpush

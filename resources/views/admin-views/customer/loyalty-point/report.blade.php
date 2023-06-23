@extends('layouts.admin.app')

@section('title',__('messages.customer_loyalty_point').' '.__('messages.report'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/customer-loyalty.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{__('messages.customer_loyalty_point')}} {{__('messages.report')}}
                </span>
            </h1>
        </div>
        <!-- Page Header -->

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-filter-outlined"></i>
                    </span>
                    <span>{{__('messages.filter')}} {{__('messages.options')}}</span>
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('admin.users.customer.loyalty-point.report')}}" method="get">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <input type="date" name="from" id="from_date" value="{{request()->get('from')}}" class="form-control" title="{{__('messages.from')}} {{__('messages.date')}}">
                        </div>
                        <div class="col-sm-6">
                            <input type="date" name="to" id="to_date" value="{{request()->get('to')}}" class="form-control" title="{{ucfirst(__('messages.to'))}} {{__('messages.date')}}">
                        </div>
                        <div class="col-sm-6">
                            @php
                            $transaction_status=request()->get('transaction_type');
                            @endphp
                            <select name="transaction_type" id="" class="form-control" title="{{__('messages.select')}} {{__('messages.transaction_type')}}">
                                <option value="">{{__('messages.all')}}</option>
                                <option value="point_to_wallet" {{isset($transaction_status) && $transaction_status=='point_to_wallet'?'selected':''}}>{{__('messages.point_to_wallet')}}</option>
                                <option value="order_place" {{isset($transaction_status) && $transaction_status=='order_place'?'selected':''}}>{{__('messages.order_place')}}</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <select id='customer' name="customer_id" data-placeholder="{{__('messages.select_customer')}}" class="js-data-example-ajax form-control" title="{{__('messages.select_customer')}}">
                                @if (request()->get('customer_id') && $customer_info = \App\Models\User::find(request()->get('customer_id')))
                                    <option value="{{$customer_info->id}}" selected>{{$customer_info->f_name.' '.$customer_info->l_name}}({{$customer_info->phone}})</option>
                                @endif

                            </select>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{__('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary"><i class="tio-filter-list mr-1"></i>{{__('messages.filter')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-document-text-outlined"></i>
                    </span>
                    <span>{{__('messages.summary')}}</span>
                </h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $credit = $data[0]->total_credit??0;
                        $debit = $data[0]->total_debit??0;
                        $balance = $credit - $debit;
                    @endphp
                    <!--Debit earned-->
                    <div class="col-md-4">
                        <div class="resturant-card dashboard--card card--bg-1">
                            <h4 class="title">{{$debit}}</h4>
                            <span class="subtitle">
                                {{__('messages.debit')}}
                            </span>
                            <img class="resturant-icon" src="{{asset('public/assets/admin/img/customer-loyality/1.png')}}" alt="dashboard">
                        </div>
                    </div>
                    <!--Debit earned End-->
                    <!--credit earned-->
                    <div class="col-md-4">
                        <div class="resturant-card dashboard--card card--bg-2">
                            <h4 class="title">{{$credit}}</h4>
                            <span class="subtitle">
                                {{__('messages.credit')}}
                            </span>
                            <img class="resturant-icon" src="{{asset('public/assets/admin/img/customer-loyality/2.png')}}" alt="dashboard">
                        </div>
                    </div>
                    <!--credit earned end-->
                    <!--balance earned-->
                    <div class="col-md-4">
                        <div class="resturant-card dashboard--card card--bg-3">
                            <h4 class="title">{{$balance}}</h4>
                            <span class="subtitle">
                                {{__('messages.balance')}}
                            </span>
                            <img class="resturant-icon" src="{{asset('public/assets/admin/img/customer-loyality/3.png')}}" alt="dashboard">
                        </div>
                    </div>
                    <!--balance earned end-->
                </div>
            </div>

        </div>

        <!-- End Stats -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0">
                <h4 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-dollar-outlined"></i>
                    </span>
                    <span>{{__('messages.transactions')}}</span>
                </h4>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-thead-bordered table-align-middle card-table table-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{__('sl')}}</th>
                                <th class="border-0">{{__('messages.transaction')}} {{__('messages.id')}}</th>
                                <th class="border-0">{{__('messages.Customer')}}</th>
                                <th class="border-0">{{__('messages.credit')}}</th>
                                <th class="border-0">{{__('messages.debit')}}</th>
                                <th class="border-0">{{__('messages.balance')}}</th>
                                <th class="border-0">{{__('messages.transaction_type')}}</th>
                                <th class="border-0">{{__('messages.reference')}}</th>
                                <th class="border-0">{{__('messages.created_at')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $k=>$wt)
                            <tr scope="row">
                                <td >{{$k+$transactions->firstItem()}}</td>
                                <td>{{$wt->transaction_id}}</td>
                                <td><a href="{{route('admin.users.customer.view',['user_id'=>$wt->user_id])}}">{{Str::limit($wt->user?$wt->user->f_name.' '.$wt->user->l_name:__('messages.not_found'),20,'...')}}</a></td>
                                <td>{{$wt->credit}}</td>
                                <td>{{$wt->debit}}</td>
                                <td>{{$wt->balance}}</td>
                                <td>
                                    <span class="badge badge-soft-{{$wt->transaction_type=='point_to_wallet'?'success':'dark'}}">
                                        {{__('messages.'.$wt->transaction_type)}}
                                    </span>
                                </td>
                                <td>{{$wt->reference}}</td>
                                <td>{{date('Y/m/d '.config('timeformat'), strtotime($wt->created_at))}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            @if(count($transactions) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $transactions->withQueryString()->links() !!}
            </div>
            @if(count($transactions) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script')

@endpush

@push('script_2')

    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script
        src="{{asset('public/assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/js/hs.chartjs-matrix.js"></script>

    <script>
        $(document).on('ready', function () {
            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{route('admin.users.customer.select-list')}}',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            all: true,
                            page: params.page
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

            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });


            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CHARTJS
            // =======================================================
            $('.js-chart').each(function () {
                $.HSCore.components.HSChartJS.init($(this));
            });

            var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

            // Call when tab is clicked
            $('[data-toggle="chart"]').click(function (e) {
                let keyDataset = $(e.currentTarget).attr('data-datasets')

                // Update datasets for chart
                updatingChart.data.datasets.forEach(function (dataset, key) {
                    dataset.data = updatingChartDatasets[keyDataset][key];
                });
                updatingChart.update();
            })


            // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
            // =======================================================
            function generateHoursData() {
                var data = [];
                var dt = moment().subtract(365, 'days').startOf('day');
                var end = moment().startOf('day');
                while (dt <= end) {
                    data.push({
                        x: dt.format('YYYY-MM-DD'),
                        y: dt.format('e'),
                        d: dt.format('YYYY-MM-DD'),
                        v: Math.random() * 24
                    });
                    dt = dt.add(1, 'day');
                }
                return data;
            }

            $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
                data: {
                    datasets: [{
                        label: 'Commits',
                        data: generateHoursData(),
                        width: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.right - a.left) / 70;
                        },
                        height: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.bottom - a.top) / 10;
                        }
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            title: function () {
                                return '';
                            },
                            label: function (item, data) {
                                var v = data.datasets[item.datasetIndex].data[item.index];

                                if (v.v.toFixed() > 0) {
                                    return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                                } else {
                                    return '<span class="font-weight-bold">No time</span> on ' + v.d;
                                }
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            position: 'bottom',
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'week',
                                round: 'week',
                                displayFormats: {
                                    week: 'MMM'
                                }
                            },
                            ticks: {
                                "labelOffset": 20,
                                "maxRotation": 0,
                                "minRotation": 0,
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 12,
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'day',
                                parser: 'e',
                                displayFormats: {
                                    day: 'ddd'
                                }
                            },
                            ticks: {
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 2,
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });


            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            $('.js-clipboard').each(function () {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });


            // INITIALIZATION OF CIRCLES
            // =======================================================
            $('.js-circle').each(function () {
                var circle = $.HSCore.components.HSCircles.init($(this));
            });
        });
    </script>

    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush

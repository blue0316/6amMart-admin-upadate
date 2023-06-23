@extends('layouts.admin.app')

@section('title',translate('messages.item_report'))

@push('css_or_js')

@endpush

@section('content')

    @php
        $from = session('from_date');
        $to = session('to_date');
    @endphp
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/report.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.item_report')}} 
                    @if (isset($filter) && $filter != 'all_time')
                    <span class="mb-0 h6 badge badge-soft-success ml-2"
                        id="itemCount">( {{ session('from_date') }} - {{ session('to_date') }} )</span>
                        @endif
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card mb-20">
            <div class="card-body">
                <h4 class="">{{translate('Search Data')}}</h4>
                <form action="{{route('admin.transactions.report.set-date')}}" method="post">
                    @csrf
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <select name="module_id" class="form-control js-select2-custom" onchange="set_filter('{{url()->full()}}',this.value,'module_id')" title="{{translate('messages.select')}} {{translate('messages.modules')}}">
                            <option value="" {{!request('module_id') ? 'selected':''}}>{{translate('messages.all')}} {{translate('messages.modules')}}</option>
                            @foreach (\App\Models\Module::notParcel()->get() as $module)
                                <option
                                    value="{{$module->id}}" {{request('module_id') == $module->id?'selected':''}}>
                                    {{$module['module_name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select name="zone_id" class="form-control js-select2-custom"
                        onchange="set_zone_filter('{{url()->full()}}',this.value)" id="zone">
                    <option value="all">{{ translate('messages.All Zones') }}</option>
                    @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                        <option
                            value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                            {{$z['name']}}
                        </option>
                    @endforeach
                </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select name="store_id" onchange="set_store_filter('{{url()->full()}}',this.value)" data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" class="js-data-example-ajax form-control">
                            @if(isset($store))
                            <option value="{{$store->id}}" selected>{{$store->name}}</option>
                            @else
                            <option value="all" selected>{{translate('messages.all')}} {{translate('messages.stores')}}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select name="category_id" id="category_id" onchange="set_category_filter('{{url()->full()}}',this.value)"
                        class="js-data-example-ajax form-control" id="category_id">
                        @if(isset($category))
                        <option value="{{$category->id}}" selected>{{$category->name}}</option>
                        @else
                        <option value="all" selected>{{ translate('messages.All Categories') }}</option>
                        @endif
                    </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
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
                    <div class="col-sm-6 col-md-3">

                            <input type="date" name="from" id="from_date" class="form-control" placeholder="{{ translate('Start Date') }}" {{session()->has('from_date')?'value='.session('from_date'):''}} required>

                    </div>
                    <div class="col-sm-6 col-md-3">

                            <input type="date" name="to" id="to_date" class="form-control" placeholder="{{ translate('End Date') }}" {{session()->has('to_date')?'value='.session('to_date'):''}} required>

                    </div>
                    @endif
                    <div class="col-sm-6 col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary btn-block h--45px">{{translate('Filter')}}</button>
                    </div>
                </div>
            </form>
            </div>
        </div>

        {{-- <div class="card">
            <div class="card-body">
                <div class="report-card-inner mb-3 pt-3">
                    <form action="{{route('admin.transactions.report.set-date')}}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4 col-sm-6">
                                <select name="module_id" class="form-control js-select2-custom" onchange="set_filter('{{url()->full()}}',this.value,'module_id')" title="{{translate('messages.select')}} {{translate('messages.modules')}}">
                                    <option value="" {{!request('module_id') ? 'selected':''}}>{{translate('messages.all')}} {{translate('messages.modules')}}</option>
                                    @foreach (\App\Models\Module::notParcel()->get() as $module)
                                        <option
                                            value="{{$module->id}}" {{request('module_id') == $module->id?'selected':''}}>
                                            {{$module['module_name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <select name="zone_id" class="form-control js-select2-custom"
                                        onchange="set_zone_filter('{{url()->full()}}',this.value)" id="zone">
                                    <option value="all">{{ translate('messages.All Zones') }}</option>
                                    @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                                        <option
                                            value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                                            {{$z['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <select name="store_id" onchange="set_store_filter('{{url()->full()}}',this.value)" data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" class="js-data-example-ajax form-control">
                                    @if(isset($store))
                                    <option value="{{$store->id}}" selected>{{$store->name}}</option>
                                    @else
                                    <option value="all" selected>{{translate('messages.all')}} {{translate('messages.stores')}}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <input type="date" name="from" id="from_date" {{session()->has('from_date')?'value='.session('from_date'):''}}
                                        class="form-control" required>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <input type="date" name="to" id="to_date" {{session()->has('to_date')?'value='.session('to_date'):''}}
                                        class="form-control" required>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <button type="submit" class="btn btn--primary btn-block">{{translate('show_data')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
        <!-- End Stats -->
        <!-- Card -->
        <div class="row card mt-4">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h3 class="card-title">
                        {{translate('Item report table')}}<span class="badge badge-soft-secondary" id="countItems">{{ $items->total() }}</span>
                    </h3>
                    <form id="search-form" class="search-form">
                    @csrf
                    <!-- Search -->
                    <div class="input--group input-group">
                        <input id="datatableSearch" name="search" type="search" class="form-control" placeholder="{{translate('ex_:_search_item_name')}}" aria-label="{{translate('messages.search_here')}}">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                    <!-- End Search -->
                    </form>                    <!-- Unfold -->
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
                            <span class="dropdown-header">{{ translate('messages.download') }}
                                {{ translate('messages.options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.report.item-wise-export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.report.item-wise-export', ['type'=>'csv',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom" id="table-div">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                    data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [],
                            "width": "5%",
                            "orderable": false
                        }],
                        "order": [],
                        "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                        },

                        "entries": "#datatableEntries",

                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false
                    }'>
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('sl')}}</th>
                        <th class="w--2">{{translate('messages.name')}}</th>
                        <th class="w--2">{{translate('messages.module')}}</th>
                        <th class="w--2">{{translate('messages.store')}}</th>
                        <th>{{translate('messages.order')}} {{translate('messages.count')}}</th>
                        <th>{{translate('messages.price')}}</th>
                        <th>{{translate('messages.total_amount_sold')}}</th>
                        <th>{{translate('messages.total_discount_given')}}</th>
                        <th>{{translate('messages.average_sale_value')}}</th>
                        <th>{{translate('messages.average_ratings')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">

                    @foreach($items as $key=>$item)
                        <tr>
                            <td>{{$key+$items->firstItem()}}</td>
                            <td>
                                <a class="media align-items-center" href="{{route('admin.item.view',[$item['id'],'module_id'=>$item['module_id']])}}">
                                    <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$item['image']}}"
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{$item['name']}}</h5>
                                    </div>
                                </a>
                            </td>
                            <td>
                                {{ $item->module->module_name }}
                            </td>
                            <td>
                                @if($item->store)
                                {{Str::limit($item->store->name,25,'...')}}
                                @else
                                {{translate('messages.store')}} {{translate('messages.deleted')}}
                                @endif
                            </td>
                            <td>
                                {{$item->orders_count}}
                            </td>
                            <td>
                                {{ \App\CentralLogics\Helpers::format_currency($item->price) }}
                            </td>
                            <td>
                                {{ \App\CentralLogics\Helpers::format_currency($item->orders_sum_price) }}
                            </td>
                            <td>
                                {{ \App\CentralLogics\Helpers::format_currency($item->orders_sum_discount_on_item) }}
                            </td>
                            <td>
                                {{ $item->orders_count>0? \App\CentralLogics\Helpers::format_currency(($item->orders_sum_price-$item->orders_sum_discount_on_item)/$item->orders_count):0 }}
                            </td>
                            <td>
                                <div class="rating">
                                    <span><i class="tio-star"></i></span>{{ round($item->avg_rating,1) }} ({{ $item->rating_count }})
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($items) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $items->links() !!}
                </div>
                @if(count($items) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
            <!-- End Table -->
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

            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{url('/')}}/admin/store/get-stores',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            // all:true,
                    @if(isset($zone))zone_ids: [{{$zone->id}}], @endif
                    @if(request('module_id'))module_id: {{request('module_id')}}, @endif
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

            $('#category_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories?parent_id=0',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        @if(request('module_id'))module_id: {{request('module_id')}}, @endif
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

        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.report.item-wise-report-search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#countItems').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush

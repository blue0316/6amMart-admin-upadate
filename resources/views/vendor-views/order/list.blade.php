@extends('layouts.vendor.app')

@section('title',translate('messages.Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title text-capitalize">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/order.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{str_replace('_',' ',$status)}} {{translate('messages.orders')}}
                    <span class="badge badge-soft-dark ml-2">{{$orders->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                        <form action="javascript:" id="search-form" class="search-form min--260">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_:_search_order_id')}}" aria-label="{{translate('messages.search')}}" required>
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>
                        <!-- Unfold -->
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                                data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                                <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}
                            </a>

                            <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{translate('messages.options')}}</span>
                                <a id="export-copy" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{asset('public/assets/admin')}}/svg/illustrations/copy.svg"
                                            alt="Image Description">
                                    {{translate('messages.copy')}}
                                </a>
                                <a id="export-print" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{asset('public/assets/admin')}}/svg/illustrations/print.svg"
                                            alt="Image Description">
                                    {{translate('messages.print')}}
                                </a>
                                <div class="dropdown-divider"></div>
                                <span
                                    class="dropdown-header">{{translate('messages.download')}} {{translate('messages.options')}}</span>
                                <a id="export-excel" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                            alt="Image Description">
                                    {{translate('messages.excel')}}
                                </a>
                                <a id="export-csv" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                    .{{translate('messages.csv')}}
                                </a>
                                <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{asset('public/assets/admin')}}/svg/components/pdf.svg"
                                            alt="Image Description">
                                    {{translate('messages.pdf')}}
                                </a>
                            </div>
                        </div>
                        <!-- End Unfold -->

                        <!-- Unfold -->
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white h--40px" href="javascript:;"
                                data-hs-unfold-options='{
                                    "target": "#showHideDropdown",
                                    "type": "css-animation"
                                }'>
                                <i class="tio-table mr-1"></i> {{translate('messages.column')}} <span
                                    class="badge badge-soft-dark rounded-circle ml-1"></span>
                            </a>

                            <div id="showHideDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="mr-2">{{translate('messages.order')}}</span>

                                            <!-- Checkbox Switch -->
                                            <label class="toggle-switch toggle-switch-sm" for="toggleColumn_order">
                                                <input type="checkbox" class="toggle-switch-input"
                                                        id="toggleColumn_order" checked>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <!-- End Checkbox Switch -->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="mr-2">{{translate('messages.date')}}</span>

                                            <!-- Checkbox Switch -->
                                            <label class="toggle-switch toggle-switch-sm" for="toggleColumn_date">
                                                <input type="checkbox" class="toggle-switch-input"
                                                        id="toggleColumn_date" checked>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <!-- End Checkbox Switch -->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="mr-2">{{translate('messages.customer')}}</span>

                                            <!-- Checkbox Switch -->
                                            <label class="toggle-switch toggle-switch-sm"
                                                    for="toggleColumn_customer">
                                                <input type="checkbox" class="toggle-switch-input"
                                                        id="toggleColumn_customer" checked>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <!-- End Checkbox Switch -->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span
                                                class="mr-2 text-capitalize">{{translate('messages.total')}} {{translate('messages.amount')}}</span>

                                            <!-- Checkbox Switch -->
                                            <label class="toggle-switch toggle-switch-sm"
                                                    for="toggleColumn_payment_status">
                                                <input type="checkbox" class="toggle-switch-input"
                                                        id="toggleColumn_payment_status" checked>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <!-- End Checkbox Switch -->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="mr-2">{{translate('messages.order')}} {{translate('messages.status')}}</span>

                                            <!-- Checkbox Switch -->
                                            <label class="toggle-switch toggle-switch-sm" for="toggleColumn_order_status">
                                                <input type="checkbox" class="toggle-switch-input"
                                                        id="toggleColumn_order_status" checked>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <!-- End Checkbox Switch -->
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="mr-2">{{translate('messages.actions')}}</span>

                                            <!-- Checkbox Switch -->
                                            <label class="toggle-switch toggle-switch-sm"
                                                    for="toggleColumn_actions">
                                                <input type="checkbox" class="toggle-switch-input"
                                                        id="toggleColumn_actions" checked>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <!-- End Checkbox Switch -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Unfold -->
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->
            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true,
                                    "paging":false
                                }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="border-0">
                                {{translate('messages.#')}}
                            </th>
                            <th class="border-0 table-column-pl-0">{{translate('messages.order_id')}}</th>
                            <th class="border-0">{{translate('messages.order_date')}}</th>
                            <th class="border-0">{{translate('messages.customer_information')}}</th>
                            <th class="border-0">{{translate('messages.total_amount')}}</th>
                            <th class="border-0 text-center">{{translate('messages.order')}} {{translate('messages.status')}}</th>
                            <th class="border-0 text-center">{{translate('messages.actions')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="">
                                    {{$key+$orders->firstItem()}}
                                </td>
                                <td class="table-column-pl-0">
                                    <a href="{{route('vendor.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>
                                        {{date('d M Y',strtotime($order['created_at']))}}
                                    </div>
                                    <div class="d-block text-uppercase">
                                        {{date(config('timeformat'),strtotime($order['created_at']))}}
                                    </div>
                                </td>
                                <td>
                                    @if($order->customer)
                                        {{-- <a class="text-body text-capitalize"
                                        href="{{route('vendor.customer.view',[$order['user_id']])}}"> --}}
                                        <strong>
                                            {{$order->customer['f_name'].' '.$order->customer['l_name']}}
                                        </strong>
                                        <div>
                                            {{$order->customer['phone']}}
                                        </div>
                                    {{-- </a> --}}
                                    @else
                                        <label
                                            class="badge badge-danger">{{translate('messages.invalid')}} {{translate('messages.customer')}} {{translate('messages.data')}}</label>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-right mw--85px">
                                        <div>
                                            {{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}
                                        </div>
                                        @if($order->payment_status=='paid')
                                        <strong class="text-success">
                                            {{translate('messages.paid')}}
                                        </strong>
                                        @else
                                        <strong class="text-danger">
                                            {{translate('messages.unpaid')}}
                                        </strong>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                        {{translate('messages.pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                        {{translate('messages.confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                        {{translate('messages.processing')}}
                                        </span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge badge-soft-warning">
                                        {{translate('messages.out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                        {{translate('messages.delivered')}}
                                        </span>
                                    @elseif($order['order_status']=='failed')
                                        <span class="badge badge-soft-danger">
                                        {{translate('messages.payment')}}  {{translate('messages.failed')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                        {{str_replace('_',' ',$order['order_status'])}}
                                        </span>
                                    @endif
                                    @if($order['order_type']=='take_away')
                                        <div class="text-info mt-1">
                                            {{translate('messages.take_away')}}
                                        </div>
                                    @else
                                        <div class="text-title mt-1">
                                        {{translate('messages.home Delivery')}}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn btn-sm btn--warning btn-outline-warning action-btn" href="{{route('vendor.order.details',['id'=>$order['id']])}}"><i class="tio-visible-outlined"></i></a>
                                        <a class="btn btn-sm btn--primary btn-outline-primary action-btn" target="_blank" href="{{route('vendor.order.generate-invoice',[$order['id']])}}"><i class="tio-print"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($orders) === 0)
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
            <!-- Footer -->
            <div class="card-footer">
                {!! $orders->links() !!}
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });


            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none'
                    },
                    {
                        extend: 'csv',
                        className: 'd-none'
                    },
                    {
                        extend: 'pdf',
                        className: 'd-none'
                    },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                        '<img class="w-7rem mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">' +

                        '</div>'
                }
            });

            $('#export-copy').click(function () {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function () {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function () {
                datatable.button('.buttons-csv').trigger()
            });

            $('#export-pdf').click(function () {
                datatable.button('.buttons-pdf').trigger()
            });

            $('#export-print').click(function () {
                datatable.button('.buttons-print').trigger()
            });

            $('#toggleColumn_order').change(function (e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_date').change(function (e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_customer').change(function (e) {
                datatable.columns(3).visible(e.target.checked)
            })

            $('#toggleColumn_payment_status').change(function (e) {
                datatable.columns(4).visible(e.target.checked)
            })

            $('#toggleColumn_order_status').change(function (e) {
                datatable.columns(5).visible(e.target.checked)
            })

            $('#toggleColumn_actions').change(function (e) {
                datatable.columns(6).visible(e.target.checked)
            })

            // INITIALIZATION OF TAGIFY
            // =======================================================
            $('.js-tagify').each(function () {
                var tagify = $.HSCore.components.HSTagify.init($(this));
            });
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
                url: '{{route('vendor.order.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush

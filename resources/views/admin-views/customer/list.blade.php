@extends('layouts.admin.app')

@section('title', translate('Customer List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/people.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{ translate('messages.customers') }} <span class="badge badge-soft-dark ml-2" id="count">{{ $customers->total() }}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0  py-2">
                <div class="search--button-wrapper justify-content-end">
                    <form action="{{ route('admin.users.customer.list') }}" id="search-form" class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control min-height-40"
                                value="{{ request()->get('search') }}" placeholder="{{ translate('search_by_name') }}"
                                aria-label="Search" required>
                            <button type="submit" class="btn btn--secondary min-height-40"><i class="tio-search"></i></button>
                            {{-- @if (request()->get('search'))
                                <button type="reset" class="btn btn-info mx-1 py-1 min-height-40"
                                    onclick="location.href = '{{ route('admin.users.customer.list') }}'">{{ translate('messages.reset') }}</button>
                            @endif --}}
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
                            <span class="dropdown-header">{{ translate('messages.options') }}</span>
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
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">{{ translate('messages.download') }}
                                {{ translate('messages.options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.customer.export', ['type'=>'excel'])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.customer.export', ['type'=>'csv'])}}">
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

                    <!-- Unfold -->
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white min-height-40" href="javascript:;"
                            data-hs-unfold-options='{
                                    "target": "#showHideDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-table mr-1"></i> {{ translate('messages.columns') }} <span
                                class="badge badge-soft-dark rounded-circle ml-1"></span>
                        </a>

                        <div id="showHideDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card min--240">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.name') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_name">
                                            <input type="checkbox" class="toggle-switch-input"
                                                id="toggleColumn_name" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.contact_information') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_email">
                                            <input type="checkbox" class="toggle-switch-input"
                                                id="toggleColumn_email" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.total') }}
                                            {{ translate('messages.order') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm"
                                            for="toggleColumn_total_order">
                                            <input type="checkbox" class="toggle-switch-input"
                                                id="toggleColumn_total_order" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.active/Inactive') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_status">
                                            <input type="checkbox" class="toggle-switch-input"
                                                id="toggleColumn_status" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="mr-2">{{ translate('messages.actions') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_actions">
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
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                            "columnDefs": [{
                                "targets": [0],
                                "orderable": false
                            }],
                            "order": [],
                            "info": {
                            "totalQty": "#datatableWithPaginationInfoTotalQty"
                            },
                            "search": "#datatableSearch",
                            "entries": "#datatableEntries",
                            "pageLength": 25,
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">
                                    {{ translate('sl') }}
                                </th>
                                <th class="table-column-pl-0 border-0">{{ translate('messages.name') }}</th>
                                <th class="border-0">{{ translate('messages.contact_information') }}</th>
                                <th class="border-0">{{ translate('messages.total') }} {{ translate('messages.order') }}</th>
                                <th class="border-0">{{ translate('messages.active') }}/{{ translate('messages.inactive') }}</th>
                                <th class="border-0">{{ translate('messages.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                            @foreach ($customers as $key => $customer)
                                <tr class="">
                                    <td class="">
                                        {{ $key + $customers->firstItem() }}
                                    </td>
                                    <td class="table-column-pl-0">
                                        <a href="{{ route('admin.users.customer.view', [$customer['id']]) }}" class="text--hover">
                                            {{ $customer['f_name'] . ' ' . $customer['l_name'] }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $customer['email'] }}
                                        </div>
                                        <div>
                                            {{ $customer['phone'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <label class="badge">
                                            {{ $customer->order_count }}
                                        </label>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm ml-xl-4" for="stocksCheckbox{{ $customer->id }}">
                                            <input type="checkbox"
                                                onclick="status_change_alert('{{ route('admin.users.customer.status', [$customer->id, $customer->status ? 0 : 1]) }}', '{{ $customer->status? translate('messages.you_want_to_block_this_customer'): translate('messages.you_want_to_unblock_this_customer') }}', event)"
                                                class="toggle-switch-input" id="stocksCheckbox{{ $customer->id }}"
                                                {{ $customer->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <a class="btn action-btn btn--warning btn-outline-warning"
                                            href="{{ route('admin.users.customer.view', [$customer['id']]) }}"
                                            title="{{ translate('messages.view') }} {{ translate('messages.customer') }}"><i
                                                class="tio-visible-outlined"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->
            </div>

            @if(count($customers) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $customers->links() !!}
            </div>
            @if(count($customers) === 0)
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

@push('script_2')
    <script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('messages.Are you sure?') }}',
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
                    location.href = url;
                }
            })
        }
        $(document).on('ready', function() {
            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function() {
                new HsNavScroller($(this)).init()
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });


            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'd-none'
                    },
                    // {
                    //     extend: 'excel',
                    //     className: 'd-none'
                    // },
                    // {
                    //     extend: 'csv',
                    //     className: 'd-none'
                    // },
                    // {
                    //     extend: 'pdf',
                    //     className: 'd-none'
                    // },
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
                        '<img class="w-7rem mb-3" src="{{ asset('public/assets/admin') }}/svg/illustrations/sorry.svg" alt="Image Description">' +

                        '</div>'
                }
            });

            $('#export-copy').click(function() {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function() {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function() {
                datatable.button('.buttons-csv').trigger()
            });

            $('#export-pdf').click(function() {
                datatable.button('.buttons-pdf').trigger()
            });

            $('#export-print').click(function() {
                datatable.button('.buttons-print').trigger()
            });

            $('#datatableSearch').on('mouseup', function(e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function() {
                    var newValue = $input.val();

                    if (newValue == "") {
                        // Gotcha
                        datatable.search('').draw();
                    }
                }, 1);
            });

            $('#toggleColumn_name').change(function(e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_email').change(function(e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_total_order').change(function(e) {
                datatable.columns(3).visible(e.target.checked)
            })


            $('#toggleColumn_status').change(function(e) {
                datatable.columns(4).visible(e.target.checked)
            })

            $('#toggleColumn_actions').change(function(e) {
                datatable.columns(5).visible(e.target.checked)
            })

            // INITIALIZATION OF TAGIFY
            // =======================================================
            $('.js-tagify').each(function() {
                var tagify = $.HSCore.components.HSTagify.init($(this));
            });
        });
    </script>

    <script>
        $('#search-form').on('submit', function() {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.users.customer.search') }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                    $('#count').html(data.count);
                },
                complete: function() {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush

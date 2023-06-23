@extends('layouts.admin.app')
@section('title', translate('Subscribed Emails'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/email.png')}}" class="w--26" alt="">
                </span>
                <span>{{ translate('messages.subscribed_mail_list') }}
                        <span class="badge badge-soft-dark ml-2" id="count">{{ \App\Models\Newsletter::count() }}</span>
                </span>
            </h1>
        </div>
        <!-- Page Header -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper justify-content-end">
                    <form action="javascript:" id="search-form" class="search-form">
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                value="{{ request()->get('search') }}" placeholder="{{ translate('messages.ex_:_search_email') }}"
                                aria-label="Search" required>
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            @if (request()->get('search'))
                                <button type="reset" class="btn btn-info mx-1"
                                    onclick="location.href = '{{ route('admin.users.customer.subscribed') }}'">{{ translate('messages.reset') }}</button>
                            @endif
                        </div>
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
                                                <span class="dropdown-header">{{ translate('messages.download') }}
                                                    {{ translate('messages.options') }}</span>
                                                <a id="export-excel" class="dropdown-item" href="{{route('admin.users.customer.subscriber-export', ['type'=>'excel'])}}">
                                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                                        alt="Image Description">
                                                    {{ translate('messages.excel') }}
                                                </a>
                                                <a id="export-csv" class="dropdown-item" href="{{route('admin.users.customer.subscriber-export', ['type'=>'csv'])}}">
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
            <!-- End Header -->
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table generalData" data-hs-datatables-options='{
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
                            <th class="border-0">{{ translate('messages.email') }}</th>
                            <th class="border-0">{{ translate('messages.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody id="set-rows">
                        @if (count($subscribedCustomers))
                            @foreach ($subscribedCustomers as $key => $customer)
                                <tr>
                                    <td>
                                        {{ ++$key }}
                                    </td>
                                    <td>
                                        {{ $customer->email }}
                                    </td>
                                    <td>{{ date('Y-m-d', strtotime($customer->created_at)) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>

                </table>
                @if(count($subscribedCustomers) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                @endif
            </div>
            <!-- End Table -->
            <!-- Footer -->
            {{-- <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            $customer->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div> --}}
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection
@push('script_2')
    <script type="text/javascript">
        $('#search-form').on('submit', function() {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ url('admin/customer/subscriber-search') }}',
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

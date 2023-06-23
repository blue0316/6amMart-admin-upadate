@extends('layouts.admin.app')

@section('title',translate('Withdraw Request'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title mr-3 mb-md-0">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/withdraw.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.store')}} {{ translate('messages.withdraw')}} {{ translate('messages.transaction')}} <span
                        class="badge badge-soft-dark ml-2" id="itemCount">{{$withdraw_req->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <div class="card mt-2">

            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <form action="javascript:" id="search-form" class="search-form">
                        @csrf
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" type="search" class="form-control h--40px" placeholder="{{translate('ex_:_search_store_name')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary h--40px"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>

                    <div class="max-sm-flex-1">
                        <select name="withdraw_status_filter" onchange="status_filter(this.value)"
                                class="custom-select h--40px py-0">
                            <option
                                value="all" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all'?'selected':''}}>
                                {{translate('messages.all')}}
                            </option>
                            <option
                                value="approved" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved'?'selected':''}}>
                                {{translate('messages.approved')}}
                            </option>
                            <option
                                value="denied" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied'?'selected':''}}>
                                {{translate('messages.denied')}}
                            </option>
                            <option
                                value="pending" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending'?'selected':''}}>
                                {{translate('messages.pending')}}
                            </option>

                        </select>
                    </div>
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
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.store.withdraw_export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.store.withdraw_export', ['type'=>'csv',request()->getQueryString()])}}">
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
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('sl')}}</th>
                            <th class="border-0">{{translate('messages.amount')}}</th>
                            {{-- <th>{{translate('messages.note')}}</th> --}}
                            <th class="border-0">{{ translate('messages.store') }}</th>
                            <th class="border-0">{{translate('messages.request_time')}}</th>
                            <th class="border-0">{{translate('messages.status')}}</th>
                            <th class="border-0">{{translate('messages.action')}}</th>
                        </tr>
                        </thead>
                        <tbody id="set-rows">
                        @foreach($withdraw_req as $k=>$wr)
                            <tr>
                                <td scope="row">{{$k+$withdraw_req->firstItem()}}</td>
                                <td>{{$wr['amount']}}</td>
                                {{-- <td>{{$wr->__action_note}}</td> --}}
                                <td>
                                    @if($wr->vendor)
                                    <a class="deco-none"
                                        href="{{route('admin.store.view',[$wr->vendor['id'],'module_id'=>$wr->vendor->stores[0]->module_id])}}">{{ Str::limit($wr->vendor->stores[0]->name, 20, '...') }}</a>
                                    @else
                                    {{translate('messages.store deleted!') }}
                                    @endif
                                </td>
                                <td>{{date('Y-m-d '.config('timeformat'),strtotime($wr->created_at))}}</td>
                                <td>
                                    @if($wr->approved==0)
                                        <label class="badge badge-primary">{{ translate('messages.pending') }}</label>
                                    @elseif($wr->approved==1)
                                        <label class="badge badge-success">{{ translate('messages.approved') }}</label>
                                    @else
                                        <label class="badge badge-danger">{{ translate('messages.denied') }}</label>
                                    @endif
                                </td>
                                <td>
                                    @if($wr->vendor)
                                    <a href="{{route('admin.transactions.store.withdraw_view',[$wr['id'],$wr->vendor['id']])}}"
                                        class="btn action-btn btn--warning btn-outline-warning"><i class="tio-visible-outlined"></i>
                                    </a>
                                    @else
                                    {{translate('messages.store').' '.translate('messages.deleted') }}
                                    @endif
                                    {{--<a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                    onclick="form_alert('withdraw-{{$wr['id']}}','Want to delete this  ?')">{{translate('messages.Delete')}}</a>
                                    <form action="{{route('vendor.withdraw.close',[$wr['id']])}}"
                                            method="post" id="withdraw-{{$wr['id']}}">
                                        @csrf @method('delete')
                                    </form>--}}

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($withdraw_req) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $withdraw_req->links() !!}
            </div>
            @if(count($withdraw_req) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function status_filter(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.store.status-filter')}}',
                data: {
                    withdraw_status_filter: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    console.log(data)
                    location.reload();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }

        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.store.withdraw_search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.total);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush


@extends('layouts.admin.app')

@section('title',$store->name."'s ".translate('messages.transactions'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    @include('admin-views.vendor.view.partials._header',['store'=>$store])
    <div class="card">
        <div class="card-header border-0 py-2">
            <div class="search--button-wrapper">
                <ul class="nav nav-tabs mr-auto transaction--table-nav">
                    <li class="nav-item">
                        @php($account_transaction = \App\Models\AccountTransaction::where('from_type', 'store')->where('from_id', $store->id)->count())
                        @php($account_transaction = isset($account_transaction) ? $account_transaction : 0)
                        <a class="nav-link text-capitalize {{$sub_tab=='cash'?'active':''}}" href="{{route('admin.store.view', ['store'=>$store->id, 'tab'=> 'transaction', 'sub_tab'=>'cash'])}}"  aria-disabled="true">{{translate('cash_transaction')}} ({{$account_transaction}})</a>
                    </li>
                    <li class="nav-item">
                        @php($digital_transaction = \App\Models\OrderTransaction::where('vendor_id', $store->id)->count())
                        @php($digital_transaction = isset($digital_transaction) ? $digital_transaction : 0)
                        <a class="nav-link text-capitalize {{$sub_tab=='digital'?'active':''}}" href="{{route('admin.store.view', ['store'=>$store->id, 'tab'=> 'transaction', 'sub_tab'=>'digital'])}}"  aria-disabled="true">{{translate('order_transactions')}} ({{$digital_transaction}})</a>
                    </li>
                    <li class="nav-item">
                        @php($withdraw_transaction = \App\Models\WithdrawRequest::where('vendor_id',$store->id)->count())
                        @php($withdraw_transaction = isset($withdraw_transaction) ? $withdraw_transaction : 0)
                        <a class="nav-link text-capitalize {{$sub_tab=='withdraw'?'active':''}}" href="{{route('admin.store.view', ['store'=>$store->id, 'tab'=> 'transaction', 'sub_tab'=>'withdraw'])}}"  aria-disabled="true">{{translate('withdraw_transactions')}} ({{$withdraw_transaction}})</a>
                    </li>
                </ul>
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
                        {{-- <span class="dropdown-header">{{translate('messages.options')}}</span>
                        <a id="export-copy" class="dropdown-item" href="javascript:;" title="{{translate('messages.current_page_only')}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/illustrations/copy.svg"
                                    alt="Image Description">
                            {{translate('messages.copy')}}
                        </a>
                        <a id="export-print" class="dropdown-item" href="javascript:;" title="{{translate('messages.current_page_only')}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/illustrations/print.svg"
                                    alt="Image Description">
                            {{translate('messages.print')}}
                        </a>
                        <div class="dropdown-divider"></div> --}}
                        <span class="dropdown-header">{{translate('messages.download')}} {{translate('messages.options')}}</span>
                        @if($sub_tab=='cash')
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.store.cash_export', ['type'=>'excel', 'store_id'=>$store->id]) }}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                    alt="Image Description">
                            {{translate('messages.excel')}}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.store.cash_export', ['type'=>'csv', 'store_id'=>$store->id])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                            .{{translate('messages.csv')}}
                        </a>
                        @elseif ($sub_tab=='digital')
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.store.order_export', ['type'=>'excel', 'store_id'=>$store->id])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                    alt="Image Description">
                            {{translate('messages.excel')}}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.store.order_export', ['type'=>'csv', 'store_id'=>$store->id])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                            .{{translate('messages.csv')}}
                        </a>
                        @elseif ($sub_tab=='withdraw')
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.store.withdraw_trans_export', ['type'=>'excel', 'store_id'=>$store->id])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                    alt="Image Description">
                            {{translate('messages.excel')}}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.store.withdraw_trans_export', ['type'=>'csv', 'store_id'=>$store->id])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                            .{{translate('messages.csv')}}
                        </a>
                        @endif
                    </div>
                </div>
                <!-- End Unfold -->
            </div>
        </div>
        <div class="card-body p-0">

        @if($sub_tab=='cash')
            @include('admin-views.vendor.view.partials.cash_transaction')
        @elseif ($sub_tab=='digital')
            @include('admin-views.vendor.view.partials.digital_transaction')
        @elseif ($sub_tab=='withdraw')
            @include('admin-views.vendor.view.partials.withdraw_transaction')
        @endif
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&callback=initMap&v=3.45.8" ></script>
    <script>
        const myLatLng = { lat: {{$store->latitude}}, lng: {{$store->longitude}} };
        let map;
        initMap();
        function initMap() {
                 map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
            });
            new google.maps.Marker({
                position: myLatLng,
                map,
                title: "{{$store->name}}",
            });
        }
    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush

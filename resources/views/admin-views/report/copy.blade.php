@extends('layouts.admin.app')

@section('title',translate('messages.item_wise_report'))

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
                    <img src="{{asset('public/assets/admin/img/item-report.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.item_report')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card mb-20">
            <div class="card-body">
                <h4 class="">{{translate('Search Data')}}</h4>
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <select class="custom-select custom-select">
                            <option value="all">{{translate('All Module')}}</option>
                            <option value="all">{{translate('All Module')}}</option>
                            <option value="all">{{translate('All Module')}}</option>
                            <option value="all">{{translate('All Module')}}</option>
                            <option value="all">{{translate('All Module')}}</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select class="custom-select custom-select">
                            <option value="all">{{translate('All Zone')}}</option>
                            <option value="all">{{translate('All Zone')}}</option>
                            <option value="all">{{translate('All Zone')}}</option>
                            <option value="all">{{translate('All Zone')}}</option>
                            <option value="all">{{translate('All Zone')}}</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select class="custom-select custom-select">
                            <option value="all">{{translate('All Stores')}}</option>
                            <option value="all">{{translate('All Stores')}}</option>
                            <option value="all">{{translate('All Stores')}}</option>
                            <option value="all">{{translate('All Stores')}}</option>
                            <option value="all">{{translate('All Stores')}}</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select class="custom-select custom-select">
                            <option value="all">{{translate('All Category')}}</option>
                            <option value="all">{{translate('All Category')}}</option>
                            <option value="all">{{translate('All Category')}}</option>
                            <option value="all">{{translate('All Category')}}</option>
                            <option value="all">{{translate('All Category')}}</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <select class="custom-select custom-select">
                            <option value="all">{{translate('This Year')}}</option>
                            <option value="all">{{translate('This Month')}}</option>
                            <option value="all">{{translate('This Week')}}</option>
                            <option value="all">{{translate('Today')}}</option>
                            <option value="all">{{translate('Custom Date Range')}}</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <!-- <label class="input-label">{{translate('start')}} {{translate('date')}}</label> -->
                        <label class="input-date">
                            <input type="text" name="from" id="from_date" class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('Start Date') }}" required>
                        </label>
                    </div>
                    <div class="col-sm-6 col-md-3">
                            <!-- <label class="input-label">{{translate('end')}} {{translate('date')}}</label> -->
                            <label class="input-date">
                            <input type="text" name="to" id="to_date" class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('End Date') }}" required>
                    </label>
                    </div>
                    <div class="col-sm-6 col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary btn-block h--45px">{{translate('Filter')}}</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stats -->
        <div class="mb-20">
            <div class="row g-4">
                <div class="col-lg-3">
                    <a class="__card-1 h-100" href="#">
                        <img src="{{asset('/public/assets/admin/img/report/new/total.png')}}" class="icon" alt="report/new">
                        <h3 class="title">2,000</h3>
                        <h6 class="subtitle">Total Order</h6>
                    </a>
                </div>
                <div class="col-lg-9">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <a class="__card-2 __bg-1" href="#">
                            <h4 class="title">36</h4>
                            <span class="subtitle">In Progress Orders</span>
                            <img src="{{asset('/public/assets/admin/img/report/new/total.png')}}" alt="report/new" class="card-icon">
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a class="__card-2 __bg-2" href="#">
                            <h4 class="title">36</h4>
                            <span class="subtitle">On the Way</span>
                            <img src="{{asset('/public/assets/admin/img/report/new/on-the-way.png')}}" alt="report/new" class="card-icon">
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a class="__card-2 __bg-3" href="#">
                            <h4 class="title">36</h4>
                            <span class="subtitle">Delivered Orders</span>
                            <img src="{{asset('/public/assets/admin/img/report/new/delivered.png')}}" alt="report/new" class="card-icon">
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a class="__card-2 __bg-4" href="#">
                            <h4 class="title">36</h4>
                            <span class="subtitle">Failed Orders</span>
                            <img src="{{asset('/public/assets/admin/img/report/new/failed.png')}}" alt="report/new" class="card-icon">
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a class="__card-2 __bg-5" href="#">
                            <h4 class="title">36</h4>
                            <span class="subtitle">Refunded Orders</span>
                            <img src="{{asset('/public/assets/admin/img/report/new/refunded.png')}}" alt="report/new" class="card-icon">
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a class="__card-2 __bg-6" href="#">
                            <h4 class="title">36</h4>
                            <span class="subtitle">Canceled Orders</span>
                            <img src="{{asset('/public/assets/admin/img/report/new/canceled.png')}}" alt="report/new" class="card-icon">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-20">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <a class="__card-3 h-100" href="#">
                                <img src="{{asset('/public/assets/admin/img/report/new/trx1.png')}}" class="icon" alt="report/new">
                                <h3 class="title text-008958">$2,000</h3>
                                <h6 class="subtitle">Completed Transaction</h6>
                                <div class="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="This is dummy information text for report card">
                                    <img src="{{asset('/public/assets/admin/img/report/new/info1.png')}}" alt="report/new">
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a class="__card-3 h-100" href="#">
                                <img src="{{asset('/public/assets/admin/img/report/new/trx2.png')}}" class="icon" alt="report/new">
                                <h3 class="title text-006AB4">$2,000</h3>
                                <h6 class="subtitle">On-Hold Transactions</h6>
                                <div class="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="This is dummy information text for report card">
                                    <img src="{{asset('/public/assets/admin/img/report/new/info2.png')}}" alt="report/new">
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <a class="__card-3 h-100" href="#">
                                <img src="{{asset('/public/assets/admin/img/report/new/trx3.png')}}" class="icon" alt="report/new">
                                <h3 class="title text-FF5A54">$2,000</h3>
                                <h6 class="subtitle">Canceled Transactions</h6>
                                <div class="info-icon" data-toggle="tooltip" data-placement="top" data-original-title="This is dummy information text for report card">
                                    <img src="{{asset('/public/assets/admin/img/report/new/info3.png')}}" alt="report/new">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <div class="__card-vertical">
                                <div class="__card-vertical-img">
                                    <img class="img" src="{{asset('/public/assets/admin/img/report/new/admin-earning.png')}}" alt="">
                                    <h4 class="name">Admin Earning</h4>
                                    <div class="info-icon" data-toggle="tooltip" data-placement="right" data-original-title="This is dummy information text for report card">
                                        <img src="{{asset('/public/assets/admin/img/report/new/info1.png')}}" alt="report/new">
                                    </div>
                                </div>
                                <h4 class="earning text-0661CB"><small>$</small> 345</h4>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="__card-vertical">
                                <div class="__card-vertical-img">
                                    <img class="img" src="{{asset('/public/assets/admin/img/report/new/store-earning.png')}}" alt="">
                                    <h4 class="name">Store Earning</h4>
                                    <div class="info-icon" data-toggle="tooltip" data-placement="right" data-original-title="This is dummy information text for report card">
                                        <img src="{{asset('/public/assets/admin/img/report/new/info2.png')}}" alt="report/new">
                                    </div>
                                </div>
                                <h4 class="earning text-00AA6D"><small>$</small> 345</h4>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="__card-vertical">
                                <div class="__card-vertical-img">
                                    <img class="img" src="{{asset('/public/assets/admin/img/report/new/deliveryman-earning.png')}}" alt="">
                                    <h4 class="name">Deliveryman Earning</h4>
                                    <div class="info-icon" data-toggle="tooltip" data-placement="right" data-original-title="This is dummy information text for report card">
                                        <img src="{{asset('/public/assets/admin/img/report/new/info3.png')}}" alt="report/new">
                                    </div>
                                </div>
                                <h4 class="earning text-FF7500"><small>$</small> 345</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h3 class="card-title">
                        {{translate('Item List')}}
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
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.report.item-wise-export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.report.item-wise-export', ['type'=>'csv',request()->getQueryString()])}}">
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
            <div class="card-body px-0 pt-0">
                <!-- Table Here -->
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')

<script>
    $(document).on('ready', function () {
        $('.js-flatpickr').each(function () {
            $.HSCore.components.HSFlatpickr.init($(this));
        });
    });
</script>
    
@endpush

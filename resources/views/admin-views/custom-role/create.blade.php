@extends('layouts.admin.app')
@section('title',translate('messages.custom_role'))
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/role.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('messages.employee')}} {{translate('messages.Role')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.users.custom-role.create')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="input-label qcont" for="name">{{translate('messages.role_name')}}</label>
                            <input type="text" name="name" class="form-control" id="name" aria-describedby="emailHelp"
                                placeholder="{{translate('role_name_example')}}" required value="{{old('name')}}">
                        </div>

                        <div class="d-flex flex-wrap select--all-checkes">
                            <h5 class="input-label m-0 text-capitalize">{{translate('messages.module_permission')}} : </h5>
                            <div class="check-item pb-0 w-auto">
                                <div class="form-group form-check form--check m-0 ml-2">
                                    <input type="checkbox" name="modules[]" value="account" class="form-check-input" id="select-all">
                                    <label class="form-check-label ml-2" for="select-all">Select All</label>
                                </div>
                            </div>
                        </div>
                        <div class="check--item-wrapper">
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="account" class="form-check-input"
                                           id="account">
                                    <label class="form-check-label qcont text-dark" for="account">{{translate('messages.collect')}} {{translate('messages.Cash')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="addon" class="form-check-input"
                                           id="addon">
                                    <label class="form-check-label qcont text-dark" for="addon">{{translate('messages.addons')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="attribute" class="form-check-input"
                                           id="attribute">
                                    <label class="form-check-label qcont text-dark" for="attribute">{{translate('messages.attributes')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="banner" class="form-check-input"
                                           id="banner">
                                    <label class="form-check-label qcont text-dark" for="banner">{{translate('messages.banners')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="campaign" class="form-check-input"
                                           id="campaign">
                                    <label class="form-check-label qcont text-dark" for="campaign">{{translate('messages.campaigns')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="category" class="form-check-input"
                                           id="category">
                                    <label class="form-check-label qcont text-dark" for="category">{{translate('messages.categories')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="coupon" class="form-check-input"
                                           id="coupon">
                                    <label class="form-check-label qcont text-dark" for="coupon">{{translate('messages.coupons')}}</label>
                                </div>
                            </div>
                            {{-- <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="custom_role" class="form-check-input"
                                           id="custom_role">
                                    <label class="form-check-label qcont text-dark" for="custom_role">{{translate('messages.custom_role')}}</label>
                                </div>
                            </div> --}}
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="customerList" class="form-check-input"
                                           id="customerList">
                                    <label class="form-check-label qcont text-dark" for="customerList">{{translate('messages.customers')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="deliveryman" class="form-check-input"
                                           id="deliveryman">
                                    <label class="form-check-label qcont text-dark" for="deliveryman">{{translate('messages.deliveryman')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="provide_dm_earning" class="form-check-input"
                                           id="provide_dm_earning">
                                    <label class="form-check-label qcont text-dark" for="provide_dm_earning">{{translate('messages.deliverymen_earning_provide')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="employee" class="form-check-input"
                                           id="employee">
                                    <label class="form-check-label qcont text-dark" for="employee">{{translate('messages.Employees')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="item" class="form-check-input"
                                           id="item">
                                    <label class="form-check-label qcont text-dark" for="item">{{translate('messages.items')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="notification" class="form-check-input"
                                           id="notification">
                                    <label class="form-check-label qcont text-dark" for="notification">{{translate('messages.notification_settings')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="order" class="form-check-input"
                                           id="order">
                                    <label class="form-check-label qcont text-dark" for="order">{{translate('messages.orders')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="store" class="form-check-input"
                                           id="store">
                                    <label class="form-check-label qcont text-dark" for="store">{{translate('messages.stores')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="report" class="form-check-input"
                                            id="report">
                                    <label class="form-check-label qcont text-dark" for="report">{{translate('messages.reports')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="settings" class="form-check-input"
                                           id="settings">
                                    <label class="form-check-label qcont text-dark" for="settings">{{translate('messages.business')}} {{translate('messages.settings')}}</label>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="withdraw_list" class="form-check-input"
                                            id="withdraw_list">
                                    <label class="form-check-label qcont text-dark" for="withdraw_list">{{translate('messages.store')}} {{translate('messages.withdraws')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="zone" class="form-check-input"
                                           id="zone">
                                    <label class="form-check-label qcont text-dark" for="zone">{{translate('messages.zone_setup')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="module" class="form-check-input"
                                           id="module_system">
                                    <label class="form-check-label qcont text-dark" for="module_system">{{translate('messages.system_module_setup')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="parcel" class="form-check-input"
                                           id="parcel">
                                    <label class="form-check-label qcont text-dark" for="parcel">{{translate('messages.parcel')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="pos" class="form-check-input"
                                           id="pos">
                                    <label class="form-check-label qcont text-dark" for="pos">{{translate('messages.pos_system')}}</label>
                                </div>
                            </div>
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="modules[]" value="unit" class="form-check-input"
                                           id="unit">
                                    <label class="form-check-label qcont text-dark" for="unit">{{translate('messages.units')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-4">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-0 py-2">
                    <div class="search--button-wrapper">
                        <h5 class="card-title">
                            {{translate('messages.roles_table')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$rl->total()}}</span>
                        </h5>
                        <form action="javascript:" id="search-form" class="search-form min--200">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('ex_:_search_role_name')}}" aria-label="Search">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>                    <!-- Unfold -->
                    {{-- <div class="hs-unfold mr-2">
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
                            <a id="export-excel" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                            <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/pdf.svg"
                                    alt="Image Description">
                                {{ translate('messages.pdf') }}
                            </a>
                        </div>
                    </div> --}}
                    <!-- End Unfold -->
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="role--table table table-borderless table-thead-bordered table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" class="border-0">{{translate('sl')}}</th>
                                <th scope="col" class="border-0">{{translate('messages.role_name')}}</th>
                                <th scope="col" class="border-0">{{translate('messages.modules')}}</th>
                                <th scope="col" class="border-0">{{translate('messages.created_at')}}</th>
                                {{--<th scope="col" class="border-0">{{translate('messages.status')}}</th>--}}
                                <th scope="col" class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody  id="set-rows">
                            @foreach($rl as $k=>$r)
                                <tr>
                                    <td scope="row">{{$k+$rl->firstItem()}}</td>
                                    <td>{{Str::limit($r['name'],25,'...')}}</td>
                                    <td class="text-capitalize">
                                        @if($r['modules']!=null)
                                            @foreach((array)json_decode($r['modules']) as $key=>$m)
                                               {{str_replace('_',' ',$m)}}
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <div class="create-date">
                                            {{date('d-M-y',strtotime($r['created_at']))}}
                                        </div>
                                    </td>
                                    {{--<td>
                                        {{$r->status?'Active':'Inactive'}}
                                    </td>--}}
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary"
                                                href="{{route('admin.users.custom-role.edit',[$r['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.role')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('role-{{$r['id']}}','{{translate('messages.Want_to_delete_this_role')}}')" title="{{translate('messages.delete')}} {{translate('messages.role')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.users.custom-role.delete',[$r['id']])}}"
                                                method="post" id="role-{{$r['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($rl) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $rl->links() !!}
                        </div>
                        @if(count($rl) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.users.custom-role.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
        $(document).ready(function() {
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));
        });
    </script>

    <script>
        $('#select-all').on('change', function(){
            if(this.checked === true) {
                $('.check--item-wrapper .check-item .form-check-input').attr('checked', true)
            } else {
                $('.check--item-wrapper .check-item .form-check-input').attr('checked', false)
            }
        })
    </script>

@endpush


@extends('layouts.admin.app')

@section('title',translate('messages.account_transaction'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Heading -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/collect-cash.png')}}" class="w--22" alt="">
            </span>
            <span>
                {{translate('messages.collect_cash')}} {{translate('messages.transaction')}}
            </span>
        </h1>
    </div>
    <!-- Page Heading -->
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.transactions.account-transaction.store')}}" method='post' id="add_transaction">
                @csrf
                <div class="row g-3">
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                        <label class="form-label" for="type">{{translate('messages.collect_from')}}<span class="input-label-secondary"></span></label>
                            <select name="type" id="type" class="form-control">
                                <option value="deliveryman">{{translate('messages.deliveryman')}}</option>
                                <option value="store">{{translate('messages.store')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="store">{{translate('messages.store')}}<span class="input-label-secondary"></span></label>
                            <select id="store" name="store_id" data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" onchange="getAccountData('{{url('/')}}/admin/store/get-account-data/',this.value,'store')" class="form-control" title="Select Restaurant" disabled>

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="deliveryman">{{translate('messages.deliveryman')}}<span class="input-label-secondary"></span></label>
                            <select id="deliveryman" name="deliveryman_id" data-placeholder="{{translate('messages.select')}} {{translate('messages.deliveryman')}}" onchange="getAccountData('{{url('/')}}/admin/delivery-man/get-account-data/',this.value,'deliveryman')" class="form-control" title="Select deliveryman">

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="method">{{translate('messages.payment_method')}}<span class="input-label-secondary"></span></label>
                            <input class="form-control" type="text" name="method" id="method" required maxlength="191" placeholder="{{translate('messages.Ex_:_Card')}}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="ref">{{translate('messages.reference')}}<span class="input-label-secondary"></span></label>
                            <input  class="form-control" type="text" name="ref" id="ref" maxlength="191">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="amount">{{translate('messages.amount')}} {{ \App\CentralLogics\Helpers::currency_symbol() }}<span class="input-label-secondary" id="account_info"></span></label>
                            <input class="form-control" type="number" min=".01" step="0.01" name="amount" id="amount" max="999999999999.99" placeholder="{{translate('messages.Ex_:_1000')}}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn--reset" type="reset" id="reset_btn">{{translate('messages.reset')}}</button>

                            <button class="btn btn--primary" type="submit">{{translate('messages.collect_cash')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-2 border-0">
                    <div class="search--button-wrapper">
                        <h5 class="card-title">
                            <span>
                                {{ translate('messages.transaction')}} {{ translate('messages.history')}}
                            </span>
                            <span class="badge badge-soft-secondary" id="itemCount">
                                ({{ $account_transaction->total() }})
                            </span>
                        </h5>

                        <form action="javascript:" id="search-form" class="search-form">
                        @csrf
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch" name="search" type="search" class="form-control h--40px" placeholder="{{translate('Search By Referance  or Name')}}" aria-label="{{translate('messages.search_here')}}">
                                <button type="submit" class="btn btn--secondary h--40px"><i class="tio-search"></i></button>
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
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.account-transaction.export', ['type'=>'excel'])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.account-transaction.export', ['type'=>'csv'])}}">
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
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{translate('sl')}}</th>
                                    <th class="border-0">{{ translate('messages.collect_from') }}</th>
                                    <th class="border-0">{{ translate('messages.type') }}</th>
                                    <th class="border-0">{{translate('messages.received_at')}}</th>
                                    <th class="border-0">{{translate('messages.amount')}}</th>
                                    <th class="border-0">{{translate('messages.reference')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($account_transaction as $k=>$at)
                                <tr>
                                    <td scope="row">{{$k+$account_transaction->firstItem()}}</td>
                                    <td>
                                        @if($at->store)
                                        <a href="{{route('admin.store.view',[$at->store['id'],'module_id'=>$at->store['module_id']])}}">{{ Str::limit($at->store->name, 20, '...') }}</a>
                                        @elseif($at->deliveryman)
                                        <a href="{{route('admin.users.delivery-man.preview',[$at->deliveryman->id])}}">{{ $at->deliveryman->f_name }} {{ $at->deliveryman->l_name }}</a>
                                        @else
                                            {{translate('messages.not_found')}}
                                        @endif
                                    </td>
                                    <td><label class="text-uppercase">{{$at['from_type']}}</label></td>
                                    <td>{{$at->created_at->format('Y-m-d '.config('timeformat'))}}</td>
                                    <td><div class="pl-4">
                                        {{$at['amount']}}
                                    </div></td>
                                    <td><div class="pl-4">
                                        {{$at['ref']}}
                                    </div></td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a href="{{route('admin.transactions.account-transaction.view',[$at['id']])}}"
                                            class="btn action-btn btn--warning btn-outline-warning"><i class="tio-visible"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($account_transaction) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $account_transaction->links() !!}
                </div>
                @if(count($account_transaction) === 0)
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
@endsection

@push('script_2')
<script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });

        $('#type').on('change', function() {
            if($('#type').val() == 'store')
            {
                $('#store').removeAttr("disabled");
                $('#deliveryman').val("").trigger( "change" );
                $('#deliveryman').attr("disabled","true");
            }
            else if($('#type').val() == 'deliveryman')
            {
                $('#deliveryman').removeAttr("disabled");
                $('#store').val("").trigger( "change" );
                $('#store').attr("disabled","true");
            }
        });
    });
    $('#store').select2({
        ajax: {
            url: '{{url('/')}}/admin/store/get-stores',
            data: function (params) {
                return {
                    q: params.term, // search term
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

    $('#deliveryman').select2({
        ajax: {
            url: '{{url('/')}}/admin/delivery-man/get-deliverymen',
            data: function (params) {
                return {
                    q: params.term, // search term
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

    function getAccountData(route, data_id, type)
    {
        $.get({
                url: route+data_id,
                dataType: 'json',
                success: function (data) {
                    $('#account_info').html('({{translate('messages.cash_in_hand')}}: '+data.cash_in_hand+' {{translate('messages.total_earning')}}: '+data.earning_balance+')');
                },
            });
    }
</script>
<script>
    $('#add_transaction').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{route('admin.transactions.account-transaction.store')}}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    toastr.success('{{translate('messages.transaction_saved')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    setTimeout(function () {
                        location.href = '{{route('admin.transactions.account-transaction.index')}}';
                    }, 2000);
                }
            }
        });
    });

    $('#reset_btn').click(function(){
            $('#store').val(null).trigger('change');
            $('#deliveryman').val(null).trigger('change');
        })

        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.account-transaction.search')}}',
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

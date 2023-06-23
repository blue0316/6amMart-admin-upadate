@extends('layouts.admin.app')

@section('title',translate('messages.deliverymen_earning_provide'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Heading -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/report.png')}}" class="w--22" alt="">
            </span>
            <span>
                {{translate('messages.provide')}} {{translate('messages.deliverymen')}} {{translate('messages.earning')}}
            </span>
        </h1>
    </div>
    <!-- Page Heading -->
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.transactions.provide-deliveryman-earnings.store')}}" method='post' id="add_transaction">
                @csrf
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="deliveryman">{{translate('messages.deliveryman')}}<span class="input-label-secondary"></span></label>
                            <select id="deliveryman" name="deliveryman_id" data-placeholder="{{translate('messages.select')}} {{translate('messages.deliveryman')}}" onchange="getAccountData('{{url('/')}}/admin/delivery-man/get-account-data/',this.value,'deliveryman')" class="form-control" title="Select deliveryman">

                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="amount">{{translate('messages.amount')}}<span class="input-label-secondary" id="account_info"></span></label>
                            <input class="form-control" type="number" min="1" step="0.01" name="amount" id="amount" max="999999999999.99" placeholder="{{translate('ex_100')}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="method">{{translate('messages.method')}}<span class="input-label-secondary"></span></label>
                            <input class="form-control" type="text" name="method" id="method" required maxlength="191" placeholder="{{translate('ex_cash')}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="ref">{{translate('messages.reference')}}<span class="input-label-secondary"></span></label>
                            <input  class="form-control" type="text" name="ref" id="ref" maxlength="191" placeholder="{{translate('ex_collect_cash')}}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn--reset" type="reset" id="reset_btn">{{translate('messages.reset')}}</button>
                            <button class="btn btn--primary" type="submit">{{translate('messages.save')}}</button>
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
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                {{ translate('messages.deliverymen_earning_provide')}} {{ translate('messages.table')}}
                            </span>
                            <span class="badge badge-soft-secondary" id="itemCount">
                                ({{ $provide_dm_earning->total() }})
                            </span>
                        </h5>

                        <form action="javascript:" id="search-form" class="search-form">
                        @csrf
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch" name="search" type="search" class="form-control h--40px" placeholder="{{translate('ex_:_search_delivery_man')}}" aria-label="{{translate('messages.search_here')}}">
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
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.export-deliveryman-earning', ['type'=>'excel'])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.export-deliveryman-earning', ['type'=>'csv'])}}">
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
                                    <th class="border-0">{{translate('messages.name')}}</th>
                                    <th class="border-0">{{translate('messages.received_at')}}</th>
                                    <th class="border-0">{{translate('messages.amount')}}</th>
                                    <th class="border-0">{{translate('messages.method')}}</th>
                                    <th class="border-0">{{translate('messages.reference')}}</th>
                                </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($provide_dm_earning as $k=>$at)
                                <tr>
                                    <td scope="row">{{$k+$provide_dm_earning->firstItem()}}</td>
                                    <td>@if($at->delivery_man)<a href="{{route('admin.users.delivery-man.preview', $at->delivery_man_id)}}">{{$at->delivery_man->f_name.' '.$at->delivery_man->l_name}}</a> @else <label class="text-capitalize text-danger">{{translate('messages.deliveryman')}} {{translate('messages.deleted')}}</label> @endif </td>
                                    <td>{{$at->created_at->format('Y-m-d '.config('timeformat'))}}</td>
                                    <td>{{$at['amount']}}</td>
                                    <td>{{$at['method']}}</td>
                                    <td>{{$at['ref']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($provide_dm_earning) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $provide_dm_earning->links() !!}
                </div>
                @if(count($provide_dm_earning) === 0)
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
                $('#restaurant').removeAttr("disabled");
                $('#deliveryman').val("").trigger( "change" );
                $('#deliveryman').attr("disabled","true");
            }
            else if($('#type').val() == 'deliveryman')
            {
                $('#deliveryman').removeAttr("disabled");
                $('#restaurant').val("").trigger( "change" );
                $('#restaurant').attr("disabled","true");
            }
        });
    });

    $('#deliveryman').select2({
        ajax: {
            url: '{{url('/')}}/admin/delivery-man/get-deliverymen',
            data: function (params) {
                return {
                    q: params.term, // search term
                    earning: true,
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
                    $('#account_info').html('({{translate('messages.cash_in_hand')}}: '+data.cash_in_hand+' {{translate('messages.earning_balance')}}: '+data.earning_balance+')');
                },
            });
    }
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
            url: '{{route('admin.transactions.search-deliveryman-earning')}}',
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

    $('#add_transaction').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{route('admin.transactions.provide-deliveryman-earnings.store')}}',
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
                        location.href = '{{route('admin.transactions.provide-deliveryman-earnings.index')}}';
                    }, 2000);
                }
            }
        });
    });


    $('#reset_btn').click(function(){
            $('#deliveryman').val(null).trigger('change');
        })
</script>
@endpush

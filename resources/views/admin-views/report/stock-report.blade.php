@extends('layouts.admin.app')

@section('title',translate('stock_report'))

@section('content')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/report.png')}}" class="w--22" alt="">
            </span>
            <span>
                {{translate('stock_report')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
    <!-- Card -->
    <div class="card mt-3">
        <!-- Header -->
        <div class="card-header border-0 py-2">
            <div class="search--button-wrapper justify-content-end">
                <form action="javascript:" id="search-form" class="search-form">
                    <!-- Search -->
                    <div class="input-group input--group">
                        <input id="datatableSearch" name="search" type="search" class="form-control" placeholder="{{translate('ex_:_search_name')}}" aria-label="{{translate('messages.search_here')}}" value="{{request()->query('search')}}">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                    <!-- End Search -->
                </form>
                {{-- <div class="min--200 ml-auto">
                    <select name="module_id" class="form-control js-select2-custom" onchange="set_filter('{{url()->full()}}',this.value,'module_id')" title="{{translate('messages.select')}} {{translate('messages.modules')}}">
                        <option value="" {{!request('module_id') ? 'selected':''}}>{{translate('messages.all')}} {{translate('messages.modules')}}</option>
                        @foreach (\App\Models\Module::notParcel()->get() as $module)
                        @if (config('module.'.$module->module_type)['stock'])
                        <option value="{{$module->id}}" {{request('module_id') == $module->id?'selected':''}}>
                            {{$module['module_name']}}
                        </option>
                        @endif

                        @endforeach
                    </select>
                </div> --}}
                <div class="min--200">
                    <select name="zone_id" class="form-control js-select2-custom" onchange="set_zone_filter('{{url()->full()}}',this.value)" id="zone">
                        <option value="all">{{translate('All Zones')}}</option>
                        @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                        <option value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                            {{($z['name'])}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="min--200">
                    <select name="store_id" onchange="set_store_filter('{{url()->full()}}',this.value)" data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" class="js-data-example-ajax form-control">
                        @if(isset($store))
                        <option value="{{$store->id}}" selected>{{$store->name}}</option>
                        @else
                        <option value="all" selected>{{translate('messages.all')}} {{translate('messages.stores')}}</option>
                        @endif
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
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.report.stock-wise-report-export', ['type'=>'excel',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.report.stock-wise-report-export', ['type'=>'csv',request()->getQueryString()])}}">
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

        <!-- Table -->
        <div class="table-responsive datatable-custom" id="table-div">
            <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table" data-hs-datatables-options='{
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
                        <th class="border-0">{{translate('sl')}}</th>
                        <th class="border-0 w--2">{{translate('messages.name')}}</th>
                        <th class="border-0 w--2">{{translate('messages.store')}}</th>
                        <th class="border-0">{{translate('messages.zone')}}</th>
                        <th class="border-0">{{translate('Current stock')}}</th>
                        <th class="border-0">{{translate('messages.action')}}</th>
                    </tr>
                </thead>

                <tbody id="set-rows">

                    @foreach($items as $key=>$item)
                    <tr>
                        <td>{{$key+$items->firstItem()}}</td>
                        <td>
                            <a class="media align-items-center" href="{{route('admin.item.view',[$item['id'],'module_id'=>$item['module_id']])}}">
                                <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$item['image']}}" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->name}} image">
                                <div class="media-body">
                                    <h5 class="text-hover-primary mb-0 max-width-200px word-break line--limit-2">{{$item['name']}}</h5>
                                </div>
                            </a>
                        </td>
                        <td>
                            @if($item->store)
                            {{Str::limit($item->store->name,25,'...')}}
                            @else
                            {{translate('messages.store')}} {{translate('messages.deleted')}}
                            @endif
                        </td>
                        <td>
                            @if($item->store)
                            {{$item->store->zone->name}}
                            @else
                            {{translate('messages.not_found')}}
                            @endif
                        </td>
                        <td>
                            {{$item->stock}}
                        </td>
                        <td>
                            <a class="btn action-btn btn--primary btn-outline-primary" href="javascript:" title="{{translate('messages.edit')}} {{translate('messages.quantity')}}" onclick="update_quantity({{ $item->id }})" data-toggle="modal" data-target="#update-quantity"><i class="tio-edit"></i>
                            </a>
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

<div class="modal fade" id="update-quantity" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body py-2">
                <form action="{{route('admin.item.stock-update')}}" method="post">
                    @csrf
                    <div class="mt-2 rest-part w-100"></div>
                    <div class="btn--container justify-content-end">
                        <button type="button" class="btn btn--danger" data-dismiss="modal" aria-label="Close">
                            {{translate('messages.close')}}
                        </button>
                        <button class="btn btn--primary" type="submit">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    function update_quantity(val) {

        $.get({
            url: '{{url('/')}}/admin/item/get-variations?id='+val,
            dataType: 'json',
            success: function (data) {

                $('.rest-part').empty().html(data.view);
            },
        });
    }

    function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {

                $('input[name="current_stock"]').attr("readonly", 'readonly');
                $('input[name="current_stock"]').val(total_qty);
            }
            else{
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }

</script>

@endpush

@push('script_2')

<script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
<script src="{{asset('public/assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/hs.chartjs-matrix.js"></script>

<script>
    $(document).on('ready', function() {
        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        // all:true,
                        @if(isset($zone))
                            zone_ids: [{{$zone->id}}],
                        @endif
                        @if(request('module_id'))
                        module_id: {{request('module_id')}}
                        ,
                        @endif
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
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.report.stock-search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
</script>


@endpush

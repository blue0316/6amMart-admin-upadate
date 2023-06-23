@extends('layouts.admin.app')

@section('title',translate('messages.new_joining_requests'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-filter-list"></i> {{translate('messages.new_joining_requests')}}</h1>
            <div class="page-header-select-wrapper">

                {{-- <div class="select-item">
                    <select name="module_id" class="form-control js-select2-custom"
                            onchange="set_filter('{{ url()->full() }}',this.value,'module_id')" title="{{translate('messages.select')}} {{translate('messages.modules')}}">
                        <option value="" {{!request('module_id') ? 'selected':''}}>{{translate('messages.all')}} {{translate('messages.modules')}}</option>
                        @foreach (\App\Models\Module::notParcel()->get() as $module)
                            <option
                                value="{{$module->id}}" {{request('module_id') == $module->id?'selected':''}}>
                                {{$module['module_name']}}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                @if(!isset(auth('admin')->user()->zone_id))
                <div class="select-item">
                    <select name="zone_id" class="form-control js-select2-custom"
                            onchange="set_filter('{{ url()->full() }}',this.value,'zone_id')">
                        <option value="" {{!request('zone_id')?'selected':''}}>{{ translate('messages.All Zones') }}</option>
                        @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                            <option
                                value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                                {{$z['name']}}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
                        <!-- Nav -->
                        <ul class="nav nav-tabs mb-3 border-0 nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.store.pending-requests') }}"   aria-disabled="true">{{translate('messages.pending_stores')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.store.deny-requests') }}"  aria-disabled="true">{{translate('messages.denied_stores')}}</a>
                            </li>
                        </ul>
                        <!-- End Nav -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{translate('messages.stores')}} {{translate('messages.list')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$stores->total()}}</span></h5>
                    <form action="javascript:" id="search-form" class="search-form">
                    <!-- Search -->
                        @csrf
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" id="search" name="search" class="form-control"
                                    placeholder="{{translate('ex_:_Search_Store_Name')}}" value="{{isset($search_by) ? $search_by : ''}}"aria-label="{{translate('messages.search')}}" required>
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                    </form>
                    <!-- End Search -->
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false

                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('sl')}}</th>
                        <th class="border-0">{{translate('messages.store_information')}}</th>
                        <th class="border-0">{{translate('messages.module')}}</th>
                        <th class="border-0">{{translate('messages.owner_information')}}</th>
                        <th class="border-0">{{translate('messages.zone')}}</th>
                        <th class="text-uppercase border-0">{{translate('messages.status')}}</th>
                        <th class="border-0">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($stores as $key=>$store)
                        <tr>
                            <td>{{$key+$stores->firstItem()}}</td>
                            <td>
                                <div>
                                    <a href="{{route('admin.store.view', $store->id)}}" class="table-rest-info" alt="view store">
                                    <img class="img--60 circle" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                            src="{{asset('storage/app/public/store')}}/{{$store['logo']}}">
                                        <div class="info"><div class="text--title">
                                            {{Str::limit($store->name,20,'...')}}
                                            </div>
                                            <div class="font-light">
                                                {{translate('messages.id')}}:{{$store->id}}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($store->module->module_name,20,'...')}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($store->vendor->f_name.' '.$store->vendor->l_name,20,'...')}}
                                </span>
                                <div>
                                    {{$store['phone']}}
                                </div>
                            </td>
                            <td>
                                {{$store->zone?$store->zone->name:translate('messages.zone').' '.translate('messages.deleted')}}
                            </td>

                            <td>
                                @if(isset($store->vendor->status))
                                    @if($store->vendor->status)
                                    <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$store->id}}">
                                        <input type="checkbox" onclick="status_change_alert('{{route('admin.store.status',[$store->id,$store->status?0:1])}}', '{{translate('messages.you_want_to_change_this_store_status')}}', event)" class="toggle-switch-input" id="stocksCheckbox{{$store->id}}" {{$store->status?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                    @else
                                    <span class="badge badge-soft-danger">{{translate('messages.denied')}}</span>
                                    @endif
                                @else
                                    <span class="badge badge-soft-danger">{{translate('messages.pending')}}</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn--container">
                                    @if($store->vendor->status == 0)
                                        <a class="btn action-btn btn--primary btn-outline-primary float-right mr-2" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ translate('messages.approve') }}"
                                        onclick="request_alert('{{route('admin.store.application',[$store['id'],1])}}','{{translate('messages.you_want_to_approve_this_application')}}')"
                                            href="javascript:"><i class="tio-done font-weight-bold"></i></a>
                                    @endif
                                    @if (!isset($store->vendor->status))
                                        <a class="btn action-btn btn--danger btn-outline-danger float-right" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ translate('messages.deny') }}"
                                        onclick="request_alert('{{route('admin.store.application',[$store['id'],0])}}','{{translate('messages.you_want_to_deny_this_application')}}')"
                                            href="javascript:"><i class="tio-clear font-weight-bold"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($stores) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $stores->withQueryString()->links() !!}
                </div>
                @if(count($stores) === 0)
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

@push('script_2')
    <script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href=url;
                }
            })
        }
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

            $('#column3_search').on('keyup', function () {
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

    <script>
        function request_alert(url, message) {
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }

        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            set_filter('{!! url()->full() !!}',formData.get('search'),'search_by')
        });
    </script>
@endpush

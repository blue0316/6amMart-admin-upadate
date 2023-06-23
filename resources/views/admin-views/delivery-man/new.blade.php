@extends('layouts.admin.app')

@section('title',translate('new_joining_requests'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-filter-list"></i> {{translate('new_joining_requests')}}</h1>
            <div class="page-header-select-wrapper">
                @if(!isset(auth('admin')->user()->zone_id))
                <div class="col-sm-auto min--240">
                    <select name="zone_id" class="form-control js-select2-custom"
                            onchange="set_zone_filter('{{ url()->full() }}', this.value)">
                        <option value="all">{{ translate('messages.All Zones') }}</option>
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
                                <a class="nav-link active" href="{{ route('admin.users.delivery-man.new') }}"   aria-disabled="true">{{translate('messages.pending_delivery_man')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.delivery-man.deny') }}"  aria-disabled="true">{{translate('messages.denied_deliveryman')}}</a>
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
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">
                        {{translate('messages.deliveryman')}} {{translate('messages.list')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$delivery_men->total()}}</span>
                    </h5>
                    <form action="javascript:" id="search-form" class="search-form">
                        <!-- Search -->
                            @csrf
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" id="search" name="search" class="form-control"
                                        placeholder="{{translate('ex_: search_delivery_man')}}" value="{{isset($search_by) ? $search_by : ''}}" aria-label="{{translate('messages.search')}}" required>
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>
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
                        <th class="border-0 text-capitalize">{{translate('sl')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.name')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.contact_info')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.zone')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.total_orders')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.availability')}} {{translate('messages.status')}}</th>
                        <th class="border-0 text-center text-capitalize">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($delivery_men as $key=>$dm)
                        <tr>
                            <td>{{$key+$delivery_men->firstItem()}}</td>
                            <td>
                                <a class="table-rest-info" href="{{route('admin.users.delivery-man.preview',[$dm['id']])}}">
                                    <img onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                            src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}" alt="{{$dm['f_name']}} {{$dm['l_name']}}">
                                    <div class="info">
                                        <h5 class="text-hover-primary mb-0">{{$dm['f_name'].' '.$dm['l_name']}}</h5>
                                        <span class="d-block text-body">
                                            <span class="rating">
                                            <i class="tio-star"></i> {{count($dm->rating)>0?number_format($dm->rating[0]->average, 1, '.', ' '):0}}
                                            </span>
                                        </span>
                                    </div>
                                </a>
                            </td>
                            <td>
                                <a class="deco-none" href="tel:{{$dm['phone']}}">{{$dm['phone']}}</a>
                            </td>
                            <td>
                                @if($dm->zone)
                                <label class="text--title font-medium mb-0">{{$dm->zone->name}}</label>
                                @else
                                <label class="text--title font-medium mb-0">{{translate('messages.zone').' '.translate('messages.deleted')}}</label>
                                @endif
                            </td>
                            <td>
                                <a class="deco-none" href="tel:{{$dm['phone']}}">{{count($dm['orders'])}}</a>
                            </td>
                            <td>
                                <div>
                                    {{translate('messages.currently_assigned_orders')}} : {{$dm->current_orders}}
                                </div>
                                <div>
                                    {{translate('messages.active_status')}} :
                                    @if($dm->application_status == 'approved')
                                        @if($dm->active)
                                        <strong class="text-capitalize text-primary">{{translate('messages.online')}}</strong>
                                        @else
                                        <strong class="text-capitalize text-secondary">{{translate('messages.offline')}}</strong>
                                        @endif
                                    @elseif ($dm->application_status == 'denied')
                                        <strong class="text-capitalize text-danger">{{translate('messages.denied')}}</strong>
                                    @else
                                        <strong class="text-capitalize text-info">{{translate('messages.pending')}}</strong>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($dm->application_status == 'approved')

                                @else
                                <div class="col-md-12">
                                    <div class="btn--container justify-content-end">
                                        <a class="btn action-btn btn--primary btn-outline-primary" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ translate('messages.approve') }}"
                                        onclick="request_alert('{{route('admin.users.delivery-man.application',[$dm['id'],'approved'])}}','{{translate('messages.you_want_to_approve_this_application')}}')"
                                            href="javascript:"><i class="tio-done font-weight-bold"></i> </a>
                                        @if($dm->application_status !='denied')
                                        <a class="btn action-btn btn--danger btn-outline-danger " data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ translate('messages.deny') }}"
                                        onclick="request_alert('{{route('admin.users.delivery-man.application',[$dm['id'],'denied'])}}','{{translate('messages.you_want_to_deny_this_application')}}')"
                                            href="javascript:"><i class="tio-clear font-weight-bold"></i></a>
                                        @endif
                                    </div>
                                </div>
                
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($delivery_men) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $delivery_men->links() !!}
                </div>
                @if(count($delivery_men) === 0)
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

@extends('layouts.admin.app')

@section('title',translate('Customer Details'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-print-none pb-3">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title mb-0">{{translate('messages.customer')}} {{translate('messages.id')}} #{{$customer['id']}}</h1>
                    <span>
                        <i class="tio-date-range"></i>
                        {{translate('messages.joined_at')}} : {{date('d M Y '.config('timeformat'),strtotime($customer['created_at']))}}
                    </span>

                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                       href="{{route('admin.users.customer.view',[$customer['id']-1])}}"
                       data-toggle="tooltip" data-placement="top" title="Previous customer">
                        <i class="tio-arrow-backward"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                       href="{{route('admin.users.customer.view',[$customer['id']+1])}}" data-toggle="tooltip"
                       data-placement="top" title="Next customer">
                        <i class="tio-arrow-forward"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row mb-2 g-2">
            <!-- Collected Cash Card Example -->
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card card--bg-1">
                    <img class="resturant-icon" src="{{asset('public/assets/admin/img/customer-loyality/1.png')}}" alt="public">
                    <div class="title text-capitalize">{{$customer->wallet_balance??0}}</div>
                    <div class="subtitle">{{__('messages.wallet')}} {{__('messages.balance')}}</div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card card--bg-2">
                    <img class="resturant-icon" src="{{asset('public/assets/admin/img/customer-loyality/2.png')}}" alt="public">
                    <div class="title text-capitalize">{{$customer->loyalty_point??0}}</div>
                    <div class="subtitle    ">{{__('messages.loyalty_point_balance')}}</div>
                </div>
            </div>
        </div>

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card">
                    <div class="card-header border-0 py-2">
                        <div class="search--button-wrapper">
                            <h5 class="card-title"> {{translate('order_list')}} <span class="badge badge-soft-secondary">{{ $orders->total() }}</span></h5>
                            <div class="min--260">
                                <div class="input--group input-group">
                                    <input type="text" id="column1_search" class="form-control form-control-sm" placeholder="{{translate('ex_:_search_ID')}}">
                                    <button type="button" class="btn btn--secondary">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <th class="border-0 pl-4">{{translate('sl')}}</th>
                                <th class="border-0 text-center">{{translate('messages.order')}} {{translate('messages.id')}}</th>
                                <th class="border-0 text-center">{{translate('messages.total_amount')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>
                                        <div class="pl-2">
                                            {{$key+$orders->firstItem()}}
                                        </div>
                                    </td>
                                    <td class="table-column-pl-0 text-center">
                                        <a href="{{route((isset($order) && $order->order_type=='parcel')?'admin.parcel.order.details':'admin.order.details',['id'=>$order['id'],'module_id'=>$order['module_id']])}}">{{$order['id']}}</a>
                                    </td>
                                    <td>
                                        <div class="text-right mw--85px mx-auto">
                                            {{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--warning btn-outline-warning" href="{{route((isset($order) && $order->order_type=='parcel')?'admin.parcel.order.details':'admin.order.details',['id'=>$order['id']])}}" title="{{translate('messages.view')}} "><i class="tio-visible"></i></a>
                                            <a class="btn action-btn btn--primary btn-outline-primary" target="_blank" href="{{route('admin.order.generate-invoice',[$order['id']])}}" title="{{translate('messages.invoice')}}"><i class="tio-print"></i> </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($orders) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $orders->links() !!}
                        </div>
                        @if(count($orders) === 0)
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

            <div class="col-lg-4">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>{{$customer['f_name'].' '.$customer['l_name']}}</span>
                        </h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if($customer)
                        <div class="card-body">
                            <div class="customer--information-single media align-items-center" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/profile/'.$customer->image)}}" alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <ul class="list-unstyled m-0">
                                        <li class="pb-1 d-flex align-items-center">
                                            <i class="tio-email mr-2"></i>
                                            <span>{{$customer['email']}}</span>
                                        </li>
                                        <li class="pb-1 d-flex align-items-center">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <span>{{$customer['phone']}}</span>
                                        </li>
                                        <li class="pb-1 d-flex align-items-center">
                                            <i class="tio-shopping-basket-outlined mr-2"></i>
                                            <span>{{$customer->order_count}} {{translate('messages.orders')}}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <hr>


                            @foreach($customer->addresses as $address)
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{translate('messages.addresses')}}</h5>
                                </div>
                                <ul class="list-unstyled list-unstyled-py-2">
                                    <li class="d-flex align-items-center">
                                        <i class="tio-tab mr-2"></i>
                                        <span>{{$address['address_type']}}</span>
                                    </li>
                                    @if($address['contact_person_umber'])
                                    <li class="d-flex align-items-center">
                                        <i class="tio-android-phone-vs mr-2"></i>
                                        <span>{{$address['contact_person_number']}}</span>
                                    </li>
                                    @endif
                                    <li>
                                        <a target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}" class="d-flex align-items-center">
                                            <i class="tio-poi mr-2"></i>
                                            {{$address['address']}}
                                        </a>
                                    </li>
                                </ul>
                                <hr>
                            @endforeach

                        </div>
                @endif
                <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Row -->
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


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
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

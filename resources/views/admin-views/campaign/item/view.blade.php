@extends('layouts.admin.app')

@section('title','Item Campaign Preview')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{asset('public/assets/admin/img/product.png')}}" class="w--26" alt="">
                    </span>
                    <span>
                        {{$campaign['title']}}
                    </span>
                </h1>
                    <a class="btn btn--primary" href="{{route('admin.campaign.edit',['item',$campaign['id']])}}">
                        <i class="tio-edit"></i> {{translate('messages.edit')}}
                    </a>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="card mb-3">
            <!-- Body -->
            <div class="card-body">
                <div class="row align-items-md-center">
                    <div class="col-md-6 col-lg-4 mb-3 mb-md-0">
                            <img class="rounded img--ratio-3" src="{{asset('storage/app/public/campaign')}}/{{$campaign['image']}}" onerror="this.src='{{asset('/public/assets/admin/img/900x400/img1.jpg')}}'" alt="Image Description">
                    </div>
                    <div class="col-md-6">
                        <span class="d-block mb-1">
                            {{translate('messages.campaign')}} {{translate('messages.starts')}} {{translate('messages.from')}} :
                            <strong class="text--title">{{$campaign->start_date->format('Y-M-d')}}</strong>
                        </span>
                        <span class="d-block mb-1">
                            {{translate('messages.campaign')}} {{translate('messages.ends')}} {{translate('messages.at')}} :
                            <strong class="text--title">{{$campaign->end_date->format('Y-M-d')}}</strong>
                        </span>
                        <span class="d-block mb-1">
                            {{translate('messages.available')}} {{translate('messages.time')}} {{translate('messages.starts')}} :
                            <strong class="text--title">{{$campaign->start_time->format(config('timeformat'))}}</strong>
                        </span>
                        <span class="d-block">
                            {{translate('messages.available')}} {{translate('messages.time')}} {{translate('messages.ends')}} :
                            <strong class="text--title">{{$campaign->end_time->format(config('timeformat'))}}</strong>
                        </span>
                    </div>
                </div>
            </div>
            <!-- End Body -->
        </div>

        <div class="row g-2">
            <div class="col-lg-4 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="text-center">
                            <span class="mb-3">{{translate('messages.store')}} {{translate('messages.info')}}</span>
                            @if($campaign->store)
                            <div class="w-100 my-2">
                                <a href="{{route('admin.store.view', $campaign->store_id)}}" title="{{$campaign->store['name']}}">
                                    <img
                                        class="img--70 circle"
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                        src="{{asset('storage/app/public/store/'.$campaign->store->logo)}}"
                                        alt="Image Description">
                                    <h5 class="input-label mt-2">{{$campaign->store['name']}}</h5>
                                </a>
                                @else
                                <span class="badge-info">{{translate('messages.store')}} {{translate('messages.deleted')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless table-thead-bordered table-align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="px-4 border-0 w--120px">
                                            <h4 class="m-0">{{translate('messages.short')}} {{translate('messages.description')}}</h4>
                                        </th>
                                        <th class="px-4 border-0 w--120px">
                                            <h4 class="m-0">{{translate('messages.price')}}</h4>
                                        </th>
                                        <th class="px-4 border-0 w--120px">
                                            <h4 class="m-0">{{translate('messages.variations')}}</h4>
                                        </th>
                                        <th class="px-4 border-0 w--120px">
                                            <h4 class="m-0">Addons</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-4">
                                            <p>{{$campaign['description']}}</p>
                                        </td>
                                        <td class="px-4">
                                            <div>

                                                <span class="d-block text-dark">Price : <strong>{{\App\CentralLogics\Helpers::format_currency($campaign['price'])}}</strong>
                                                </span>
                                                <span class="d-block text-dark">{{translate('messages.tax')}} :
                                                    <strong>{{\App\CentralLogics\Helpers::format_currency(\App\CentralLogics\Helpers::tax_calculate($campaign,$campaign['price']))}}</strong>
                                                </span>
                                                <span class="d-block text-dark">{{translate('messages.discount')}} :
                                                    <strong>{{\App\CentralLogics\Helpers::format_currency(\App\CentralLogics\Helpers::discount_calculate($campaign,$campaign['price']))}}</strong>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            @if ($campaign->module->module_type == 'food')
                                            @if ($campaign->food_variations && is_array(json_decode($campaign['food_variations'], true)))
                                                @foreach (json_decode($campaign->food_variations, true) as $variation)
                                                    @if (isset($variation['price']))
                                                        <span class="d-block mb-1 text-capitalize">
                                                            <strong>
                                                                {{ translate('please_update_the_food_variations.') }}
                                                            </strong>
                                                        </span>
                                                    @break
    
                                                @else
                                                    <span class="d-block text-capitalize">
                                                        <strong>
                                                            {{ $variation['name'] }} -
                                                        </strong>
                                                        @if ($variation['type'] == 'multi')
                                                            {{ translate('messages.multiple_select') }}
                                                        @elseif($variation['type'] == 'single')
                                                            {{ translate('messages.single_select') }}
                                                        @endif
                                                        @if ($variation['required'] == 'on')
                                                            - ({{ translate('messages.required') }})
                                                        @endif
                                                    </span>
    
                                                    @if ($variation['min'] != 0 && $variation['max'] != 0)
                                                        ({{ translate('messages.Min_select') }}: {{ $variation['min'] }} -
                                                        {{ translate('messages.Max_select') }}: {{ $variation['max'] }})
                                                    @endif
    
                                                    @if (isset($variation['values']))
                                                        @foreach ($variation['values'] as $value)
                                                            <span class="d-block text-capitalize">
                                                                &nbsp; &nbsp; {{ $value['label'] }} :
                                                                <strong>{{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}</strong>
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endforeach
                                            @endif
                                        @else.
                                        @if ($campaign->variations && is_array(json_decode($campaign['variations'], true)))
                                            @foreach (json_decode($campaign['variations'], true) as $variation)
                                                <span class="d-block mb-1 text-capitalize">
                                                    {{ $variation['type'] }} :
                                                    {{ \App\CentralLogics\Helpers::format_currency($variation['price']) }}
                                                </span>
                                            @endforeach
                                        @endif
                                        @endif

                                        </td>
                                        <td class="px-4">
                                            @foreach(\App\Models\AddOn::whereIn('id',json_decode($campaign['add_ons'],true))->get() as $addon)
                                                <small class="d-block text-capitalize">
                                                {{$addon['name']}} : {{\App\CentralLogics\Helpers::format_currency($addon['price'])}}
                                                </small>
                                            @endforeach
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- End Card -->
        @php($orders = $campaign->orderdetails()->paginate(config('default_pagination')))
        <!-- Card -->
        <div class="card mt-3">
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                       class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                       data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">
                            SL
                        </th>
                        <th class="table-column-pl-0 border-0">{{translate('messages.order')}}</th>
                        <th class="border-0">{{translate('messages.date')}}</th>
                        <th class="border-0">{{translate('messages.customer')}}</th>
                        <th class="border-0">{{translate('messages.store')}}</th>
                        <th class="border-0">{{translate('messages.payment')}} {{translate('messages.status')}}</th>
                        <th class="border-0">{{translate('messages.total')}}</th>
                        <th class="border-0">{{translate('messages.order')}} {{translate('messages.status')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="">
                                {{$key+1}}
                            </td>
                            <td class="table-column-pl-0">
                                <a href="{{route('admin.order.details',['id'=>$order['order_id']])}}">{{$order->order['id']}}</a>
                            </td>
                            <td>{{date('d M Y',strtotime($order->order['created_at']))}}</td>
                            <td>
                                @if($order->order->customer)
                                    <a class="text-body text-capitalize"
                                       href="{{route('admin.customer.view',[$order->order['user_id']])}}">{{$order->order->customer['f_name'].' '.$order->order->customer['l_name']}}</a>
                                @else
                                    <label class="badge badge-danger">{{translate('messages.invalid')}} {{translate('messages.customer')}} {{translate('messages.data')}}</label>
                                @endif
                            </td>
                            <td>
                                <label class="badge badge-soft-primary">{{Str::limit($order->order->store?$order->order->store->name:translate('messages.store deleted!'),20,'...')}}</label>
                            </td>
                            <td>
                                @if($order->order->payment_status=='paid')
                                    <span class="badge badge-soft-success">
                                      <span class="legend-indicator bg-success"></span>{{translate('messages.paid')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                      <span class="legend-indicator bg-danger"></span>{{translate('messages.unpaid')}}
                                    </span>
                                @endif
                            </td>
                            <td>{{\App\CentralLogics\Helpers::format_currency($order->order['order_amount'])}}</td>
                            <td class="text-capitalize">
                                @if($order->order['order_status']=='pending')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                      {{translate('messages.pending')}}
                                    </span>
                                @elseif($order->order['order_status']=='confirmed')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                      {{translate('messages.confirmed')}}
                                    </span>
                                @elseif($order->order['order_status']=='processing')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      {{translate('messages.processing')}}
                                    </span>
                                @elseif($order->order['order_status']=='out_for_delivery')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      {{translate('messages.out_for_delivery')}}
                                    </span>
                                @elseif($order->order['order_status']=='delivered')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                      {{translate('messages.delivered')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                      {{str_replace('_',' ',$order->order['order_status'])}}
                                    </span>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Footer -->
            <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-12">
                        {!! $orders->links() !!}
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')

@endpush

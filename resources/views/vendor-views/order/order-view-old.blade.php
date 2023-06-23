@extends('layouts.vendor.app')

@section('title','Order Details')

@push('css_or_js')
<style>
    .item-box{
        height:250px;
        width:150px;
        padding:3px;
    }

    .header-item{
        width:10rem;
    }
</style>
@endpush

@section('content')
    <?php $campaign_order=$order->details[0]->campaign?true:false; ?>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link"
                                   href="{{route('vendor.order.list',['status'=>'all'])}}">
                                    {{translate('messages.orders')}}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{translate('messages.order')}} {{translate('messages.details')}}</li>
                        </ol>
                    </nav>

                    <div class="d-sm-flex align-items-sm-center">
                        <h1 class="page-header-title">{{translate('messages.order')}} #{{$order['id']}}</h1>

                        @if($order['payment_status']=='paid')
                            <span class="badge badge-soft-success ml-sm-3">
                                <span class="legend-indicator bg-success"></span>{{translate('messages.paid')}}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-sm-3">
                                <span class="legend-indicator bg-danger"></span>{{translate('messages.unpaid')}}
                            </span>
                        @endif

                        @if($order['order_status']=='pending')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info text"></span>{{translate('messages.pending')}}
                            </span>
                        @elseif($order['order_status']=='confirmed')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info"></span>{{translate('messages.confirmed')}}
                            </span>
                        @elseif($order['order_status']=='processing')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-warning"></span>{{translate('messages.cooking')}}
                            </span>
                        @elseif($order['order_status']=='picked_up')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-warning"></span>{{translate('messages.out_for_delivery')}}
                            </span>
                        @elseif($order['order_status']=='delivered')
                            <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-success"></span>{{translate('messages.delivered')}}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$order['order_status'])}}
                            </span>
                        @endif
                        @if($campaign_order)
                            <span class="badge badge-soft-success ml-sm-3">
                                <span class="legend-indicator bg-success"></span>{{translate('messages.campaign_order')}}
                            </span>
                        @endif
                        @if($order->edited)
                            <span class="badge badge-soft-dark ml-sm-3">
                                <span class="legend-indicator bg-dark"></span>{{translate('messages.edited')}}
                            </span>
                        @endif
                        <span class="ml-2 ml-sm-3">
                                <i class="tio-date-range"></i> {{date('d M Y '.config('timeformat'),strtotime($order['created_at']))}}
                        </span>
                    </div>

                    <div class="mt-2">
                        <a class="text-body mr-3"
                           href="{{route('vendor.order.generate-invoice',[$order['id']])}}">
                            <i class="tio-print mr-1"></i> {{translate('messages.print')}} {{translate('messages.invoice')}}
                        </a>

                        <!-- Unfold -->
                        @php($order_delivery_verification = (boolean)\App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value)
                        <div class="hs-unfold float-right">
                            @if($order['order_status']=='pending')
                            <a class="btn btn-sm btn-primary" onclick="order_status_change_alert('{{route('vendor.order.status',['id'=>$order['id'],'order_status'=>'confirmed'])}}','Change status to confirmed ?')" href="javascript:">{{translate('messages.confirm_this_order')}}</a>
                            @if(config('canceled_by_restaurant'))
                            <a class="btn btn-sm btn-danger" onclick="order_status_change_alert('{{route('vendor.order.status',['id'=>$order['id'],'order_status'=>'canceled'])}}', '{{translate('messages.order_canceled_confirmation')}}')" href="javascript:">{{translate('messages.cancel_this_order')}}</a>
                            @endif
                            @elseif ($order['order_status']=='confirmed' || $order['order_status']=='accepted')
                            <a class="btn btn-sm btn-primary" onclick="order_status_change_alert('{{route('vendor.order.status',['id'=>$order['id'],'order_status'=>'processing'])}}','Change status to processing ?')" href="javascript:">{{translate('messages.Proceed_for_cooking')}}</a>
                            @elseif ($order['order_status']=='processing')
                            <a class="btn btn-sm btn-primary" onclick="order_status_change_alert('{{route('vendor.order.status',['id'=>$order['id'],'order_status'=>'handover'])}}','Change status to ready for handover ?')" href="javascript:">{{translate('messages.make_ready_for_handover')}}</a>
                            @elseif ($order['order_status']=='handover' && ($order['order_type']=='take_away' || \App\CentralLogics\Helpers::get_store_data()->self_delivery_system))
                            <a class="btn btn-sm btn-primary" onclick="order_status_change_alert('{{route('vendor.order.status',['id'=>$order['id'],'order_status'=>'delivered'])}}','Change status to delivered (payment status will be paid if not) ?', {{$order_delivery_verification?'true':'false'}})" href="javascript:">{{translate('messages.maek_delivered')}}</a>
                            @endif
                        </div>
                        <!-- End Unfold -->
                    </div>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle mr-1"
                       href="{{route('vendor.order.details',[$order['id']-1])}}"
                       data-toggle="tooltip" data-placement="top" title="Previous order">
                        <i class="tio-arrow-backward"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle"
                       href="{{route('vendor.order.details',[$order['id']+1])}}" data-toggle="tooltip"
                       data-placement="top" title="Next order">
                        <i class="tio-arrow-forward"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header" style="display: block!important;">
                        <div class="row">
                            <div class="col-12 pb-2 border-bottom d-flex justify-content-between">
                                <h4 class="card-header-title">
                                    {{translate('messages.order')}} {{translate('messages.details')}}
                                    <span
                                        class="badge badge-soft-dark rounded-circle ml-1">{{$order->details->count()}}</span>
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 pt-2">
                                <h6 style="color: #8a8a8a;">
                                    {{translate('messages.order')}} {{translate('messages.note')}} : {{$order['order_note']}}
                                </h6>
                                @if ($order->order_attachment)
                                    <h5 class="text-dark">
                                        {{translate('messages.prescription')}}:
                                    </h5>
                                    <button class="btn w-100"  data-toggle="modal" data-target="#imagemodal" title="{{translate('messages.order')}} {{translate('messages.attachment')}}">
                                        <div class="gallary-card">
                                            <img src="{{asset('storage/app/'.'public/order/'.$order->order_attachment)}}" alt="{{translate('messages.prescription')}}" style="height:auto;width:50%;">
                                        </div>
                                    </button>
                                    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel">{{translate('messages.prescription')}}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{asset('storage/app/'.'public/order/'.$order->order_attachment)}}" style="width: 100%; height: auto;" >
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary" href="{{route('admin.file-manager.download', base64_encode('public/order/'.$order->order_attachment))}}"><i class="tio-download"></i> {{translate('messages.download')}} </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-6 pt-2">
                                <div class="text-right">
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">
                                        {{translate('messages.payment')}} {{translate('messages.method')}} : {{str_replace('_',' ',$order['payment_method'])}}
                                    </h6>
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">{{translate('messages.order')}} {{translate('messages.type')}}
                                        : <label style="font-size: 10px"
                                                 class="badge badge-soft-primary">{{str_replace('_',' ',$order['order_type'])}}</label>
                                    </h6>
                                    @if($order->schedule_at && $order->scheduled)
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">{{translate('messages.scheduled_at')}}
                                        : <label style="font-size: 10px"
                                                 class="badge badge-soft-primary">{{date('d M Y '.config('timeformat'),strtotime($order['schedule_at']))}}</label>
                                    </h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                    <?php
                        $total_addon_price = 0;
                        $product_price = 0;
                        $store_discount_amount = 0;
                        $product_price=0;
                        $total_addon_price=0;
                    ?>
                    @foreach($order->details as $key=>$detail)
                        @if(isset($detail->item_id) )
                            @php($detail->item = json_decode($detail->item_details, true))
                            <!-- Media -->
                            <div class="media">
                                <a class="avatar avatar-xl mr-3 cursor-pointer" href="{{route('vendor.item.view',$detail->item['id'])}}">
                                    <img class="img-fluid"
                                            src="{{asset('storage/app/public/product')}}/{{$detail->item['image']}}"
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                                            alt="Image Description">
                                </a>

                                <div class="media-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0 text-break">
                                            <strong> {{Str::limit($detail->item['name'], 25, '...')}}</strong><br>

                                            @if(count(json_decode($detail['variation'],true))>0)
                                                <strong><u>{{translate('messages.variation')}} : </u></strong>
                                                @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                                    @if ($key1 != 'stock')
                                                        <div class="font-size-sm text-body">
                                                            <span>{{$key1}} :  </span>
                                                            <span class="font-weight-bold">{{Str::limit($variation,20,'...')}}</span>
                                                        </div>
                                                    @endif

                                                @endforeach
                                            @endif

                                            @foreach(json_decode($detail['add_ons'],true) as $key2 =>$addon)
                                                @if($key2==0)<strong><u>{{translate('messages.addons')}} : </u></strong>@endif
                                                <div class="font-size-sm text-body">
                                                    <span>{{Str::limit($addon['name'], 25, '...')}} :  </span>
                                                    <span class="font-weight-bold">
                                                        {{$addon['quantity']}} x {{\App\CentralLogics\Helpers::format_currency($addon['price'])}}
                                                    </span>
                                                </div>
                                                @php($total_addon_price+=$addon['price']*$addon['quantity'])
                                            @endforeach
                                        </div>

                                        <div class="col col-md-2 align-self-center">
                                            <h6>{{\App\CentralLogics\Helpers::format_currency($detail['price'])}}</h6>
                                        </div>
                                        <div class="col col-md-1 align-self-center">
                                            <h5>{{$detail['quantity']}}</h5>
                                        </div>

                                        <div class="col col-md-3 align-self-center text-right">
                                            @php($amount=($detail['price'])*$detail['quantity'])
                                            <h5>{{\App\CentralLogics\Helpers::format_currency($amount)}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php($product_price+=$amount)
                            @php($store_discount_amount += ($detail['discount_on_item']*$detail['quantity']))
                            <!-- End Media -->
                            <hr>
                        @elseif(isset($detail->item_campaign_id))
                            @php($detail->campaign = json_decode($detail->item_details, true))
                            <!-- Media -->
                                <div class="media">
                                    <div class="avatar avatar-xl mr-3">
                                        <img class="img-fluid"
                                             src="{{asset('storage/app/public/campaign')}}/{{$detail->campaign['image']}}"
                                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                                             alt="Image Description">
                                    </div>

                                    <div class="media-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <strong> {{Str::limit($detail->campaign['name'], 25, '...')}}</strong><br>

                                                @if(count(json_decode($detail['variation'],true))>0)
                                                    <strong><u>{{translate('messages.variation')}} : </u></strong>
                                                    @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                                        @if ($key1 != 'stock')
                                                            <div class="font-size-sm text-body">
                                                                <span>{{$key1}} :  </span>
                                                                <span class="font-weight-bold">{{Str::limit($variation, 25, '...')}}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif

                                                @foreach(json_decode($detail['add_ons'],true) as $key2 =>$addon)
                                                    @if($key2==0)<strong><u>{{translate('messages.addons')}} : </u></strong>@endif
                                                    <div class="font-size-sm text-body">
                                                        <span>{{Str::limit($addon['name'], 20, '...')}} :  </span>
                                                        <span class="font-weight-bold">
                                                            {{$addon['quantity']}} x {{\App\CentralLogics\Helpers::format_currency($addon['price'])}}
                                                        </span>
                                                    </div>
                                                    @php($total_addon_price+=$addon['price']*$addon['quantity'])
                                                @endforeach
                                            </div>

                                            <div class="col col-md-2 align-self-center">
                                                <h6>{{\App\CentralLogics\Helpers::format_currency($detail['price']) }}</h6>
                                            </div>
                                            <div class="col col-md-1 align-self-center">
                                                <h5>{{$detail['quantity']}}</h5>
                                            </div>

                                            <div class="col col-md-3 align-self-center text-right">
                                                @php($amount=($detail['price'])*$detail['quantity'])
                                                <h5>{{\App\CentralLogics\Helpers::format_currency($amount)}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @php($product_price+=$amount)
                            @php($store_discount_amount += ($detail['discount_on_item']*$detail['quantity']))
                            <!-- End Media -->
                                <hr>
                        @endif
                    @endforeach
                        <?php

                            $coupon_discount_amount = $order['coupon_discount_amount'];

                            $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

                            $total_tax_amount= $order['total_tax_amount'];

                            $store_discount_amount = $order['store_discount_amount'];

                        ?>
                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-sm-right">
                                    <dt class="col-sm-6">{{translate('messages.items')}} {{translate('messages.price')}}:</dt>
                                    <dd class="col-sm-6">{{\App\CentralLogics\Helpers::format_currency($product_price)}}</dd>
                                    <dt class="col-sm-6">{{translate('messages.addon')}} {{translate('messages.cost')}}:</dt>
                                    <dd class="col-sm-6">
                                        {{\App\CentralLogics\Helpers::format_currency($total_addon_price)}}
                                        <hr>
                                    </dd>

                                    <dt class="col-sm-6">{{translate('messages.subtotal')}}:</dt>
                                    <dd class="col-sm-6">
                                        {{\App\CentralLogics\Helpers::format_currency($product_price+$total_addon_price)}}</dd>
                                    <dt class="col-sm-6">{{translate('messages.discount')}}:</dt>
                                    <dd class="col-sm-6">
                                        - {{\App\CentralLogics\Helpers::format_currency($store_discount_amount)}}</dd>
                                    <dt class="col-sm-6">{{translate('messages.coupon')}} {{translate('messages.discount')}}:</dt>
                                    <dd class="col-sm-6">
                                        - {{\App\CentralLogics\Helpers::format_currency($coupon_discount_amount)}}</dd>
                                    <dt class="col-sm-6">{{translate('messages.vat/tax')}}:</dt>
                                    <dd class="col-sm-6">
                                        + {{\App\CentralLogics\Helpers::format_currency($total_tax_amount)}}</dd>
                                    <dt class="col-sm-6">{{ translate('messages.delivery_man_tips') }}</dt>
                                    <dd class="col-sm-6">
                                        + {{ \App\CentralLogics\Helpers::format_currency($order->dm_tips) }}</dd>
                                    <dt class="col-sm-6">{{translate('messages.delivery')}} {{translate('messages.fee')}}:</dt>
                                    <dd class="col-sm-6">
                                            @php($del_c=$order['delivery_charge'])
                                        + {{\App\CentralLogics\Helpers::format_currency($del_c)}}
                                        <hr>
                                    </dd>

                                    <dt class="col-sm-6">{{translate('messages.total')}}:</dt>
                                    <dd class="col-sm-6">{{\App\CentralLogics\Helpers::format_currency($product_price+$del_c+$total_tax_amount+$total_addon_price-$coupon_discount_amount - $store_discount_amount + $order->dm_tips)}}</dd>
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4">
            @if($order['order_type']!='take_away')
                <!-- Card -->
                <div class="card mb-2">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{translate('messages.deliveryman')}}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->

                    <div class="card-body">
                    @if($order->delivery_man)
                        <div class="media align-items-center" href="javascript:">
                            <div class="avatar avatar-circle mr-3">
                                <img
                                    class="avatar-img" style="width: 75px"
                                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                    src="{{asset('storage/app/public/delivery-man/'.$order->delivery_man->image)}}"
                                    alt="Image Description">
                            </div>
                            <div class="media-body">
                            <span
                                class="text-body text-hover-primary">{{$order->delivery_man['f_name'].' '.$order->delivery_man['l_name']}}</span>
                            </div>
                            <div class="media-body text-right">
                                {{--<i class="tio-chevron-right text-body"></i>--}}
                            </div>
                        </div>

                        <hr>

                        <div class="media align-items-center" href="javascript:">
                            <div class="icon icon-soft-info icon-circle mr-3">
                                <i class="tio-shopping-basket-outlined"></i>
                            </div>
                            <div class="media-body">
                                <span class="text-body text-hover-primary text-lowercase">{{$order->delivery_man->orders_count}} {{translate('messages.orders')}}</span>
                            </div>
                            <div class="media-body text-right">
                                {{--<i class="tio-chevron-right text-body"></i>--}}
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{translate('messages.contact')}} {{translate('messages.info')}}</h5>
                        </div>

                        <ul class="list-unstyled list-unstyled-py-2">
                            <li>
                                <i class="tio-online mr-2"></i>
                                {{$order->delivery_man['email']}}
                            </li>
                            <li>
                                <a class="deco-none" href="tel:{{$order->delivery_man['phone']}}">
                                    <i class="tio-android-phone-vs mr-2"></i>
                                {{$order->delivery_man['phone']}}</a>
                            </li>
                        </ul>

                        @if($order['order_type']!='take_away')
                            <hr>
                            @php($address=$order->dm_last_location)
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{translate('messages.last')}} {{translate('messages.location')}}</h5>
                            </div>
                            @if(isset($address))
                            <span class="d-block">
                                <a target="_blank"
                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                    <i class="tio-map"></i> {{$address['location']}}<br>
                                </a>
                            </span>
                            @else
                            <span class="d-block text-lowercase qcont">
                                {{translate('messages.location').' '.translate('messages.not_found')}}
                            </span>
                            @endif
                        @endif
                    @else
                        <span class="d-block text-lowercase qcont">
                                {{translate('messages.deliveryman').' '.translate('messages.not_found')}}
                        </span>
                    @endif
                    </div>

                <!-- End Body -->
                </div>
                <!-- End Card -->
                @endif
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{translate('messages.customer')}}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if($order->customer)
                        <div class="card-body">
                            <div class="media align-items-center" href="javascript:">
                                <div class="avatar avatar-circle mr-3">
                                    <img
                                        class="avatar-img" style="width: 75px"
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                        src="{{asset('storage/app/public/profile/'.$order->customer->image)}}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                <span
                                    class="text-body text-hover-primary">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</span>
                                </div>
                                <div class="media-body text-right">
                                    {{--<i class="tio-chevron-right text-body"></i>--}}
                                </div>
                            </div>

                            <hr>

                            <div class="media align-items-center" href="javascript:">
                                <div class="icon icon-soft-info icon-circle mr-3">
                                    <i class="tio-shopping-basket-outlined"></i>
                                </div>
                                <div class="media-body">
                                    <span class="text-body text-hover-primary">{{$order->customer->orders_count}} orders</span>
                                </div>
                                <div class="media-body text-right">
                                    {{--<i class="tio-chevron-right text-body"></i>--}}
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{translate('messages.contact')}} {{translate('messages.info')}}</h5>
                            </div>

                            <ul class="list-unstyled list-unstyled-py-2">
                                <li>
                                    <i class="tio-online mr-2"></i>
                                    {{$order->customer['email']}}
                                </li>
                                <li>
                                    <a class="deco-none" href="tel:{{$order->customer['phone']}}">
                                        <i class="tio-android-phone-vs mr-2"></i>
                                        {{$order->customer['phone']}}
                                    </a>
                                </li>
                            </ul>

                            @if($order->delivery_address)
                                <hr>
                                @php($address=json_decode($order->delivery_address,true))
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{translate('messages.delivery')}} {{translate('messages.info')}}</h5>
                                    @if(isset($address))
                                        {{--<a class="link" data-toggle="modal" data-target="#shipping-address-modal"
                                           href="javascript:">{{translate('messages.edit')}}</a>--}}
                                    @endif
                                </div>
                                @if(isset($address))
                                    <span class="d-block">
                                    {{translate('messages.name')}}: {{$address['contact_person_name']}}<br>
                                    {{translate('messages.contact')}}:<a class="deco-none" href="tel:{{$address['contact_person_number']}}"> {{$address['contact_person_number']}}</a><br>
                                    {{ translate('Floor') }}: {{ isset($address['floor'])?$address['floor']:'' }}<br>
                                    {{ translate('Road') }}: {{ isset($address['road'])?$address['road']:'' }}<br>
                                    {{ translate('House') }}: {{ isset($address['house'])?$address['house']:'' }}<br>
                                    @if($order['order_type']!='take_away' && isset($address['address']))
                                        @if(isset($address['latitude']) && isset($address['longitude']))
                                        <a target="_blank"
                                        href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                        <i class="tio-map"></i>{{$address['address']}}<br>
                                        </a>
                                        @else
                                        <i class="tio-map"></i>{{$address['address']}}<br>
                                        @endif
                                    @endif
                                </span>
                                @endif
                            @endif
                        </div>
                @endif
                <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Row -->
    </div>

    <!-- Modal -->
    <div id="shipping-address-modal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-top-cover bg-dark text-center">
                    <figure class="position-absolute right-0 bottom-0 left-0" style="margin-bottom: -1px;">
                        <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                             viewBox="0 0 1920 100.1">
                            <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z"/>
                        </svg>
                    </figure>

                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                                aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor"
                                      d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- End Header -->

                <div class="modal-top-cover-icon">
                    <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                      <i class="tio-location-search"></i>
                    </span>
                </div>

                @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
                @if(isset($address))
                    <form action="{{route('vendor.order.update-shipping',[$order['delivery_address_id']])}}"
                          method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('messages.type')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address_type"
                                           value="{{$address['address_type']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('messages.contact')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_number"
                                           value="{{$address['contact_person_number']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('messages.name')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_name"
                                           value="{{$address['contact_person_name']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('House') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="house"
                                        value="{{ isset($address['house'])?$address['house']:'' }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('Floor') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="floor"
                                        value="{{ isset($address['floor'])? $address['floor'] : '' }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('Road') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="road"
                                        value="{{ isset($address['road'])?$address['road']:'' }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('messages.address')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address"
                                           value="{{$address['address']}}"
                                           required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('messages.latitude')}}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="latitude"
                                           value="{{$address['latitude']}}"
                                           required>
                                </div>
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('messages.longitude')}}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="longitude"
                                           value="{{$address['longitude']}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-dismiss="modal">{{translate('messages.close')}}</button>
                            <button type="submit" class="btn btn-primary">{{translate('messages.save')}} {{translate('messages.changes')}}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- End Content -->


@endsection
@push('script_2')
    <script>
        function order_status_change_alert(route, message, verification) {
            if(verification)
            {
                Swal.fire({
                    title: 'Enter order verification code',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: 'Submit',
                    showLoaderOnConfirm: true,
                    preConfirm: (otp) => {
                        location.href = route+'&otp='+otp;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                    })
            }
            else
            {
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
                        location.href = route;
                    }
                })
            }
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endpush

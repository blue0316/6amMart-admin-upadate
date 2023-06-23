@extends('layouts.vendor.app')

@section('title', translate('messages.Order Details'))


@section('content')
    <?php
    if (count($order->details) > 0) {
        $campaign_order = $order->details[0]->campaign ? true : false;
        $tax_included =0;
    }
    ?>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <img src="{{ asset('/public/assets/admin/img/shopping-basket.png') }}" class="w--20"
                                alt="">
                        </span>
                        <span>
                            {{ translate('order_details') }} <span
                                class="badge badge-soft-dark rounded-circle ml-1">{{ $order->details->count() }}</span>
                        </span>
                    </h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                        href="{{ route('vendor.order.details', [$order['id'] - 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Previous order">
                        <i class="tio-chevron-left"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                        href="{{ route('vendor.order.details', [$order['id'] + 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Next order">
                        <i class="tio-chevron-right"></i>
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
                    <div class="card-header border-0 align-items-start flex-wrap">
                        <div class="order-invoice-left d-flex d-sm-flex justify-content-between">
                            <div>
                                <h1 class="page-header-title">
                                    {{ translate('messages.order') }} #{{ $order['id'] }}

                                    @if ($order->edited)
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.edited') }}
                                        </span>
                                    @endif
                                </h1>
                                <span class="mt-2 d-block">
                                    <i class="tio-date-range"></i>
                                    {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                                </span>
                                @if ($order->schedule_at && $order->scheduled)
                                    <h6 class="text-capitalize">
                                        {{ translate('messages.scheduled_at') }}
                                        : <label
                                            class="fz--10 badge badge-soft-warning">{{ date('d M Y ' . config('timeformat'), strtotime($order['schedule_at'])) }}</label>
                                    </h6>
                                @endif
                                @if ($order['order_note'])
                                    <h6>
                                        {{ translate('messages.order') }} {{ translate('messages.note') }} :
                                        {{ $order['order_note'] }}
                                    </h6>
                                @endif
                            </div>
                            <div class="d-sm-none">
                                <a class="btn btn--primary print--btn font-regular"
                                    href={{ route('vendor.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i> <span>{{ translate('messages.print') }}
                                        {{ translate('messages.invoice') }}</span>
                                </a>
                            </div>
                        </div>


                        <div class="order-invoice-right mt-3 mt-sm-0">
                            <div class="btn--container ml-auto align-items-center justify-content-end">
                                <a class="btn btn--primary print--btn font-regular d-none d-sm-block"
                                    href={{ route('vendor.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i> <span>{{ translate('messages.print') }}
                                        {{ translate('messages.invoice') }}</span>
                                </a>
                            </div>
                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h6>
                                    {{ translate('messages.payment_status') }} :
                                    @if ($order['payment_status'] == 'paid')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{ translate('messages.paid') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.unpaid') }}
                                        </span>
                                    @endif
                                </h6>
                                <h6 class="text-capitalize">
                                    {{ translate('messages.payment') }} {{ translate('messages.method') }} :
                                    {{ translate(str_replace('_', ' ', $order['payment_method'])) }}
                                </h6>
                                @if ($order['transaction_reference'])
                                    <h6 class="">
                                        {{ translate('messages.reference') }} {{ translate('messages.code') }} :
                                        <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                            data-target=".bd-example-modal-sm">
                                            {{ translate('messages.add') }}
                                        </button>
                                    </h6>
                                @endif
                                <h6 class="text-capitalize">{{ translate('messages.order') }}
                                    {{ translate('messages.type') }}
                                    : <label
                                        class="fz--10 badge m-0 badge-soft-primary">{{ translate(str_replace('_', ' ', $order['order_type'])) }}</label>
                                </h6>
                                <h6>
                                    {{ translate('messages.order_status') }} :
                                    @if ($order['order_status'] == 'pending')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif($order['order_status'] == 'confirmed')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.confirmed') }}
                                        </span>
                                    @elseif($order['order_status'] == 'processing')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.processing') }}
                                        </span>
                                    @elseif($order['order_status'] == 'picked_up')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.out_for_delivery') }}
                                        </span>
                                    @elseif($order['order_status'] == 'delivered')
                                        <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.delivered') }}
                                        </span>
                                    @elseif($order['order_status'] == 'failed')
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.payment') }}
                                            {{ translate('messages.failed') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ str_replace('_', ' ', $order['order_status']) }}
                                        </span>
                                    @endif
                                </h6>
                                @if ($order->order_attachment)
                                    @if ($order->prescription_order)
                                        @php
                                            $order_images = json_decode($order->order_attachment);
                                        @endphp
                                        <h5 class="text-dark">
                                            {{ translate('messages.prescription') }}:
                                        </h5>
                                        <div class="d-flex flex-wrap" style="gap:15px">
                                            @foreach ($order_images as $key => $item)
                                                <div>
                                                    <button class="btn w-100 px-0" data-toggle="modal"
                                                        data-target="#imagemodal{{ $key }}"
                                                        title="{{ translate('messages.order') }} {{ translate('messages.attachment') }}">
                                                        <div class="gallary-card ml-auto">
                                                            <img src="{{ asset('storage/app/' . 'public/order/' . $item) }}"
                                                                alt="{{ translate('messages.prescription') }}"
                                                                class="initial--22 object-cover">
                                                        </div>
                                                    </button>
                                                </div>
                                                <div class="modal fade" id="imagemodal{{ $key }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    {{ translate('messages.prescription') }}</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal"><span
                                                                        aria-hidden="true">&times;</span><span
                                                                        class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ asset('storage/app/' . 'public/order/' . $item) }}"
                                                                    class="initial--22 w-100">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a class="btn btn-primary"
                                                                    href="{{ route('admin.file-manager.download', base64_encode('public/order/' . $item)) }}"><i
                                                                        class="tio-download"></i>
                                                                    {{ translate('messages.download') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                    <h5 class="text-dark">
                                        {{ translate('messages.prescription') }}:
                                    </h5>
                                    <button class="btn w-100 px-0" data-toggle="modal" data-target="#imagemodal"
                                        title="{{ translate('messages.order') }} {{ translate('messages.attachment') }}">
                                        <div class="gallary-card ml-auto">
                                            <img src="{{ asset('storage/app/' . 'public/order/' . $order->order_attachment) }}"
                                                alt="{{ translate('messages.prescription') }}"
                                                class="initial--22 object-cover">
                                        </div>
                                    </button>
                                    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        {{ translate('messages.prescription') }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><span
                                                            aria-hidden="true">&times;</span><span
                                                            class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ asset('storage/app/' . 'public/order/' . $order->order_attachment) }}"
                                                        class="initial--22 w-100">
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary"
                                                        href="{{ route('admin.file-manager.download', base64_encode('public/order/' . $order->order_attachment)) }}"><i
                                                            class="tio-download"></i> {{ translate('messages.download') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body px-0">
                        <?php
                        $total_addon_price = 0;
                        $product_price = 0;
                        $store_discount_amount = 0;
                        
                        if ($order->prescription_order == 1) {
                            $product_price = $order['order_amount'] - $order['delivery_charge'] - $order['total_tax_amount'] - $order['dm_tips'] + $order['store_discount_amount'];
                        }
                        
                        $total_addon_price = 0;
                        ?>
                        <div class="table-responsive">
                            <table
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{ translate('messages.#') }}</th>
                                        <th class="border-0">{{ translate('messages.item_details') }}</th>
                                        @if ($order->store->module->module_type == 'food')
                                            <th class="border-0">{{ translate('messages.addons') }}</th>
                                        @endif
                                        <th class="text-right  border-0">{{ translate('messages.price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->details as $key => $detail)
                                        @if (isset($detail->item_id))
                                            @php($detail->item = json_decode($detail->item_details, true))
                                            <!-- Media -->
                                            <tr>
                                                <td>
                                                    <div>
                                                        {{ $key + 1 }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="media media--sm">
                                                        <a class="avatar avatar-xl mr-3"
                                                            href="{{ route('vendor.item.view', $detail->item['id']) }}">
                                                            <img class="img-fluid rounded"
                                                                src="{{ asset('storage/app/public/product') }}/{{ $detail->item['image'] }}"
                                                                onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                                                                alt="Image Description">
                                                        </a>
                                                        <div class="media-body">
                                                            <div>
                                                                <strong
                                                                    class="line--limit-1">{{ Str::limit($detail->item['name'], 25, '...') }}</strong>
                                                                <h6>
                                                                    {{ $detail['quantity'] }} x
                                                                    {{ \App\CentralLogics\Helpers::format_currency($detail['price']) }}
                                                                </h6>
                                                                @if ($order->store && $order->store->module->module_type == 'food')
                                                                    @if (isset($detail['variation']) ? json_decode($detail['variation'], true) : [])
                                                                        @foreach (json_decode($detail['variation'], true) as $variation)
                                                                            @if (isset($variation['name']) && isset($variation['values']))
                                                                                <span class="d-block text-capitalize">
                                                                                    <strong>
                                                                                        {{ $variation['name'] }} -
                                                                                    </strong>
                                                                                </span>
                                                                                @foreach ($variation['values'] as $value)
                                                                                    <span class="d-block text-capitalize">
                                                                                        &nbsp; &nbsp;
                                                                                        {{ $value['label'] }} :
                                                                                        <strong>{{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}</strong>
                                                                                    </span>
                                                                                @endforeach
                                                                            @else
                                                                                @if (isset(json_decode($detail['variation'], true)[0]))
                                                                                    <strong><u>
                                                                                            {{ translate('messages.Variation') }}
                                                                                            : </u></strong>
                                                                                    @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                                                                        <div
                                                                                            class="font-size-sm text-body">
                                                                                            <span>{{ $key1 }}
                                                                                                : </span>
                                                                                            <span
                                                                                                class="font-weight-bold">{{ $variation }}</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                @endif
                                                                                {{-- @break --}}
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @else
                                                                    @if (count(json_decode($detail['variation'], true)) > 0)
                                                                        <strong><u>{{ translate('messages.variation') }} :
                                                                            </u></strong>
                                                                        @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                                                            @if ($key1 != 'stock' || ($order->store && config('module.' . $order->store->module->module_type)['stock']))
                                                                                <div class="font-size-sm text-body">
                                                                                    <span>{{ $key1 }} : </span>
                                                                                    <span
                                                                                        class="font-weight-bold">{{ Str::limit($variation, 20, '...') }}</span>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($order->store->module->module_type == 'food')
                                                    <td>
                                                        <div>
                                                            @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                                                                @if ($key2 == 0)
                                                                    <strong><u>{{ translate('messages.addons') }} :
                                                                        </u></strong>
                                                                @endif
                                                                <div class="font-size-sm text-body">
                                                                    <span>{{ Str::limit($addon['name'], 25, '...') }} :
                                                                    </span>
                                                                    <span class="font-weight-bold">
                                                                        {{ $addon['quantity'] }} x
                                                                        {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                                    </span>
                                                                </div>
                                                                @php($total_addon_price += $addon['price'] * $addon['quantity'])
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                @endif
                                                <td>
                                                    <div class="text-right">
                                                        @php($amount = $detail['price'] * $detail['quantity'])
                                                        <h5>{{ \App\CentralLogics\Helpers::format_currency($amount) }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php($product_price += $amount)
                                            @php($store_discount_amount += $detail['discount_on_item'] * $detail['quantity'])
                                            <!-- End Media -->
                                        @elseif(isset($detail->item_campaign_id))
                                            @php($detail->campaign = json_decode($detail->item_details, true))
                                            <!-- Media -->
                                            <tr>
                                                <td>
                                                    <div>
                                                        {{ $key + 1 }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="media media--sm">
                                                        <div class="avatar avatar-xl mr-3">
                                                            <img class="img-fluid"
                                                                src="{{ asset('storage/app/public/campaign') }}/{{ $detail->campaign['image'] }}"
                                                                onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                                                                alt="Image Description">
                                                        </div>
                                                        <div class="media-body">
                                                            <div>
                                                                <strong
                                                                    class="line--limit-1">{{ Str::limit($detail->campaign['name'], 25, '...') }}</strong>

                                                                <h6>
                                                                    {{ $detail['quantity'] }} x
                                                                    {{ \App\CentralLogics\Helpers::format_currency($detail['price']) }}
                                                                </h6>

                                                                @if (count(json_decode($detail['variation'], true)) > 0)
                                                                    <strong><u>{{ translate('messages.variation') }} :
                                                                        </u></strong>
                                                                    @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                                                        @if ($key1 != 'stock')
                                                                            <div class="font-size-sm text-body">
                                                                                <span>{{ $key1 }} : </span>
                                                                                <span
                                                                                    class="font-weight-bold">{{ Str::limit($variation, 25, '...') }}</span>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($order->store->module->module_type == 'food')
                                                    <td>
                                                        @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                                                            @if ($key2 == 0)
                                                                <strong><u>{{ translate('messages.addons') }} :
                                                                    </u></strong>
                                                            @endif
                                                            <div class="font-size-sm text-body">
                                                                <span>{{ Str::limit($addon['name'], 20, '...') }} : </span>
                                                                <span class="font-weight-bold">
                                                                    {{ $addon['quantity'] }} x
                                                                    {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                                </span>
                                                            </div>
                                                            @php($total_addon_price += $addon['price'] * $addon['quantity'])
                                                        @endforeach
                                                    </td>
                                                @endif
                                                <td>
                                                    <div class="text-right">
                                                        @php($amount = $detail['price'] * $detail['quantity'])
                                                        <h5>{{ \App\CentralLogics\Helpers::format_currency($amount) }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php($product_price += $amount)
                                            @php($store_discount_amount += $detail['discount_on_item'] * $detail['quantity'])
                                            <!-- End Media -->
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mx-3">
                            <hr>
                        </div>
                        <?php
                        
                        $coupon_discount_amount = $order['coupon_discount_amount'];
                        
                        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;
                        
                        $total_tax_amount = $order['total_tax_amount'];
                        $tax_included = \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first() ?  \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
                        
                        $store_discount_amount = $order['store_discount_amount'];
                        
                        ?>
                        <div class="row justify-content-md-end mb-3 mx-0 mt-4">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-right">
                                    <dt class="col-6">{{ translate('messages.items') }}
                                        {{ translate('messages.price') }}:</dt>
                                    <dd class="col-6">{{ \App\CentralLogics\Helpers::format_currency($product_price) }}
                                    </dd>
                                    @if ($order->store->module->module_type == 'food')
                                        <dt class="col-6">{{ translate('messages.addon') }}
                                            {{ translate('messages.cost') }}:</dt>

                                        <dd class="col-6">
                                            {{ \App\CentralLogics\Helpers::format_currency($total_addon_price) }}
                                            <hr>
                                        </dd>
                                    @endif

                                    <dt class="col-6">{{ translate('messages.subtotal') }}
                                        @if ($order->tax_status == 'included' ||  $tax_included ==  1)
                                        ({{ translate('messages.TAX_Included') }})
                                        @endif
                                        :</dt>

                                    <dd class="col-6">
                                        @if ($order->prescription_order == 1 &&
                                            ($order['order_status'] == 'pending' ||
                                                $order['order_status'] == 'confirmed' ||
                                                $order['order_status'] == 'processing'))
                                            <button class="btn btn-sm" type="button" data-toggle="modal"
                                                data-target="#edit-order-amount"><i class="tio-edit"></i></button>
                                        @endif
                                        {{ \App\CentralLogics\Helpers::format_currency($product_price + $total_addon_price) }}
                                    </dd>
                                    <dt class="col-6">{{ translate('messages.discount') }}:</dt>
                                    <dd class="col-6">
                                        @if ($order->prescription_order == 1 &&
                                            ($order['order_status'] == 'pending' ||
                                                $order['order_status'] == 'confirmed' ||
                                                $order['order_status'] == 'processing'))
                                            <button class="btn btn-sm" type="button" data-toggle="modal"
                                                data-target="#edit-discount-amount"><i class="tio-edit"></i></button>
                                        @endif
                                        - {{ \App\CentralLogics\Helpers::format_currency($store_discount_amount) }}
                                    </dd>
                                    <dt class="col-6">{{ translate('messages.coupon') }}
                                        {{ translate('messages.discount') }}:</dt>
                                    <dd class="col-6">
                                        - {{ \App\CentralLogics\Helpers::format_currency($coupon_discount_amount) }}</dd>
                                        @if ($order->tax_status == 'excluded' || $order->tax_status == null  )
                                        <dt class="col-sm-6">{{ translate('messages.vat/tax') }}:</dt>
                                        <dd class="col-sm-6">
                                            +
                                            {{ \App\CentralLogics\Helpers::format_currency($total_tax_amount) }}
                                        </dd>
                                        @endif
                                    <dt class="col-6">{{ translate('messages.delivery_man_tips') }}</dt>
                                    <dd class="col-6">
                                        + {{ \App\CentralLogics\Helpers::format_currency($order->dm_tips) }}</dd>
                                    <dt class="col-6">{{ translate('messages.delivery') }}
                                        {{ translate('messages.fee') }}:</dt>
                                    <dd class="col-6">
                                        @php($del_c = $order['delivery_charge'])
                                        + {{ \App\CentralLogics\Helpers::format_currency($del_c) }}
                                        <hr>
                                    </dd>

                                    <dt class="col-6">{{ translate('messages.total') }}:</dt>
                                    <dd class="col-6">
                                        {{ \App\CentralLogics\Helpers::format_currency($product_price + $del_c + $total_tax_amount + $total_addon_price - $coupon_discount_amount - $store_discount_amount + $order->dm_tips) }}
                                    </dd>
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
                <!-- Card -->
                @if ($order->order_status != 'refund_requested' &&
                    $order->order_status != 'refunded' &&
                    $order->order_status != 'delivered')
                    <div class="card mb-2">
                        <!-- Header -->
                        <div class="card-header justify-content-center text-center px-0 mx-4">
                            <h5 class="card-header-title text-capitalize">
                                <span>{{ translate('messages.order_setup') }}</span>
                            </h5>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->

                        <div class="card-body">
                            <!-- Order Status Flow Starts -->
                            @php($order_delivery_verification = (bool) \App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value)
                            <div class="mb-4">
                                <div class="row g-1">
                                    <div class="{{ config('canceled_by_store') ? 'col-6' : 'col-12' }}">
                                        <a class="btn btn--primary w-100 fz--13 px-2 {{ $order['order_status'] == 'pending' ? '' : 'd-none' }}"
                                            onclick="route_alert('{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'confirmed']) }}','{{ translate('messages.confirm_this_order') }}')"
                                            href="javascript:">{{ translate('messages.confirm_this_order') }}</a>
                                    </div>
                                    @if (config('canceled_by_store'))
                                        <div class="col-6">
                                            <a class="btn btn--danger w-100 fz--13 px-2 {{ $order['order_status'] == 'pending' ? '' : 'd-none' }}"
                                                onclick="order_status_change_alert('{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'canceled']) }}', '{{ translate('messages.order_canceled_confirmation') }}')"
                                                href="javascript:">{{ translate('messages.cancel') }}</a>
                                        </div>
                                    @endif
                                </div>
                                <a class="btn btn--primary w-100 {{ $order['order_status'] == 'confirmed' || $order['order_status'] == 'accepted' ? '' : 'd-none' }}"
                                    onclick="route_alert('{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}','{{ translate('messages.proceed_for_processing') }}')"
                                    href="javascript:">{{ translate('messages.proceed_for_processing') }}</a>
                                <a class="btn btn--primary w-100 {{ $order['order_status'] == 'processing' ? '' : 'd-none' }}"
                                    onclick="route_alert('{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'handover']) }}','{{ translate('messages.make_ready_for_handover') }}')"
                                    href="javascript:">{{ translate('messages.make_ready_for_handover') }}</a>
                                <a class="btn btn--primary w-100 {{ $order['order_status'] == 'handover' ? '' : 'd-none' }}"
                                    onclick="order_status_change_alert('{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'delivered']) }}','{{ translate('messages.Change status to delivered (payment status will be paid if not)?') }}', {{ $order_delivery_verification ? 'true' : 'false' }})"
                                    href="javascript:">{{ translate('messages.make_delivered') }}</a>
                            </div>
                        </div>

                        <!-- End Body -->
                    </div>
                @endif
                <!-- End Card -->
                @if ($order['order_type'] != 'take_away')
                    <!-- Card -->
                    <div class="card mb-2">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">
                                <span class="card-header-icon"><i class="tio-user"></i></span>
                                <span>{{ translate('messages.Delivery Man') }}</span>
                            </h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            @if ($order->delivery_man)
                                <div class="media align-items-center customer--information-single" href="javascript:">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img"
                                            onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                            src="{{ asset('storage/app/public/delivery-man/' . $order->delivery_man->image) }}"
                                            alt="Image Description">
                                    </div>
                                    <div class="media-body">
                                        <span
                                            class="text-body d-block text-hover-primary mb-1">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</span>

                                        <span class="text--title font-semibold d-flex align-items-center">
                                            <i class="tio-shopping-basket-outlined mr-2"></i>
                                            {{ $order->delivery_man->orders_count }}
                                            {{ translate('messages.orders_delivered') }}
                                        </span>

                                        <span class="text--title font-semibold d-flex align-items-center">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            {{ $order->delivery_man['phone'] }}
                                        </span>

                                        <span class="text--title font-semibold d-flex align-items-center">
                                            <i class="tio-email-outlined mr-2"></i>
                                            {{ $order->delivery_man['email'] }}
                                        </span>
                                    </div>
                                </div>

                                @if ($order['order_type'] != 'take_away')
                                    <hr>
                                    @php($address = $order->dm_last_location)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>{{ translate('messages.last') }} {{ translate('messages.location') }}</h5>
                                    </div>
                                    @if (isset($address))
                                        <span class="d-block">
                                            <a target="_blank"
                                                href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                <i class="tio-map"></i> {{ $address['location'] }}<br>
                                            </a>
                                        </span>
                                    @else
                                        <span class="d-block text-lowercase qcont">
                                            {{ translate('messages.location') . ' ' . translate('messages.not_found') }}
                                        </span>
                                    @endif
                                @endif
                            @else
                                <span class="badge badge-soft-danger py-2 d-block qcont">
                                    {{ translate('messages.deliveryman') . ' ' . translate('messages.not_found') }}
                                </span>
                            @endif
                        </div>
                        <!-- End Body -->
                    </div>
                @endif
                <!-- End Card -->
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">
                            <span class="card-header-icon"><i class="tio-user"></i></span>
                            <span>{{ translate('messages.customer') }}</span>
                        </h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if ($order->customer)
                        <div class="card-body">

                            <div class="media align-items-center customer--information-single" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img"
                                        onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                        src="{{ asset('storage/app/public/profile/' . $order->customer->image) }}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span
                                        class="text-body d-block text-hover-primary mb-1">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-shopping-basket-outlined mr-2"></i>
                                        {{ $order->customer->orders_count }}
                                        {{ translate('messages.orders_delivered') }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        {{ $order->customer['phone'] }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-email-outlined mr-2"></i>
                                        {{ $order->customer['email'] }}
                                    </span>

                                </div>
                            </div>
                            <hr>

                            @if ($order->delivery_address)
                                @php($address = json_decode($order->delivery_address, true))
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{ translate('messages.delivery') }} {{ translate('messages.info') }}</h5>
                                    @if (isset($address))
                                        {{-- <a class="link" data-toggle="modal" data-target="#shipping-address-modal"
                                           href="javascript:">{{translate('messages.edit')}}</a> --}}
                                    @endif
                                </div>
                                @if (isset($address))
                                    <span class="delivery--information-single d-block">
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.name') }}:</span>
                                            <span class="info">{{ $address['contact_person_name'] }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.contact') }}:</span>
                                            <a class="info deco-none"
                                                href="tel:{{ $address['contact_person_number'] }}">
                                                {{ $address['contact_person_number'] }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Floor') }}:</span>
                                            <span
                                                class="info">{{ isset($address['floor']) ? $address['floor'] : '' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Road') }}:</span>
                                            <span
                                                class="info">{{ isset($address['road']) ? $address['road'] : '' }}</span>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <span class="name">{{ translate('House') }}:</span>
                                            <span
                                                class="info">{{ isset($address['house']) ? $address['house'] : '' }}</span>
                                        </div>

                                        @if ($order['order_type'] != 'take_away' && isset($address['address']))
                                            @if (isset($address['latitude']) && isset($address['longitude']))
                                                <a target="_blank"
                                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                    <i class="tio-map"></i>{{ $address['address'] }}<br>
                                                </a>
                                            @else
                                                <i class="tio-map"></i>{{ $address['address'] }}<br>
                                            @endif
                                        @endif
                                    </span>
                                @endif
                            @endif
                        </div>
                    @else
                        <div class="card-body">
                            <span class="badge badge-soft-danger py-2 d-block qcont">
                                {{ translate('Customer Not found!') }}
                            </span>
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
                    <figure class="position-absolute right-0 bottom-0 left-0 mb--n-1">
                        <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                            viewBox="0 0 1920 100.1">
                            <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                        </svg>
                    </figure>

                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                            aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor"
                                    d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z" />
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

                @php($address = \App\Models\CustomerAddress::find($order['delivery_address_id']))
                @if (isset($address))
                    <form action="{{ route('vendor.order.update-shipping', [$order['delivery_address_id']]) }}"
                        method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.type') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address_type"
                                        value="{{ $address['address_type'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.contact') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_number"
                                        value="{{ $address['contact_person_number'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.name') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_name"
                                        value="{{ $address['contact_person_name'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('House') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="house"
                                        value="{{ isset($address['house']) ? $address['house'] : '' }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('Floor') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="floor"
                                        value="{{ isset($address['floor']) ? $address['floor'] : '' }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('Road') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="road"
                                        value="{{ isset($address['road']) ? $address['road'] : '' }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.address') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $address['address'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.latitude') }}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="latitude"
                                        value="{{ $address['latitude'] }}" required>
                                </div>
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.longitude') }}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="longitude"
                                        value="{{ $address['longitude'] }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white"
                                data-dismiss="modal">{{ translate('messages.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ translate('messages.save') }}
                                {{ translate('messages.changes') }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <div class="modal fade" id="edit-order-amount" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.update_order_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.order.update-order-amount') }}" method="POST" class="row">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="form-group col-12">
                            <label for="">{{ translate('messages.order_amount') }}</label>
                            <input type="number" class="form-control" name="order_amount" min="0"
                                value="{{ $order['order_amount'] - $order['total_tax_amount'] - $order['delivery_charge'] + $order['store_discount_amount'] }}" step=".01">
                        </div>

                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                type="submit">{{ translate('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-discount-amount" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.update_discount_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.order.update-discount-amount') }}" method="POST" class="row">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="form-group col-12">
                            <label for="">{{ translate('messages.discount_amount') }}</label>
                            <input type="number" class="form-control" name="discount_amount" min="0"
                                value="{{ $order['store_discount_amount'] }}" step=".01">
                        </div>

                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                type="submit">{{ translate('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- End Content -->


@endsection
@push('script_2')
    <script>
        function order_status_change_alert(route, message, verification) {
            if (verification) {
                Swal.fire({
                    title: '{{ translate('Enter order verification code') }}',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: '{{ translate('messages.Submit') }}',
                    cancelButtonText: '{{ translate('messages.cancel') }}',
                    showLoaderOnConfirm: true,
                    preConfirm: (otp) => {
                        location.href = route + '&otp=' + otp;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            } else {
                Swal.fire({
                    title: '{{ translate('messages.Are you sure?') }}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.no') }}',
                    confirmButtonText: '{{ translate('messages.Yes') }}',
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

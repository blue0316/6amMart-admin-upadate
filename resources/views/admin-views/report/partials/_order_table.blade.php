@foreach ($orders as $key => $order)
<tr class="status-{{ $order['order_status'] }} class-all">
    <td class="">
        {{ $key + 1 }}
    </td>
    <td class="table-column-pl-0">
        <a
            href="{{ route('admin.order.details', ['id' => $order['id'],'module_id'=>$order['module_id']]) }}">{{ $order['id'] }}</a>
    </td>
    <td  class="text-capitalize">
        @if($order->store)
            {{Str::limit($order->store->name,25,'...')}}
        @else
            <label class="badge badge-danger">{{ translate('messages.invalid') }}
        @endif
    </td>
    <td>
        @if ($order->customer)
            <a class="text-body text-capitalize"
                href="{{ route('admin.users.customer.view', [$order['user_id']]) }}">
                <strong>{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</strong>
            </a>
        @else
            <label class="badge badge-danger">{{ translate('messages.invalid') }}
                {{ translate('messages.customer') }}
                {{ translate('messages.data') }}</label>
        @endif
    </td>
    <td>
        <div class="text-right mw--85px">
            <div>
                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']-$order['dm_tips']-$order['total_tax_amount']-$order['delivery_charge']+$order['coupon_discount_amount'] + $order['store_discount_amount']) }}
            </div>
            @if ($order->payment_status == 'paid')
                <strong class="text-success">
                    {{ translate('messages.paid') }}
                </strong>
            @else
                <strong class="text-danger">
                    {{ translate('messages.unpaid') }}
                </strong>
            @endif
        </div>
    </td>
    <td class="text-center mw--85px">
        {{ \App\CentralLogics\Helpers::number_format_short($order->details->sum('discount_on_item')) }}
    </td>
    <td class="text-center mw--85px">
        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount']) }}
    </td>
    <td class="text-center mw--85px">
        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount'] + $order['store_discount_amount']) }}
    </td>
    <td class="text-center mw--85px white-space-nowrap">
        {{ \App\CentralLogics\Helpers::number_format_short($order['total_tax_amount']) }}
    </td>
    <td class="text-center mw--85px">
        {{ \App\CentralLogics\Helpers::number_format_short($order['delivery_charge']) }}
    </td>
    <td>
        <div class="text-right mw--85px">
            <div>
                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']) }}
            </div>
            @if ($order->payment_status == 'paid')
                <strong class="text-success">
                    {{ translate('messages.paid') }}
                </strong>
            @else
                <strong class="text-danger">
                    {{ translate('messages.unpaid') }}
                </strong>
            @endif
        </div>
    </td>
    <td class="text-center mw--85px text-capitalize">
        {{isset($order->transaction) ? $order->transaction->received_by : translate('messages.not_received_yet')}}
    </td>
    <td class="text-center mw--85px text-capitalize">
            {{ translate(str_replace('_', ' ', $order['payment_method'])) }}
    </td>
    <td class="text-center mw--85px text-capitalize">
        @if($order['order_status']=='pending')
                <span class="badge badge-soft-info">
                  {{translate('messages.pending')}}
                </span>
            @elseif($order['order_status']=='confirmed')
                <span class="badge badge-soft-info">
                  {{translate('messages.confirmed')}}
                </span>
            @elseif($order['order_status']=='processing')
                <span class="badge badge-soft-warning">
                  {{translate('messages.processing')}}
                </span>
            @elseif($order['order_status']=='picked_up')
                <span class="badge badge-soft-warning">
                  {{translate('messages.out_for_delivery')}}
                </span>
            @elseif($order['order_status']=='delivered')
                <span class="badge badge-soft-success">
                  {{translate('messages.delivered')}}
                </span>
            @elseif($order['order_status']=='failed')
                <span class="badge badge-soft-danger">
                  {{translate('messages.payment')}}  {{translate('messages.failed')}}
                </span>
            @elseif($order['order_status']=='handover')
                <span class="badge badge-soft-danger">
                  {{translate('messages.handover')}}
                </span>
            @elseif($order['order_status']=='canceled')
                <span class="badge badge-soft-danger">
                  {{translate('messages.canceled')}}
                </span>
            @elseif($order['order_status']=='accepted')
                <span class="badge badge-soft-danger">
                  {{translate('messages.accepted')}}
                </span>
            @else
                <span class="badge badge-soft-danger">
                  {{str_replace('_',' ',$order['order_status'])}}
                </span>
            @endif
           
    </td>


    <td>
        <div class="btn--container justify-content-center">
            <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn"
                href="{{ route('admin.order.details', ['id' => $order['id'],'module_id'=>$order['module_id']]) }}">
                <i class="tio-invisible"></i>
            </a>
            <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn"
                href="{{ route('admin.transactions.order.generate-invoice', ['id' => $order['id']]) }}">
                <i class="tio-print"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
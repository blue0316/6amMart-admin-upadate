@foreach ($order_transactions as $k => $ot)
<tr scope="row">
    <td>{{ $k + 1 }}</td>
    @if ($ot->order->order_type == 'parcel')
        <td><a
                href="{{ route('admin.transactions.parcel.order.details', $ot->order_id) }}">{{ $ot->order_id }}</a>
        </td>
    @else
        <td><a
                href="{{ route('admin.transactions.order.details', $ot->order_id) }}">{{ $ot->order_id }}</a>
        </td>
    @endif
    <td  class="text-capitalize">
        @if($ot->order->store)
            {{Str::limit($ot->order->store->name,25,'...')}}
        @else
            <label class="badge badge-soft-success white-space-nowrap">{{ translate('messages.parcel_order') }}
        @endif
    </td>
    <td class="white-space-nowrap">
        @if ($ot->order->customer)
            <a class="text-body text-capitalize"
                href="{{ route('admin.users.customer.view', [$ot->order['user_id']]) }}">
                <strong>{{ $ot->order->customer['f_name'] . ' ' . $ot->order->customer['l_name'] }}</strong>
            </a>
        @else
            <label class="badge badge-danger">{{ translate('messages.invalid') }}
                {{ translate('messages.customer') }}
                {{ translate('messages.data') }}</label>
        @endif
    </td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->order['order_amount'] - $ot->order['dm_tips']-$ot->order['delivery_charge'] - $ot['tax'] + $ot->order['coupon_discount_amount'] + $ot->order['store_discount_amount']) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->order->details->sum('discount_on_item')) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->order['coupon_discount_amount']) }}</td>
    <td class="white-space-nowrap">  {{ \App\CentralLogics\Helpers::number_format_short($ot->order['coupon_discount_amount'] + $ot->order['store_discount_amount']) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->tax) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->delivery_charge) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->order_amount) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->admin_expense) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->order->store_discount_amount) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency(($ot->admin_commission + $ot->admin_expense) - $ot->delivery_fee_comission) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->delivery_fee_comission) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency(($ot->admin_commission)) }}</td>
    <td class="white-space-nowrap">{{ \App\CentralLogics\Helpers::format_currency($ot->store_amount - $ot->tax) }}</td>
    @if ($ot->received_by == 'admin')
        <td class="text-capitalize white-space-nowrap">{{ translate('messages.admin') }}</td>
    @elseif ($ot->received_by == 'deliveryman')
        <td class="text-capitalize white-space-nowrap">
            <div>{{ translate('messages.delivery_man') }}</div>
            <div class="text-right mw--85px">
                @if (isset($ot->delivery_man) && $ot->delivery_man->earning == 1)
                <span class="badge badge-soft-primary">
                    {{translate('messages.freelance')}}
                </span>
                @elseif (isset($ot->delivery_man) && $ot->delivery_man->earning == 0 && $ot->delivery_man->type == 'restaurant_wise')
                <span class="badge badge-soft-warning">
                    {{translate('messages.restaurant')}}
                </span>
                @elseif (isset($ot->delivery_man) && $ot->delivery_man->earning == 0 && $ot->delivery_man->type == 'zone_wise')
                <span class="badge badge-soft-success">
                    {{translate('messages.admin')}}
                    </span>
                @endif
            </div>
        </td>
    @elseif ($ot->received_by == 'store')
        <td class="text-capitalize white-space-nowrap">{{ translate('messages.store') }}</td>
    @endif
    <td class="mw--85px text-capitalize min-w-120 ">
            {{ translate(str_replace('_', ' ', $ot->order['payment_method'])) }}
    </td>
    <td class="text-capitalize white-space-nowrap">
        @if ($ot->status)
        <span class="badge badge-soft-danger">
            {{translate('messages.refunded')}}
          </span>
        @else
        <span class="badge badge-soft-success">
            {{translate('messages.completed')}}
          </span>
        @endif
    </td>

    <td>
        <div class="btn--container justify-content-center">
            <a class="btn btn-outline-success square-btn btn-sm mr-1 action-btn"  href="{{route('admin.report.generate-statement',[$ot['id']])}}">
                <i class="tio-download-to"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
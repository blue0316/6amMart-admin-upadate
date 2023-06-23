@foreach($orders as $key=>$order)

<tr class="status-{{$order['order_status']}} class-all">
    <td class="">
        {{$key+1}}
    </td>
    <td class="table-column-pl-0">
        <a href="{{route('admin.transactions.order.details',['id' => $order['id'],'module_id'=>$order['module_id']])}}">{{$order['id']}}</a>
    </td>
    <td>
        <div>
            <div>
                {{date('d M Y',strtotime($order['created_at']))}}
            </div>
            <div class="d-block text-uppercase">
                {{date(config('timeformat'),strtotime($order['created_at']))}}
            </div>
        </div>
    </td>
    <td>
        @if($order->customer)
            <a class="text-body text-capitalize" href="{{route('admin.transactions.customer.view',[$order['user_id']])}}">
                <strong>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong>
                <div>{{$order->customer['phone']}}</div>
            </a>
        @else
            <label class="badge badge-danger">{{translate('messages.invalid')}} {{translate('messages.customer')}} {{translate('messages.data')}}</label>
        @endif
    </td>
    <td>
        <div class="text-right mw--85px">
            <div>
                {{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}
            </div>
            @if($order->payment_status=='paid')
            <strong class="text-success">
                {{translate('messages.paid')}}
            </strong>
            @else
            <strong class="text-danger">
                {{translate('messages.unpaid')}}
            </strong>
            @endif
        </div>
    </td>
    <td class="text-center mw--85px">
        {{\App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount']+$order['store_discount_amount'])}}
    </td>
    <td class="text-center mw--85px">
        {{\App\CentralLogics\Helpers::format_currency($order['total_tax_amount'])}}
    </td>
    <td class="text-center mw--85px">
        {{\App\CentralLogics\Helpers::format_currency($order['original_delivery_charge'])}}
    </td>

    <td>
        <div class="btn--container justify-content-center">
            <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn" href="{{route('admin.transactions.order.details',['id' => $order['id'],'module_id'=>$order['module_id']])}}">
                <i class="tio-invisible"></i>
            </a>
            <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn" href="{{route('admin.transactions.order.generate-invoice',['id'=>$order['id']])}}">
                <i class="tio-print"></i>
            </a>
        </div>
    </td>
</tr>

@endforeach

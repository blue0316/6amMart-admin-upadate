@foreach($orders as $key=>$order)

<tr class="status-{{$order['order_status']}} class-all">
    <td class="">
        {{$key+ 1}}
    </td>
    <td class="table-column-pl-0">
        <a href="{{route('admin.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
    </td>
    <td>
        <div>
            {{date('d M Y',strtotime($order['created_at']))}}
        </div>
        <div class="d-block text-uppercase">
            {{date(config('timeformat'),strtotime($order['created_at']))}}
        </div>
    </td>
    <td>
        @if($order->customer)
        <div>
            <a class="text-body text-capitalize"
            href="{{route('admin.customer.view',[$order['user_id']])}}">
                <div>
                    {{$order->customer['f_name'].' '.$order->customer['l_name']}}
                </div>
                <div>
                    {{$order->customer['phone']}}
                </div>
            </a>
        </div>
        @else
            <label class="badge badge-danger">{{translate('messages.invalid')}} {{translate('messages.customer')}} {{translate('messages.data')}}</label>
        @endif
    </td>
    <td>
        @if($order->payment_status=='paid')
            <span class="badge badge-soft-success">
            {{translate('messages.paid')}}
            </span>
        @else
            <span class="badge badge-soft-danger">
            {{translate('messages.unpaid')}}
            </span>
        @endif
    </td>
    <td>{{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}</td>
    <td class="text-capitalize text-center">
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
        @elseif($order['order_status']=='out_for_delivery')
            <span class="badge badge-soft-warning">
            {{translate('messages.out_for_delivery')}}
            </span>
        @elseif($order['order_status']=='delivered')
            <span class="badge badge-soft-success">
            {{translate('messages.delivered')}}
            </span>
        @else
            <span class="badge badge-soft-danger">
            {{str_replace('_',' ',$order['order_status'])}}
            </span>
        @endif
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--warning btn-outline-warning" href="{{route('admin.order.details',['id'=>$order['id']])}}"><i class="tio-visible"></i></a>
            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.order.generate-invoice',['id'=>$order['id']])}}"><i class="tio-print"></i></a>
        </div>
    </td>
</tr>

@endforeach

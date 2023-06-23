@foreach ($stores as $k => $store)
@php($delivered = $store->orders->where('order_status', 'delivered')->count())
@php($canceled = $store->orders->where('order_status', 'canceled')->count())
@php($refunded = $store->orders->where('order_status', 'refunded')->count())
@php($refund_requested = $store->orders->whereNotNull('refund_requested')->count())
<tr>
    <td>{{$k+1}}</td>
    <td>
        <a href="{{route('admin.store.view', [$store->id, 'module_id'=>$store->module_id])}}">{{ $store->name }}</a>
    </td>
    <td class="text-center">
        {{ $store->orders->count() }}
    </td>
    <td class="text-center">
        {{ $delivered }}
    </td>
    <td class="text-center">
        {{\App\CentralLogics\Helpers::number_format_short($store->orders->where('order_status','delivered')->sum('order_amount'))}}
    </td>
    <td class="text-center">
        {{ ($store->orders->count() > 0 && $delivered > 0)? number_format((100*$delivered)/$store->orders->count(), config('round_up_to_digit')): 0 }}%
    </td>
    <td class="text-center">
        {{ ($store->orders->count() > 0 && $delivered > 0)? number_format((100*($store->orders->count()-($delivered+$canceled)))/$store->orders->count(), config('round_up_to_digit')): 0 }}%
    </td>
    <td class="text-center">
        {{ ($store->orders->count() > 0 && $canceled > 0)? number_format((100*$canceled)/$store->orders->count(), config('round_up_to_digit')): 0 }}%
    </td>
    <td class="text-center">
        {{ $refunded }} <small>({{ $refund_requested }} pending)</small>
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a href="{{route('admin.store.view', [$store->id, 'module_id'=>$store->module_id])}}" class="action-btn btn--primary btn-outline-primary">
                <i class="tio-invisible"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
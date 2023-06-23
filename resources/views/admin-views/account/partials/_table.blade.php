@foreach($account_transaction as $k=>$at)
<tr>
    <td scope="row">{{$k+1}}</td>
    <td>
        @if($at->store)
        <a href="{{route('admin.store.view',[$at->store['id'],'module_id'=>$at->store['module_id']])}}">{{ Str::limit($at->store->name, 20, '...') }}</a>
        @elseif($at->deliveryman)
        <a href="{{route('admin.users.delivery-man.preview',[$at->deliveryman->id])}}">{{ $at->deliveryman->f_name }} {{ $at->deliveryman->l_name }}</a>
        @else
            {{translate('messages.not_found')}}
        @endif
    </td>
    <td><label class="text-uppercase">{{$at['from_type']}}</label></td>
    <td>{{$at->created_at->format('Y-m-d '.config('timeformat'))}}</td>
    <td><div class="pl-4">
        {{$at['amount']}}
    </div></td>
    <td><div class="pl-4">
        {{$at['ref']}}
    </div></td>
    <td>
        <div class="btn--container justify-content-center">
            <a href="{{route('admin.transactions.account-transaction.view',[$at['id']])}}"
            class="btn action-btn btn--warning btn-outline-warning"><i class="tio-visible"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach

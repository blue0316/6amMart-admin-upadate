@foreach($withdraw_req as $k=>$wr)
<tr>
    <td scope="row">{{$k+1}}</td>
    <td>{{$wr['amount']}}</td>
    {{-- <td>{{$wr->__action_note}}</td> --}}
    <td>
        @if($wr->vendor)
        <a class="deco-none"
            href="{{route('admin.store.view',[$wr->vendor['id'],'module_id'=>$wr->vendor->stores[0]->module_id])}}">{{ Str::limit($wr->vendor->stores[0]->name, 20, '...') }}</a>
        @else
        {{translate('messages.store deleted!') }}
        @endif
    </td>
    <td>{{date('Y-m-d '.config('timeformat'),strtotime($wr->created_at))}}</td>
    <td>
        @if($wr->approved==0)
            <label class="badge badge-primary">{{ translate('messages.pending') }}</label>
        @elseif($wr->approved==1)
            <label class="badge badge-success">{{ translate('messages.approved') }}</label>
        @else
            <label class="badge badge-danger">{{ translate('messages.denied') }}</label>
        @endif
    </td>
    <td>
        @if($wr->vendor)
        <a href="{{route('admin.transactions.store.withdraw_view',[$wr['id'],$wr->vendor['id']])}}"
            class="btn action-btn btn--warning btn-outline-warning"><i class="tio-visible-outlined"></i>
        </a>
        @else
        {{translate('messages.store').' '.translate('messages.deleted') }}
        @endif
        {{--<a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
        onclick="form_alert('withdraw-{{$wr['id']}}','Want to delete this  ?')">{{translate('messages.Delete')}}</a>
        <form action="{{route('vendor.withdraw.close',[$wr['id']])}}"
                method="post" id="withdraw-{{$wr['id']}}">
            @csrf @method('delete')
        </form>--}}

    </td>
</tr>
@endforeach

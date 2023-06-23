@foreach($provide_dm_earning as $k=>$at)
<tr>
    <td scope="row">{{$k+1}}</td>
    <td>@if($at->delivery_man)<a href="{{route('admin.users.delivery-man.preview', $at->delivery_man_id)}}">{{$at->delivery_man->f_name.' '.$at->delivery_man->l_name}}</a> @else <label class="text-capitalize text-danger">{{translate('messages.deliveryman')}} {{translate('messages.deleted')}}</label> @endif </td>
    <td>{{$at->created_at->format('Y-m-d '.config('timeformat'))}}</td>
    <td>{{$at['amount']}}</td>
    <td>{{$at['method']}}</td>
    <td>{{$at['ref']}}</td>
</tr>
@endforeach

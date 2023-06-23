@foreach($digital_transaction as $k=>$dt)
<tr>
    <td scope="row">{{$k+1}}</td>
    <td><a href="{{route('admin.order.details',$dt->order_id)}}">{{$dt->order_id}}</a></td>
    <td>{{$dt->original_delivery_charge}}</td>
    <td>{{$dt->created_at->format('Y-m-d')}}</td>
</tr>
@endforeach

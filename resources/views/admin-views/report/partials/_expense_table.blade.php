@foreach ($expense as $key => $exp)
<tr>
    <td scope="row">{{$key+1}}</td>
    <td><label class="text-uppercase">{{translate("messages.{$exp['type']}")}}</label></td>
    <td>
        <div>
            {{$exp['amount']}}
        </div>
    </td>
    <td>
        <div>
            {{$exp['order_id']}}
        </div>
    </td>
    {{-- <td>
        <div>
            {{$exp['description']}}
        </div>
    </td> --}}
    <td>
        {{$exp->created_at->format('Y-m-d '.config('timeformat'))}}
    </td>
</tr>
@endforeach
@foreach($employees as $k=>$e)
<tr>
    <td scope="row">{{$k+1}}</td>
    <td class="text-capitalize text-break">{{$e['f_name']}} {{$e['l_name']}}</td>
    <td >
        {{$e['email']}}
    </td>
    <td>{{$e['phone']}}</td>
    <td>{{$e->role?$e->role['name']:translate('messages.role_deleted')}}</td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary"
                href="{{route('vendor.employee.edit',[$e['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.Employee')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                onclick="form_alert('employee-{{$e['id']}}','{{translate('messages.Want_to_delete_this_role')}}')" title="{{translate('messages.delete')}} {{translate('messages.Employee')}}"><i class="tio-delete-outlined"></i>
            </a>
        </div>
        <form action="{{route('vendor.employee.delete',[$e['id']])}}"
                method="post" id="employee-{{$e['id']}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach
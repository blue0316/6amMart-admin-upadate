@foreach($rl as $k=>$r)
    <tr>
        <td scope="row">{{$k+1}}</td>
        <td>{{$r['name']}}</td>
        <td class="text-capitalize">
            @if($r['modules']!=null)
                @foreach((array)json_decode($r['modules']) as $key=>$m)
                    {{str_replace('_',' ',$m)}},
                @endforeach
            @endif
        </td>
        <td>{{date('d-M-y',strtotime($r['created_at']))}}</td>
        {{--<td>
            {{$r->status?'Active':'Inactive'}}
        </td>--}}
        <td>
            <div class="btn--container justify-content-center">
                <a class="btn action-btn btn--primary btn-outline-primary"
                    href="{{route('admin.users.custom-role.edit',[$r['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.role')}}"><i class="tio-edit"></i>
                </a>
                <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                    onclick="form_alert('role-{{$r['id']}}','{{translate('messages.Want_to_delete_this_role')}}')" title="{{translate('messages.delete')}} {{translate('messages.role')}}"><i class="tio-delete-outlined"></i>
                </a>
            </div>
            <form action="{{route('admin.users.custom-role.delete',[$r['id']])}}"
                    method="post" id="role-{{$r['id']}}">
                @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach
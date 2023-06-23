@foreach($attributes as $key=>$attribute)
    <tr>
        <td class="text-center">
            <span class="mr-3">
                {{$key+1}}
            </span>
        </td>
        <td class="text-center">
            <span class="font-size-sm text-body mr-3">
                {{Str::limit($attribute['name'],20,'...')}}
            </span>
        </td>
        <td>
            <div class="btn--container justify-content-center">
                <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.attribute.edit',[$attribute['id']])}}" title="{{translate('messages.edit')}}"><i class="tio-edit"></i>
                </a>
                <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('attribute-{{$attribute['id']}}','Want to delete this attribute ?')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                </a>
                <form action="{{route('admin.attribute.delete',[$attribute['id']])}}"
                        method="post" id="attribute-{{$attribute['id']}}">
                    @csrf @method('delete')
                </form>
            </div>
        </td>
    </tr>
@endforeach
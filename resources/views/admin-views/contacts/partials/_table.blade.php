@foreach($contacts as $key=>$contact)
<tr>
    <td class="text-center">
        <span class="mr-3">
            {{$key+1}}
        </span>
    </td>
    <td class="text-center">
        <span class="font-size-sm text-body mr-3">
            {{Str::limit($contact['name'],20,'...')}}
        </span>
    </td>
    <td class="text-center">
        <span class="font-size-sm text-body mr-3">
            {{$contact['email']}}
        </span>
    </td>
    <td class="text-center">
        <div class="font-size-sm text-body mr-3 white--space-initial max-w-180px mx-auto">
            {{Str::limit($contact['subject'],40,'...')}}
        </div>
    </td>
    <td class="text-center">
        <span class="font-size-sm text-body mr-3">
            @if($contact->seen==1)
            <label class="badge badge-soft-success mb-0">{{translate('messages.Seen')}}</label>
        @else
            <label class="badge badge-soft-info mb-0">{{translate('messages.Not_Seen_Yet')}}</label>
        @endif
        </span>
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.users.contact.contact-view',[$contact['id']])}}" title="{{translate('messages.edit')}}"><i class="tio-invisible"></i>
            </a>
            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('contact-{{$contact['id']}}','{{ translate('messages.Want to delete this message?') }}')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.users.contact.contact-delete',[$contact['id']])}}"
                    method="post" id="contact-{{$contact['id']}}">
                @csrf @method('delete')
            </form>
        </div>
    </td>
</tr>
@endforeach
